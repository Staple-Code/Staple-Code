<?php
/**
 * Unit Tests for \Staple\Query\Query object
 *
 * @author Ironpilot
 * @copyright Copyright (c) 2011, STAPLE CODE
 *
 * This file is part of the STAPLE Framework.
 *
 * The STAPLE Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by the
 * Free Software Foundation, either version 3 of the License, or (at your option)
 * any later version.
 *
 * The STAPLE Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public License for
 * more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with the STAPLE Framework.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Staple\Tests;

use PHPUnit\Framework\TestCase;
use Staple\Exception\ConfigurationException;
use Staple\Exception\QueryException;
use Staple\Query\Condition;
use Staple\Query\Connection;
use Staple\Query\MockStatement;
use Staple\Query\Query;
use Staple\Query\MockConnection;
use \DateTime;

class QueryTest extends TestCase
{
	const PARAMETERIZED_QUERY_STRING = "SELECT\n* \nFROM customers\nWHERE city = :city AND age = :age AND created <= :created";

	private function getMockConnection()
	{
		return new MockConnection(NULL);
	}
	private function getInMemorySqlite()
	{
		return new MockConnection('sqlite::memory:');
	}

	/**
	 * @test
	 * @throws \Staple\Exception\QueryException
	 */
	public function testMySQLQueryCreation()
	{
		//Setup
		$connection = $this->getInMemorySqlite();

		//Act
		$select = Query::select('customers',NULL, $connection);
		$insert = Query::insert('customers',array('id'=>1), $connection);
		$update = Query::update('customers',array('name'=>'Larry'), $connection)->setAllowUnboundedUpdate(true);
		$delete = Query::delete('customers', $connection);

		//Assert
		$this->assertInstanceOf('\Staple\Query\Select',$select);
		$this->assertEquals("SELECT\n* \nFROM customers",(string)$select);
		$this->assertInstanceOf('\Staple\Query\Insert',$insert);
		$this->assertEquals("INSERT \nINTO customers (id) \nVALUES (:id) ",(string)$insert);
		$this->assertInstanceOf('\Staple\Query\Update',$update);
		$this->assertEquals("UPDATE customers\nSET name=:name",(string)$update);
		$this->assertInstanceOf('\Staple\Query\Delete',$delete);
		$this->assertEquals("DELETE FROM customers",(string)$delete);
	}

	public function testMakeSQLiteInMemoryDatabase()
	{
		//Setup
		$connection = $this->getInMemorySqlite();

		//Assert
		$this->assertInstanceOf('Staple\Query\Connection',$connection);
		$this->assertEquals(Connection::DRIVER_SQLITE,$connection->getDriver());
	}

	/**
	 * Test that we can construct a stored procedure call to MySQL
	 * @test
	 * @throws QueryException
	 * @throws ConfigurationException
	 */
	public function testStoredMySqlProcedureConstruction()
	{
		$connection = $this->getMockConnection();
		$connection->setDriver(Connection::DRIVER_MYSQL);

		$params = [
			'first_name'=>	'John',
			'last_name' =>	'Smith',
			'married'	=>	true,
			'age' 		=>	32,
			'deleted'	=>	NULL,
		];
		Query::procedure('GetCustomers', $params, $connection);

		$this->assertEquals('CALL GetCustomers(:first_name, :last_name, :married, :age, :deleted)',$connection->getLastQuery());

		//Now test with numeric keys for the params
		$connection = $this->getMockConnection();
		$connection->setDriver(Connection::DRIVER_MYSQL);

		$params = [
			'John',
			'Smith',
			true,
			32,
			NULL,
		];
		Query::procedure('GetCustomers', $params, $connection);

		$this->assertEquals('CALL GetCustomers(?, ?, ?, ?, ?)',$connection->getLastQuery());
	}

	/**
	 * Test that we can construct a stored procedure call to SQL Server
	 * @test
	 * @throws QueryException
	 * @throws ConfigurationException
	 */
	public function testStoredSqlSrvProcedureConstruction()
	{
		$connection = $this->getMockConnection();
		$connection->setDriver(Connection::DRIVER_SQLSRV);

		Query::procedure('GetCustomers',['first_name'=>'John','last_name'=>'Smith'],$connection);

		$this->assertEquals('EXEC GetCustomers @first_name = ?, @last_name = ?', $connection->getLastQuery());
	}

	/**
	 * @test
	 * @throws QueryException
	 */
	public function testParameterizedQuery()
	{
		$connection = $this->getMockConnection();
		$connection->setDriver(Connection::DRIVER_MYSQL);

		$createdEpoch = new DateTime('1-1-1970');

		$query = Query::select('customers', null, $connection)
			->whereEqual('city', 'Memphis')
			->whereEqual('age', 20)
			->where('created', Condition::LESS_EQUAL, $createdEpoch);

		$queryString = $query->build();

		$this->assertEquals(self::PARAMETERIZED_QUERY_STRING, $queryString);

		$params = $query->getParams();

		$this->assertEquals(['city'=>'Memphis','age'=>20, 'created' => $createdEpoch], $params);

		$result = $query->execute();

		$this->assertInstanceOf(MockStatement::class, $result);
	}

	/**
	 * @test
	 * @throws QueryException
	 */
	public function testParameterizedQueryToStringConversion()
	{
		$connection = $this->getMockConnection();
		$connection->setDriver(Connection::DRIVER_MYSQL);

		$createdEpoch = new DateTime('1-1-1970');

		$query = Query::select('customers', null, $connection)
			->whereEqual('city', 'Memphis')
			->whereEqual('age', 20)
			->where('created', Condition::LESS_EQUAL, $createdEpoch);

		$this->assertEquals(self::PARAMETERIZED_QUERY_STRING, (string)$query);
	}
}
