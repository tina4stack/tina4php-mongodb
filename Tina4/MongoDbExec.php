<?php
/**
 * Tina4 - This is not a 4ramework.
 * Copy-right 2007 - current Tina4
 * License: MIT https://opensource.org/licenses/MIT
 */

namespace Tina4;

/**
 * MongoDB exec trait
 * Handles executing insert, update, delete, and create operations
 * @package Tina4
 */
trait MongoDbExec
{
    /**
     * Executes a SQL-like statement against MongoDB
     * @return DataError
     */
    public function exec()
    {
        $params = $this->parseParams(func_get_args());
        $params = $params["params"];
        $sql = $params[0];

        $statement = (new NoSQLParser())->parseSQLToNoSQL($sql);

        $collectionName = $statement["collectionName"] ?? "";

        if (empty($collectionName)) {
            $this->lastError = new DataError("PARSE_ERROR", "Could not parse collection name from query: {$sql}");
            return $this->lastError;
        }

        try {
            // Handle CREATE TABLE (collection creation — MongoDB creates on first insert, so this is a no-op)
            if (stripos($sql, "create table") !== false) {
                // MongoDB creates collections implicitly on first insert
                $this->lastError = new DataError("", "");
                return $this->lastError;
            }

            // Handle DELETE
            if (stripos($sql, "delete") !== false) {
                $filter = $statement["filter"] ?? [];
                // Replace parameterized placeholders with actual values
                $filter = $this->bindFilterParams($filter, $params);
                $result = $this->dbh->{$collectionName}->deleteMany($filter);
                $this->lastError = new DataError("", "");
                return $this->lastError;
            }

            // Build data from columns + params
            if (empty($statement["data"])) {
                $data = [];
                foreach ($statement["columns"] as $id => $column) {
                    if (isset($params[$id + 1])) {
                        $data[$this->camelCase($column)] = $params[$id + 1];
                    }
                }
            } else {
                $data = [];
                foreach ($statement["columns"] as $id => $column) {
                    $data[$this->camelCase($column)] = $statement["data"][$id] ?? null;
                }
            }

            // Handle UPDATE
            if (stripos($sql, "update") !== false) {
                $filter = $statement["filter"] ?? [];
                $filter = $this->bindFilterParams($filter, $params);
                $result = $this->dbh->{$collectionName}->updateMany($filter, ['$set' => $data]);
                $this->lastError = new DataError("", "");
                return $this->lastError;
            }

            // Handle INSERT
            $result = $this->dbh->{$collectionName}->insertOne($data);
            $this->lastId = (string)$result->getInsertedId();
            $this->lastError = new DataError("", "");
            return $this->lastError;

        } catch (\Exception $e) {
            $this->lastError = new DataError("EXEC_ERROR", $e->getMessage());
            return $this->lastError;
        }
    }

    /**
     * Replace parameterized filter placeholders with actual parameter values
     * @param array $filter
     * @param array $params
     * @return array
     */
    private function bindFilterParams(array $filter, array $params): array
    {
        // For now, return filter as-is — NoSQLParser produces literal values in the filter
        // Parameterized binding for MongoDB filters can be extended here
        return $filter;
    }
}
