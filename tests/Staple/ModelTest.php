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
		return new userModel();
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
}
