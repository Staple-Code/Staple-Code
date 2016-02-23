<?php
/**
 * Unit Tests for \Staple\Provider object
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

use Staple\Exception\PageNotFoundException;
use Staple\Main;
use Staple\Provider;

class TestProvider extends Provider
{
	public function __construct()
	{
		parent::__construct();

		$this->openMethod('getFoo');
	}

	public function getFoo()
	{
		return 'Foo';
	}

	public function postFoo()
	{
		return 'Bar';
	}
}

class ProviderTest extends \PHPUnit_Framework_TestCase
{
	public function getTestObject()
	{
		return new TestProvider();
	}

	/**
	 * @test
	 */
	public function testAuth()
	{
		$provider = $this->getTestObject();

		$this->assertEquals(1,$provider->auth('getFoo'));
		//$this->assertEquals(0,$provider->auth('postFoo'));
	}

	/**
	 * Test that we can successfully route to a provider, that it returns an not found on a bad route, and that we cannot route to auth objects.
	 * @test
	 * @throws \Exception
	 */
	public function testRouteToProvider()
	{
		ob_start();
		Main::get()->run('service/test');
		$bufferWorking = ob_get_contents();
		ob_end_clean();

		$this->assertEquals('Test',$bufferWorking);

		try
		{
			Main::get()->run('service/not-found');
			throw new \Exception('Test Failed.');
		}
		catch (PageNotFoundException $e)
		{
			$this->assertInstanceOf('PageNotFoundException',$e);
		}

		try
		{
			Main::get()->run('service/auth');
			throw new \Exception('Test Failed.');
		}
		catch (PageNotFoundException $e)
		{
			$this->assertInstanceOf('PageNotFoundException',$e);
		}
	}

	public function testReturnJson()
	{
		ob_start();
		Main::get()->run('service/return-json');
		$bufferWorking = ob_get_contents();
		ob_end_clean();

		$this->assertEquals('{"state":"California","city":"Sacramento"}',$bufferWorking);
	}
}
