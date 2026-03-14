<?php
/**
 * Tina4 - This is not a 4ramework.
 * Copy-right 2007 - current Tina4
 * License: MIT https://opensource.org/licenses/MIT
 */

namespace Tina4;

/**
 * The MongoDB database implementation
 * @package Tina4
 */
class DataMongoDb implements DataBase
{
    use DataBaseCore;
    use MongoDbConnection;
    use MongoDbExec;
    use MongoDbMetaData;
    use MongoDbQuery;

    /**
     * @var string Last inserted ID
     */
    private string $lastId = "";

    /**
     * @var DataError Last error from database operations
     */
    private DataError $lastError;

    /**
     * Returns the last id after an insert
     * @return string
     */
    final public function getLastId(): string
    {
        return $this->lastId;
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
     * The default date format for this database type
     * @return string
     */
    final public function getDefaultDatabaseDateFormat(): string
    {
        return "Y-m-d";
    }

    /**
     * The default MongoDB port
     * @return int
     */
    final public function getDefaultDatabasePort(): int
    {
        return 27017;
    }

    /**
     * Gets the query parameter format
     * @param string $fieldName
     * @param int $fieldIndex
     * @return string
     */
    final public function getQueryParam(string $fieldName, int $fieldIndex): string
    {
        return ":{$fieldName}";
    }

    /**
     * Get a short name for the database used for specific database migrations
     * @return string
     */
    final public function getShortName(): string
    {
        return "mongodb";
    }

    /**
     * Is it a No SQL database?
     * @return bool
     */
    final public function isNoSQL(): bool
    {
        return true;
    }
}
