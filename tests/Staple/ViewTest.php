<?php
/**
 * Unit Tests for \Staple\View object
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


use Staple\View;

class ViewTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @return View
	 */
	private function getTestObject()
	{
		return new View();
	}

	/**
	 * @test
	 */
	public function testAddData()
	{
		//Get the object
		$view = $this->getTestObject();

		//Setup test data
		$testString = 'My Test Data';
		$testArray = array(1,2,3,4);
		$testObject = new \stdClass();
		$testObject->myString = $testString;
		$testObject->myArray = $testArray;

		//Add data
		$view->addData('testString',$testString);
		$view->addData('testArray',$testArray);
		$view->addData('testObject',$testObject);

		//Assert
		$this->assertEquals($view->testString, $testString);
		$this->assertEquals($view->testArray, $testArray);
		$this->assertInstanceOf('stdClass',$view->testObject);
		$this->assertEquals($view->testObject,$testObject);
	}

	/**
	 * @covers View->hasController
	 * @uses View->setController
	 */
	public function testHasController()
	{
		$view = $this->getTestObject();

		//Assert that no controller has been defined yet
		$this->assertNotTrue($view->hasController());

		//Add a controller Reference
		$view->setController('index');

		//Assert that this is now true
		$this->assertTrue($view->hasController());
	}

	public function testHasView()
	{
		$view = $this->getTestObject();

		//Assert that no controller has been defined yet
		$this->assertNotTrue($view->hasView());

		//Add a controller Reference
		$view->setView('index');

		//Assert that this is now true
		$this->assertTrue($view->hasView());
	}

	public function testCreate()
	{
		//Statically create a view
		$view = View::create();

		//Assert that we got a view object back
		$this->assertInstanceOf('View',$view);
	}

	public function testDynamicSetAndGet()
	{
		$view = $this->getTestObject();

		//Setup test data
		$testString = 'My Test Data';
		$testArray = array(1,2,3,4);
		$testObject = new \stdClass();
		$testObject->myString = $testString;
		$testObject->myArray = $testArray;

		//Add data
		$view->testString = $testString;
		$view->testArray = $testArray;
		$view->testObject = $testObject;

		//Assert
		$this->assertEquals($view->testString, $testString);
		$this->assertEquals($view->testArray, $testArray);
		$this->assertInstanceOf('stdClass',$view->testObject);
		$this->assertEquals($view->testObject,$testObject);
	}

	/*public function testBuild()
	{
		//Get the test object
		$view = $this->getTestObject();

		//Set controller and view
		$view->setController('index');
		$view->setView('testBuild');

		//Buffer output and build the view
		ob_start();
		$view->build();
		$buffer = ob_get_contents();
		ob_end_clean();

		//Assert the results
		$this->assertEqual('View Build Test', $buffer);
	}*/
}
