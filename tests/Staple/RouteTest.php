<?php
/**
 * Unit Tests for \Staple\Route object
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

use Staple\Route;

class RouteTest extends \PHPUnit_Framework_TestCase
{
	public function getTestObject($route)
	{
		return new Route($route);
	}

	/**
	 * Test the factory method
	 * @test
	 */
	public function testMakeObject()
	{
		$route = Route::make('foo');

		$this->assertInstanceOf('Route', $route);
		$this->assertEquals('foo',$route->getController());
		$this->assertEquals('index',$route->getAction());
	}

	public function testControllerExecuteRouteReturnView()
	{
		$route = $this->getTestObject('test/foo');
		$route->execute();

		$this->markTestIncomplete();
	}

	public function testStaticRouteRegistration()
	{
		$route = Route::register('/add',function($a){return $a+2;});

		$this->assertInstanceOf('Staple\Route',$route);
	}
}
