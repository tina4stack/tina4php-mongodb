<?php
/**
 * Tina4 - This is not a 4ramework.
 * Copy-right 2007 - current Tina4
 * License: MIT https://opensource.org/licenses/MIT
 */

use PHPUnit\Framework\TestCase;

class DataMongoDbTest extends TestCase
{
    /**
     * Test camelCase conversion
     */
    public function testCamelCaseSimple(): void
    {
        $db = $this->createPartialMock(\Tina4\DataMongoDb::class, []);
        $this->assertEquals("firstName", $db->camelCase("first_name"));
    }

    public function testCamelCaseMultipleUnderscores(): void
    {
        $db = $this->createPartialMock(\Tina4\DataMongoDb::class, []);
        $this->assertEquals("myFirstName", $db->camelCase("my_first_name"));
    }

    public function testCamelCaseNoUnderscore(): void
    {
        $db = $this->createPartialMock(\Tina4\DataMongoDb::class, []);
        $this->assertEquals("name", $db->camelCase("name"));
    }

    public function testCamelCaseUpperInput(): void
    {
        $db = $this->createPartialMock(\Tina4\DataMongoDb::class, []);
        $this->assertEquals("firstName", $db->camelCase("FIRST_NAME"));
    }

    public function testCamelCaseTrailingUnderscore(): void
    {
        $db = $this->createPartialMock(\Tina4\DataMongoDb::class, []);
        $this->assertEquals("name", $db->camelCase("name_"));
    }

    public function testCamelCaseEmpty(): void
    {
        $db = $this->createPartialMock(\Tina4\DataMongoDb::class, []);
        $this->assertEquals("", $db->camelCase(""));
    }

    public function testCamelCaseSingleChar(): void
    {
        $db = $this->createPartialMock(\Tina4\DataMongoDb::class, []);
        $this->assertEquals("a", $db->camelCase("a"));
    }

    /**
     * Test default values
     */
    public function testGetDefaultDatabasePort(): void
    {
        $db = $this->createPartialMock(\Tina4\DataMongoDb::class, []);
        $this->assertEquals(27017, $db->getDefaultDatabasePort());
    }

    public function testGetDefaultDatabaseDateFormat(): void
    {
        $db = $this->createPartialMock(\Tina4\DataMongoDb::class, []);
        $this->assertEquals("Y-m-d", $db->getDefaultDatabaseDateFormat());
    }

    public function testIsNoSQL(): void
    {
        $db = $this->createPartialMock(\Tina4\DataMongoDb::class, []);
        $this->assertTrue($db->isNoSQL());
    }

    public function testGetShortName(): void
    {
        $db = $this->createPartialMock(\Tina4\DataMongoDb::class, []);
        $this->assertEquals("mongodb", $db->getShortName());
    }

    public function testGetQueryParam(): void
    {
        $db = $this->createPartialMock(\Tina4\DataMongoDb::class, []);
        $this->assertEquals(":fieldName", $db->getQueryParam("fieldName", 0));
        $this->assertEquals(":id", $db->getQueryParam("id", 1));
    }

    /**
     * Test NoSQLParser — SELECT queries
     */
    public function testNoSQLParserSelectPlain(): void
    {
        $parser = new \Tina4\NoSQLParser();
        $result = $parser->parseSQLToNoSQL("select id, name from users");
        $this->assertEquals("users", $result["collectionName"]);
        $this->assertEquals(["id", "name"], $result["columns"]);
        $this->assertEmpty($result["filter"]);
    }

    public function testNoSQLParserSelectWithWhere(): void
    {
        $parser = new \Tina4\NoSQLParser();
        $result = $parser->parseSQLToNoSQL("select id, name from users where id = 1");
        $this->assertEquals("users", $result["collectionName"]);
        $this->assertArrayHasKey("id", $result["filter"]);
        $this->assertArrayHasKey('$eq', $result["filter"]["id"]);
    }

    public function testNoSQLParserSelectComparisonOperators(): void
    {
        $parser = new \Tina4\NoSQLParser();
        $result = $parser->parseSQLToNoSQL("select id from users where id > 5");
        $this->assertEquals("users", $result["collectionName"]);
        $this->assertArrayHasKey("id", $result["filter"]);
        $this->assertArrayHasKey('$gt', $result["filter"]["id"]);
    }

    /**
     * Test that DataMongoDb implements the DataBase interface
     */
    public function testImplementsDataBaseInterface(): void
    {
        $reflection = new \ReflectionClass(\Tina4\DataMongoDb::class);
        $this->assertTrue($reflection->implementsInterface(\Tina4\DataBase::class));
    }
}
