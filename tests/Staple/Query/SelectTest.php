<?php
/**
 * Unit Tests for \Staple\Query\Select object
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

use Staple\Query\Query;
use Staple\Query\Select;
use Staple\Query\MockConnection;

//require_once '../Mocks/MockConnection.php';

class SelectTest extends \PHPUnit_Framework_TestCase
{
	const TABLE_NAME = 'customers';

	private function getMockConnection($driver = MockConnection::DRIVER_MYSQL)
	{
		return new MockConnection($driver);
	}

	public function testBuildSelectObject()
	{
		$conn = $this->getMockConnection();

		//Act
		$obj1 = Query::select(self::TABLE_NAME,NULL,$conn);
		$obj2 = Select::select(self::TABLE_NAME,NULL,$conn);
		$obj3 = new Select(self::TABLE_NAME,NULL,$conn);

		//Assert
		$this->assertInstanceOf('\\Staple\\Query\\Select',$obj1);
		$this->assertInstanceOf('\\Staple\\Query\\Select',$obj2);
		$this->assertInstanceOf('\\Staple\\Query\\Select',$obj3);
	}

	public function testQuery()
	{
		//Setup
		$conn = $this->getMockConnection();

		//Act
		$columns = [
			'first_name',
			'last_name',
			'address',
			'city',
			'state',
			'zip'
		];
		$query = Query::select(self::TABLE_NAME,$columns,$conn)
			->innerJoin('orders','orders.customer_id = customers.id');

		//Assert
		$expected = "SELECT\nfirst_name, last_name, address, city, state, zip \nFROM customers\nINNER JOIN orders ON orders.customer_id = customers.id";
		$this->assertEquals($expected,(string)$query);
	}
}
