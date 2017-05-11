<?php
/**
 * Created by PhpStorm.
 * User: scott.henscheid
 * Date: 5/11/2017
 * Time: 12:13 PM
 */

namespace Staple\Tests;

use Staple\Query\MockConnection;
use Staple\Query\Query;
use PHPUnit\Framework\TestCase;

class UnionTest extends TestCase
{
	const TABLE_NAME = 'customers';

	private function getMockConnection($driver = MockConnection::DRIVER_MYSQL)
	{
		return new MockConnection($driver);
	}

	public function testQueryWithSchema()
	{
		//Setup
		$connSqlSrv = $this->getMockConnection(MockConnection::DRIVER_SQLSRV);

		//Act
		$columns = [
			'first_name',
			'last_name',
			'address',
			'city',
			'state',
			'zip'
		];
		$select1 = Query::select(self::TABLE_NAME,$columns,$connSqlSrv)
			->setSchema('myschema')
			->innerJoin('orders','orders.customer_id = customers.id')
			->whereNull('deleted');

		$select2 = Query::select(self::TABLE_NAME,$columns,$connSqlSrv)
			->setSchema('myschema')
			->innerJoin('orders','orders.customer_id = customers.id')
			->whereEqual('id',10);

		$query = Query::union([$select1, $select2])
			->orderBy(['last_name','first_name']);

		//Assert
		$expected = "SELECT \n\t*\n\tFROM (SELECT\nfirst_name, last_name, address, city, state, zip \nFROM myschema.customers\nINNER JOIN myschema.orders ON orders.customer_id = customers.id\nWHERE deleted IS NULL\nUNION \nSELECT\nfirst_name, last_name, address, city, state, zip \nFROM myschema.customers\nINNER JOIN myschema.orders ON orders.customer_id = customers.id\nWHERE id = 10) AS stapleunion\nORDER BY last_name,first_name";
		$this->assertEquals($expected,(string)$query);
	}
}
