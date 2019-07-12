<?php
/**
 * Created by PhpStorm.
 * User: scott.henscheid
 * Date: 5/11/2017
 * Time: 12:12 PM
 */

namespace Staple\Tests;

use Staple\Exception\QueryException;
use PHPUnit\Framework\TestCase;
use Staple\Query\MockConnection;
use Staple\Query\Query;

class DeleteTest extends TestCase
{
	const TABLE_NAME = 'customers';

	private function getMockConnection($driver = MockConnection::DRIVER_MYSQL)
	{
		return new MockConnection($driver);
	}

	public function testQueryWithSchema()
	{
		//Setup
		$connMySQL = $this->getMockConnection(MockConnection::DRIVER_MYSQL);
		$connSqlSrv = $this->getMockConnection(MockConnection::DRIVER_SQLSRV);

		//Act
		//Should throw for MySQL
		try
		{
			Query::delete(self::TABLE_NAME, $connMySQL)
				->setSchema('test');
			$this->fail('Did not throw as expected.');
		}
		catch(QueryException $q)
		{
			$this->assertInstanceOf('\Staple\Exception\QueryException',$q);
		}

		//Test SQL Server
		$query = Query::delete(self::TABLE_NAME,$connSqlSrv)
			->setSchema('myschema')
			->whereEqual('id', 5);

		//Assert
		$expected = "DELETE FROM myschema.".self::TABLE_NAME."\nWHERE id = :id";
		$expectedParamArray = [
			'id' => 5
		];

		$this->assertEquals($expected, $query->build());
		$this->assertEquals($expectedParamArray, $query->getParams());
	}
}
