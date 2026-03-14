<?php
/**
 * Tina4 - This is not a 4ramework.
 * Copy-right 2007 - current Tina4
 * License: MIT https://opensource.org/licenses/MIT
 */

namespace Tina4;

/**
 * MongoDB query trait
 * Handles fetching records from MongoDB collections
 * @package Tina4
 */
trait MongoDbQuery
{
    /**
     * Native fetch for MongoDB
     * @param string $sql SQL-like query string
     * @param int $noOfRecords Number of records to return
     * @param int $offSet Record offset
     * @param array $fieldMapping Mapped fields
     * @return DataResult
     */
    final public function fetch($sql = "", int $noOfRecords = 10, int $offSet = 0, array $fieldMapping = []): DataResult
    {
        $statement = (new NoSQLParser())->parseSQLToNoSQL($sql);
        $collectionName = $statement["collectionName"] ?? "";

        if (empty($collectionName)) {
            return new DataResult([], [], 0, $offSet, new DataError("PARSE_ERROR", "Could not parse query: {$sql}"));
        }

        try {
            $filter = $statement["filter"] ?? [];

            // Build options for limit/offset
            $options = [];
            if ($noOfRecords > 0) {
                $options["limit"] = $noOfRecords;
            }
            if ($offSet > 0) {
                $options["skip"] = $offSet;
            }

            // Get total count for pagination
            $totalCount = $this->dbh->{$collectionName}->countDocuments($filter);

            // Fetch documents
            $collection = $this->dbh->{$collectionName}->find($filter, $options);

            $records = [];
            $fields = [];
            $countRecords = 0;

            foreach ($collection as $document) {
                if (!empty($document)) {
                    $docArray = (array)$document;

                    // Build field metadata from first document
                    if (empty($fields) && !empty($docArray)) {
                        foreach ($docArray as $fieldName => $fieldValue) {
                            $fields[] = (object)[
                                "name" => $fieldName,
                                "alias" => $this->camelCase($fieldName),
                                "type" => gettype($fieldValue),
                                "default" => null
                            ];
                        }
                    }

                    $records[] = new DataRecord($docArray, $fieldMapping, $this->getDefaultDatabaseDateFormat(), $this->dateFormat);
                    $countRecords++;
                }
            }

            $this->lastError = new DataError("", "");

            return new DataResult($records, $fields, $totalCount, $offSet, $this->lastError);

        } catch (\Exception $e) {
            $this->lastError = new DataError("FETCH_ERROR", $e->getMessage());
            return new DataResult([], [], 0, $offSet, $this->lastError);
        }
    }
}
