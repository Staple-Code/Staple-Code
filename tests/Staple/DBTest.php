<?php

namespace Staple\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use Staple\DB;

class DBTest extends TestCase
{
    /**
     * Test that the getLastQuery method returns the last executed query.
     * @throws Exception
     */
    public function testGetLastQueryReturnsLastExecutedQuery()
    {
        $db = $this->getMockBuilder(DB::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get','query','connect','__destruct'])
            ->getMock();

        $testQuery = "SELECT * FROM users;";
        $db->last_query = $testQuery;

        $this->assertSame($testQuery, $db->getLastQuery());
    }
}