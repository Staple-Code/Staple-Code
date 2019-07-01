<?php
/**
 * Unit Tests for \Staple\Model object
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
use SplObserver;
use Staple\Config;
use Staple\Exception\ModelNotFoundException;
use Staple\Model;
use Staple\Query\Condition;
use Staple\Query\Connection;
use Staple\Query\IConnection;
use Staple\Query\IStatement;
use Staple\Query\MockStatement;
use Staple\Query\Select;
use Exception;

class userModel extends Model
{
	protected $userKey = '12345';
}

class productListCategoryModel extends Model
{

}

class clientModel extends Model
{
	protected $_table = 'customers';
}

class MockTestConnection extends Connection implements IConnection
{
	private $data = [
		[
			'id'	=>	1,
			'name'	=>	"Joe",
			'email'	=>	'joe@aol.com'
		],
		[
			'id'	=>	2,
			'name'	=>	"Tom",
			'email'	=>	'tom@hotmail.com'
		],
	];

	public function __construct()
	{
		try
		{
			parent::__construct(Config::getValue('db', 'dsn'));
		}
		catch(Exception $e) {}
	}

	public function exec($statement)
	{
		// TODO: Implement exec() method.
	}

	/**
	 * @param string $statement
	 * @param array|null $driver_options
	 * @return IStatement
	 */
	public function prepare($statement, $driver_options = null)
	{
		$statement = new MockStatement();
		$statement->queryString = $statement;
		return $statement;
	}

	public function query($statement)
	{
		switch(get_class($statement))
		{
			case 'Staple\Query\Select':
				/** @var Select $statement */
				$mockStatement = new MockStatement();
				$results = [];
				foreach($this->data as $row)
				{
					$remove = false;
					/** @var Condition $where */
					foreach($statement->getWhere() as $where)
					{
						switch($where->getOperator())
						{
							case $where::EQUAL:
							case Condition::IS:
								if($row[$where->getColumn()] != $where->getValue())
									$remove = true;
								break;
							case Condition::GREATER:
								if($row[$where->getColumn()] <= $where->getValue())
									$remove = true;
								break;
							case Condition::GREATER_EQUAL:
								if($row[$where->getColumn()] < $where->getValue())
									$remove = true;
								break;
							case Condition::LESS:
								if($row[$where->getColumn()] >= $where->getValue())
									$remove = true;
								break;
							case Condition::LESS_EQUAL:
								if($row[$where->getColumn()] > $where->getValue())
									$remove = true;
								break;
							case Condition::IN:
								if(!in_array($where->getValue(),$row[$where->getColumn()]))
									$remove = true;
								break;
							case Condition::NOTEQUAL:
							case Condition::IS_NOT:
								if($row[$where->getColumn()] == $where->getValue())
									$remove = true;
								break;
						}
					}

					if($remove !== true)
					{
						$results[] = $row;
					}
				}
				$mockStatement->setRows($results);
				break;
			default:
				$mockStatement = false;
		}

		return $mockStatement;
	}

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Attach an SplObserver
	 * @link http://php.net/manual/en/splsubject.attach.php
	 * @param SplObserver $observer <p>
	 * The <b>SplObserver</b> to attach.
	 * </p>
	 * @return void
	 */
	public function attach(SplObserver $observer)
	{
		// TODO: Implement attach() method.
	}

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Detach an observer
	 * @link http://php.net/manual/en/splsubject.detach.php
	 * @param SplObserver $observer <p>
	 * The <b>SplObserver</b> to detach.
	 * </p>
	 * @return void
	 */
	public function detach(SplObserver $observer)
	{
		// TODO: Implement detach() method.
	}

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Notify an observer
	 * @link http://php.net/manual/en/splsubject.notify.php
	 * @return void
	 */
	public function notify()
	{
		// TODO: Implement notify() method.
	}

	public function getLastQuery()
	{
		// TODO: Implement getLastQuery() method.
	}

	public function setLastQuery($lastQuery)
	{
		// TODO: Implement setLastQuery() method.
	}

	public function getDriver()
	{
		return $this->driver;
	}

	public function getDriverOptions()
	{
		return NULL;
	}
}


class ModelTest extends TestCase
{
	/**
	 * @return MockTestConnection
	 */
	protected function getMockConnection()
	{
		return new MockTestConnection();
	}

	protected function getTestUserModelObject()
	{
		//Setup a bunch of data to test with.
		$user = new userModel();
		$user->first_name = 'Jenny';
		$user->last_name = 'McCarthy';
		$client = $this->getTestClientModelObject();
		$client->first_name = 'Alyson';
		$client->last_name = 'Hannigan';
		$user->client = $client;
		$user->fibonacci = [0,1,1,2,3,5,8,13,21];

		return $user;
	}

	protected function getTestProductListCategoryModelObject()
	{
		return new productListCategoryModel();
	}

	protected function getTestClientModelObject()
	{
		return new clientModel();
	}

	/**
	 * Test that the auto-creation of table models is constructed correctly.
	 * @test
	 */
	public function testModelTableNameGeneration()
	{
		$userModel = $this->getTestUserModelObject();
		$productListCategoryModel = $this->getTestProductListCategoryModelObject();
		$clientModel = $this->getTestClientModelObject();

		$this->assertEquals('users',$userModel->_getTable());
		$this->assertEquals('product_list_categories',$productListCategoryModel->_getTable());
		$this->assertEquals('customers',$clientModel->_getTable());
	}

	/**
	 * Test the model's ability to create instances of itself.
	 * @test
	 */
	public function testFactory()
	{
		$this->assertInstanceOf('Staple\\Tests\\userModel',userModel::make());
		$this->assertInstanceOf('Staple\\Tests\\clientModel',clientModel::make());
		$this->assertInstanceOf('Staple\\Tests\\productListCategoryModel',productListCategoryModel::make());
	}

	/**
	 * @test
	 * @throws ModelNotFoundException
	 */
	public function testFind()
	{
		/** @var userModel $user */
		$user = userModel::find(1, $this->getMockConnection());
		/** @var userModel $user2 */
		$user2 = userModel::find(2, $this->getMockConnection());


		//Assert user 1 results
		$this->assertInstanceOf('Staple\Tests\userModel',$user);
		$this->assertEquals(1, $user->id);
		$this->assertEquals('Joe', $user->name);
		$this->assertEquals('joe@aol.com', $user->email);
		$this->assertEquals('Joe', $user->getName());
		$this->assertEquals('joe@aol.com', $user->getEmail());
		$this->assertEquals(1, $user->getId());

		//Assert user 2 results
		$this->assertInstanceOf('Staple\Tests\userModel',$user2);
		$this->assertEquals(2, $user2->id);
		$this->assertEquals('Tom', $user2->name);
		$this->assertEquals('tom@hotmail.com', $user2->email);
		$this->assertEquals('Tom', $user2->getName());
		$this->assertEquals('tom@hotmail.com', $user2->getEmail());
		$this->assertEquals(2, $user2->getId());

		try
		{
			/** @var bool $user3 */
			userModel::find(3, $this->getMockConnection());
			$this->hasFailed();
		}
		catch(ModelNotFoundException $e)
		{
			//Assert user 3 not found
			$this->assertInstanceOf('\\Staple\Exception\\ModelNotFoundException',$e);
		}
	}

	/**
	 * @test
	 * @throws ModelNotFoundException
	 * @throws \Staple\Exception\QueryException
	 */
	public function testFindAll()
	{
		/** @var userModel[] $users */
		$users = userModel::findAll(NULL,NULL,$this->getMockConnection());

		$this->assertCount(2, $users);
		foreach($users as $user)
		{
			$this->assertInstanceOf('Staple\Tests\userModel',$user);
		}

		$user2 = array_pop($users);
		//Assert user 2 results
		$this->assertInstanceOf('Staple\Tests\userModel',$user2);
		$this->assertEquals(2, $user2->id);
		$this->assertEquals('Tom', $user2->name);
		$this->assertEquals('tom@hotmail.com', $user2->email);
		$this->assertEquals('Tom', $user2->getName());
		$this->assertEquals('tom@hotmail.com', $user2->getEmail());
		$this->assertEquals(2, $user2->getId());

		$user1 = array_pop($users);
		//Assert user 1 results
		$this->assertInstanceOf('Staple\Tests\userModel',$user1);
		$this->assertEquals(1, $user1->id);
		$this->assertEquals('Joe', $user1->name);
		$this->assertEquals('joe@aol.com', $user1->email);
		$this->assertEquals('Joe', $user1->getName());
		$this->assertEquals('joe@aol.com', $user1->getEmail());
		$this->assertEquals(1, $user1->getId());
	}

	public function testArrayGet()
	{
		$user = $this->getTestUserModelObject();

		//Check that we can get the data
		$this->assertEquals('Jenny', $user['first_name']);
		$this->assertEquals('McCarthy', $user['last_name']);
		$this->assertEquals('Alyson',$user['client']['first_name']);
		$this->assertEquals([0,1,1,2,3,5,8,13,21],$user['fibonacci']);
		$this->assertNull($user['client']['phone']);
	}

	public function testArraySet()
	{
		$user = $this->getTestUserModelObject();

		//Check that we can get the data
		$this->assertEquals('Jenny', $user['first_name']);
		$this->assertEquals('McCarthy', $user['last_name']);
		$this->assertEquals('Alyson',$user['client']['first_name']);
		$this->assertNull($user['phone']);

		$user['phone'] = '555-555-5555';
		$user['first_name'] = 'Taylor';
		$user['last_name'] = 'Swift';
		$user['client']['last_name'] = 'Stoner';

		//Check that the set worked
		$this->assertEquals('555-555-5555', $user['phone']);
		$this->assertEquals('555-555-5555', $user->phone);
		$this->assertEquals('Taylor', $user['first_name']);
		$this->assertEquals('Swift', $user['last_name']);
		$this->assertEquals('Stoner', $user['client']['last_name']);
	}
	public function testArrayIsset()
	{
		$user = $this->getTestUserModelObject();

		//Test Isset
		$this->assertFalse(isset($user['phone']));
		$this->assertTrue(isset($user['first_name']));
		$this->assertTrue(isset($user['last_name']));
		$this->assertTrue(isset($user['client']['first_name']));
		$this->assertFalse(isset($user['client']['phone']));
	}
	public function testArrayUnset()
	{
		$user = $this->getTestUserModelObject();

		//Test Isset\
		$this->assertTrue(isset($user['first_name']));
		$this->assertTrue(isset($user['last_name']));
		$this->assertTrue(isset($user['client']['first_name']));


		//Test Unset
		unset($user['first_name']);
		unset($user['last_name']);
		unset($user['client']['first_name']);

		$this->assertFalse(isset($user['first_name']));
		$this->assertFalse(isset($user['last_name']));
		$this->assertFalse(isset($user['client']['first_name']));
	}

	public function testJSONEncode()
	{
		$model = $this->getTestUserModelObject();

		$jsonObject = '{"first_name":"Jenny","last_name":"McCarthy","client":{"first_name":"Alyson","last_name":"Hannigan"},"fibonacci":[0,1,1,2,3,5,8,13,21],"userKey":"12345"}';

		$this->assertEquals($jsonObject,json_encode($model));
	}
}
