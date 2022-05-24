<?php
/**
 * Tina4 - This is not a 4ramework.
 * Copy-right 2007 - current Tina4
 * License: MIT https://opensource.org/licenses/MIT
 */

namespace Tina4;

/**
 * The Mongodb database implementation
 * @package Tina4
 */
class DataMongoDb implements Database
{
    use DataBaseCore;

    private $manager;

    public function open()
    {
        if (!class_exists("MongoDB\Client")) {
            throw new \Exception("MongoDb extension for PHP needs to be installed");
        }

        if (empty($this->hostName))
        {
            $this->hostName = "localhost";
        }

        if (empty($this->port))
        {
            $this->port = $this->getDefaultDatabasePort();
        }

        if (empty($this->databaseName))
        {
            $this->databaseName = "testing";
        }

        $connectionString = "mongodb://" . $this->hostName . ":" . $this->port;
        if (!empty($this->username)) {
            $connectionString = "mongodb://{$this->username}:{$this->password}@" . $this->hostName . ":" . $this->port;
        }

        $this->dbh = (new \MongoDB\Client($connectionString))->{$this->databaseName};

        $this->manager = new \MongoDB\Driver\Manager($connectionString);
    }

    public function close()
    {
        unset($this->dbh);
    }

    public function exec()
    {
        $params = $this->parseParams(func_get_args());
        $params = $params["params"];
        $sql = $params[0];
        $statement = (new NoSQLParser())->parseSQLToNoSQL($sql);

        $data = [];
        foreach ($statement["columns"] as $id => $column) {
            $data[$this->camelCase($column)] = $params[$id+1];
        }

        if (strpos($sql, "update") !== false) {
            foreach ($this->dbh->{$statement["collectionName"]}->find($statement["filter"]) as $document) {
                $this->dbh->{$statement["collectionName"]}->findOneAndUpdate($statement["filter"], ['$set' => $data]);
            }
        } else {
            $this->dbh->{$statement["collectionName"]}->insertOne($data);
        }

        return $this->error();
    }

    final public function getLastId(): string
    {
       return 0;
    }

    final public function tableExists(string $tableName): bool
    {
        $collections = new \MongoDB\Command\ListCollections($this->databaseName);

        $collectionNames = [];

        foreach ($collections as $collection) {
            $collectionNames[] = $collection->getName();
        }

        return in_array($tableName, $collectionNames);
    }

    final public function fetch($sql = "", int $noOfRecords = 10, int $offSet = 0, array $fieldMapping = []): DataResult
    {
        $statement = (new NoSQLParser())->parseSQLToNoSQL($sql);

        $collection = $this->dbh->{$statement["collectionName"]}->find($statement["filter"]);

        $countRecords = 0;

        $records = [];

        if (!empty($collection)) {
            foreach ($collection as  $document) {
                if (!empty($document)) {

                    $records[] = (new DataRecord((array)$document, $fieldMapping, $this->getDefaultDatabaseDateFormat(), $this->dateFormat));
                    $countRecords++;
                }
            }
        }

        $error = $this->error();

        return (new DataResult($records, $fields=[], $countRecords, $offSet, $error));
    }

    final public function autoCommit(bool $onState = true): void
    {
        // TODO: Implement autoCommit() method.
    }

    final public function startTransaction()
    {
        // TODO: Implement startTransaction() method.
    }

    final public function error()
    {
        return (new DataError("", ""));
    }

    final public function getDatabase(): array
    {
        // TODO: Implement getDatabase() method.
    }

    final public function getDefaultDatabaseDateFormat(): string
    {
        return "Y-m-d";
    }

    final public function getDefaultDatabasePort(): int
    {
        return 27017;
    }

    final public function getQueryParam(string $fieldName, int $fieldIndex): string
    {
        return ":{$fieldName}";
    }

    final public function commit($transactionId = null)
    {
        // TODO: Implement commit() method.
    }


    final public function rollback($transactionId = null)
    {
        // TODO: Implement rollback() method.
    }

    /**
     * Is it a No SQL database?
     * @return bool
     */
    final public function isNoSQL(): bool
    {
        return true;
    }

    /**
     * Return a camel cased version of the name
     * @param string $name A field name or object name with underscores
     * @return string Camel case version of the input
     */
    public function camelCase(string $name): string
    {
        $fieldName = "";
        $name = strtolower($name);
        for ($i = 0, $iMax = strlen($name); $i < $iMax; $i++) {
            if ($name[$i] === "_") {
                $i++;
                if ($i < strlen($name)) {
                    $fieldName .= strtoupper($name[$i]);
                }
            } else {
                $fieldName .= $name[$i];
            }
        }
        return $fieldName;
    }

    /**
     * Get a short name for the database used for specific database migrations
     * @return string
     */
    final public function getShortName(): string
    {
        return "mongodb";
    }
}
