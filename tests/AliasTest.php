<?php
/**
 *
 */

namespace Staple\Tests;


use Staple\Alias;

class AliasTest extends \PHPUnit_Framework_TestCase
{
	private $alias;

	public function __construct()
	{
		$this->alias = new Alias();
	}

	public function testCheckAlias()
	{
		//This one exists
		$this->assertEquals($this->alias->checkAlias('Controller'),'\\Staple\\Controller');

		//This one does not
		$this->assertNotEquals($this->alias->checkAlias('Controller'),'\\Staple\\Alias');
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
