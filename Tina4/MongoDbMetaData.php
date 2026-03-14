<?php
/**
 * Tina4 - This is not a 4ramework.
 * Copy-right 2007 - current Tina4
 * License: MIT https://opensource.org/licenses/MIT
 */

namespace Tina4;

/**
 * MongoDB metadata trait
 * Handles getDatabase and tableExists operations
 * @package Tina4
 */
trait MongoDbMetaData
{
    /**
     * Check if a collection (table) exists
     * @param string $tableName
     * @return bool
     */
    final public function tableExists(string $tableName): bool
    {
        try {
            $collections = [];
            foreach ($this->dbh->listCollectionNames() as $name) {
                $collections[] = $name;
            }
            return in_array($tableName, $collections);
        } catch (\Exception $e) {
            $this->lastError = new DataError("TABLE_CHECK_ERROR", $e->getMessage());
            return false;
        }
    }

    /**
     * Determines the database layout in the form table -> columns
     * @return array
     */
    final public function getDatabase(): array
    {
        $result = [];

        try {
            foreach ($this->dbh->listCollectionNames() as $collectionName) {
                $tableEntry = [
                    "tableName" => $collectionName,
                    "fields" => []
                ];

                // Sample a document to discover field structure
                $sample = $this->dbh->{$collectionName}->findOne();
                if ($sample !== null) {
                    $docArray = (array)$sample;
                    foreach ($docArray as $fieldName => $fieldValue) {
                        $tableEntry["fields"][] = [
                            "fieldName" => $fieldName,
                            "fieldType" => gettype($fieldValue),
                            "fieldDefault" => null,
                            "isNotNull" => false,
                            "isPrimaryKey" => ($fieldName === "_id")
                        ];
                    }
                }

                $result[] = $tableEntry;
            }
        } catch (\Exception $e) {
            $this->lastError = new DataError("METADATA_ERROR", $e->getMessage());
        }

        return $result;
    }
}
