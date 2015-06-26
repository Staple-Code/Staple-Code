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

use Staple\Model;

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


class ModelTest extends \PHPUnit_Framework_TestCase
{
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

	public function testFind()
	{
		$this->markTestIncomplete();
		userModel::make()->find(1);

		//$this->assertInstanceOf(,'userModel');
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
