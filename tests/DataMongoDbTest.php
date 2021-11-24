<?php

use PHPUnit\Framework\TestCase;

require_once "./Tina4/DataMongoDb.php";

class DataMongoDbTest extends TestCase
{
    private $DBA;

    function setUp() :void
    {
        $this->DBA = new \Tina4\DataMongoDb("localhost/27017:testing");
    }



}