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
use Staple\Query\Connection;
use Staple\Query\Query;
use Staple\Query\MockConnection;

//require_once '../Mocks/MockConnection.php';

class QueryTest extends TestCase
{
	private function getMockConnection($driver = NULL)
	{
		return new MockConnection($driver);
	}
	private function getInMemorySqlite()
	{
		return new MockConnection('sqlite::memory:');
	}

	public function testMySQLQueryCreation()
	{
		//Setup
		$connection = $this->getInMemorySqlite();

		//Act
		$select = Query::select('customers',NULL, $connection);
		$insert = Query::insert('customers',array('id'=>1), $connection);
		$update = Query::update('customers',array('name'=>'Larry'), $connection);
		$delete = Query::delete('customers', $connection);

		//Assert
		$this->assertInstanceOf('\Staple\Query\Select',$select);
		$this->assertEquals("SELECT\n* \nFROM customers",(string)$select);
		$this->assertInstanceOf('\Staple\Query\Insert',$insert);
		$this->assertEquals("INSERT \nINTO customers (id) \nVALUES (1) ",(string)$insert);
		$this->assertInstanceOf('\Staple\Query\Update',$update);
		$this->assertEquals("UPDATE  customers\nSET  name='Larry'",(string)$update);
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
}
