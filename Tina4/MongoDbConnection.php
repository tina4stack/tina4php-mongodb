<?php
/**
 * Tina4 - This is not a 4ramework.
 * Copy-right 2007 - current Tina4
 * License: MIT https://opensource.org/licenses/MIT
 */

namespace Tina4;

/**
 * MongoDB connection trait
 * Handles open, close, error, and transaction methods
 * @package Tina4
 */
trait MongoDbConnection
{
    /**
     * Opens a MongoDB connection
     * @return void
     * @throws \Exception
     */
    public function open()
    {
        if (!class_exists("\MongoDB\Client")) {
            throw new \Exception("MongoDB extension for PHP needs to be installed. Run: composer require mongodb/mongodb");
        }

        if (empty($this->hostName)) {
            $this->hostName = "localhost";
        }

        if (empty($this->port)) {
            $this->port = $this->getDefaultDatabasePort();
        }

        if (empty($this->databaseName)) {
            $this->databaseName = "testing";
        }

        $this->lastError = new DataError("", "");

        // Build connection string from parsed components
        if (!empty($this->connectionString) && strpos($this->connectionString, "mongodb") !== false) {
            // Full MongoDB connection string provided directly
            $connectionString = $this->connectionString;
        } else {
            // Build from host/port/credentials
            $connectionString = "mongodb://";
            if (!empty($this->username) && !empty($this->password)) {
                $connectionString .= urlencode($this->username) . ":" . urlencode($this->password) . "@";
            }
            $connectionString .= $this->hostName . ":" . $this->port;
        }

        try {
            $this->dbh = (new \MongoDB\Client($connectionString))->{$this->databaseName};
        } catch (\Exception $e) {
            $this->lastError = new DataError("CONNECTION_FAILED", $e->getMessage());
            throw $e;
        }
    }

    /**
     * Closes the MongoDB connection
     * @return void
     */
    public function close()
    {
        unset($this->dbh);
    }

    /**
     * Returns the last error from the database
     * @return DataError
     */
    final public function error()
    {
        return $this->lastError ?? new DataError("", "");
    }

    /**
     * Auto commit — MongoDB does not use autocommit in the traditional sense
     * @param bool $onState
     * @return void
     */
    final public function autoCommit(bool $onState = true): void
    {
        // MongoDB does not use autocommit in the traditional sense
    }

    /**
     * Start transaction — MongoDB transactions require replica sets
     * @return void
     */
    final public function startTransaction()
    {
        // MongoDB transactions require replica sets — not supported in standalone mode
        // For replica set deployments, this could be extended with session-based transactions
    }

    /**
     * Commit — MongoDB transactions require replica sets
     * @param null $transactionId
     * @return void
     */
    final public function commit($transactionId = null)
    {
        // MongoDB transactions require replica sets
    }

    /**
     * Rollback — MongoDB transactions require replica sets
     * @param null $transactionId
     * @return void
     */
    final public function rollback($transactionId = null)
    {
        // MongoDB transactions require replica sets
    }
}
