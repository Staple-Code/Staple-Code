<?php
/**
 * Created by PhpStorm.
 * User: scott.henscheid
 * Date: 5/11/2017
 * Time: 11:22 AM
 */

namespace Staple\Tests;

use Staple\Exception\QueryException;
use Staple\Query\MockConnection;
use Staple\Query\Query;
use PHPUnit\Framework\TestCase;

class UpdateTest extends TestCase
{
	const TABLE_NAME = 'customers';

	private function getMockConnection($driver = MockConnection::DRIVER_MYSQL)
	{
		return new MockConnection($driver);
	}

	/**
	 * @test
	 * @throws QueryException
	 */
	public function testQueryWithSchema()
	{
		//Setup
		$connMySQL = $this->getMockConnection(MockConnection::DRIVER_MYSQL);
		$connSqlSrv = $this->getMockConnection(MockConnection::DRIVER_SQLSRV);

		//Act
		//Should throw for MySQL
		try
		{
			Query::update(self::TABLE_NAME, null, $connMySQL)
				->setSchema('test');
			$this->fail('Did not throw as expected.');
		}
		catch(QueryException $q)
		{
			$this->assertInstanceOf('\Staple\Exception\QueryException',$q);
		}

		//Test SQL Server
		$columns = [
			'first_name'=>'Larry',
			'last_name'=>'Smith',
			'address'=>'123 1st St.',
			'city'=>'Boston',
			'state'=>'MA',
			'zip'=>'02110'
		];
		try
		{
			$query = Query::update(self::TABLE_NAME, $columns, $connSqlSrv)
				->setSchema('myschema')
				->whereEqual('id', 5);
		}
		catch(QueryException $e)
		{
			$this->fail('Query Builder should not throw an exception: '.$e->getMessage());
			return;
		}

		//Assert
		$expected = "UPDATE myschema.".self::TABLE_NAME."\nSET first_name=:first_name, last_name=:last_name, address=:address, city=:city, state=:state, zip=:zip\nWHERE id = :id";
		$expectedParamArray = [
			'first_name'=>'Larry',
			'last_name'=>'Smith',
			'address'=>'123 1st St.',
			'city'=>'Boston',
			'state'=>'MA',
			'zip'=>'02110',
			'id' => 5,
		];
		$this->assertEquals($expected, (string)$query);
		$this->assertEquals($expectedParamArray, $query->getParams());
	}
}
