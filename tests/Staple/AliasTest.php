<?php
/**
 * Unit Tests for \Staple\Alias object
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
use Staple\Alias;
use Staple\Form\Form;

class AliasTest extends TestCase
{
	private $alias;

	public function __construct()
	{
		parent::__construct();
		$this->alias = new Alias();
	}

	public function testCheckAlias()
	{
		//This one exists
		$this->assertEquals($this->alias->checkAlias('Controller'),'\\Staple\\Controller\\Controller');

		//This one does not
		$this->assertNotEquals($this->alias->checkAlias('Controller'),'\\Staple\\Alias');
	}

	public function testAllAliasLoad()
	{
		$map = Alias::getClassMap();

		foreach($map as $key=>$value)
		{
			if(class_exists($key))
				$this->assertTrue(true);
			elseif(interface_exists($key))
				$this->assertTrue(true);
			elseif(trait_exists($key))
				$this->assertTrue(true);
			else
				$this->assertTrue(false);
		}
	}

	public function testAddAlias()
	{
		//Add an alias
		$this->alias->addAlias('MyNewClass','\\MyNamespace\\MyNewClass');

		//Test that the array key was added
		$this->assertArrayHasKey('MyNewClass',$this->alias->getClassMap());

		//Test that the alias is returned when checked for.
		$this->assertEquals($this->alias->checkAlias('MyNewClass'),'\\MyNamespace\\MyNewClass');
	}

	public function testLoad()
	{
		$this->alias->load('Controller');
		$this->alias->load('Route');
		$this->alias->load('View');
		$this->alias->load('Form');

		//Check that all Aliases are created.
		$this->assertTrue(class_exists('Controller'));
		$this->assertTrue(class_exists('Route'));
		$this->assertTrue(class_exists('View'));
		$this->assertTrue(class_exists('Form'));

		//Check that an object can be created from the alias
		$form = new Form();
		$this->assertTrue($form instanceof Form);
	}
}
