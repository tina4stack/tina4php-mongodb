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
     * Delegates to the canonical implementation in \Tina4\Utilities (tina4php-core)
     * @param string $name A field name or object name with underscores
     * @return string Camel case version of the input
     * @see \Tina4\Utility::camelCase()
     */
    public function camelCase(string $name): string
    {
        if (class_exists('\Tina4\Utilities')) {
            return (new \Tina4\Utilities())->camelCase($name);
        }

        // Fallback for standalone usage without tina4php-core
        if (strpos($name, '_')) {
            $name = str_replace('_', ' ', strtolower($name));
        }

        return lcfirst(str_replace(' ', '', ucwords($name)));
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
