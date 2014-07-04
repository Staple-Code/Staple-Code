<?php

require_once 'public\downloads\staple0.5.9\library\Staple\Config.class.php';

require_once 'PHPUnit\Framework\TestCase.php';

/**
 * Staple_Config test case.
 */
class Staple_ConfigTest extends PHPUnit_Framework_TestCase
{
	
	/**
	 * @var Staple_Config
	 */
	private $Staple_Config;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		parent::setUp();
		
		// TODO Auto-generated Staple_ConfigTest::setUp()
		

		$this->Staple_Config = new Staple_Config(/* parameters */);
	
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		// TODO Auto-generated Staple_ConfigTest::tearDown()
		

		$this->Staple_Config = null;
		
		parent::tearDown();
	}

	/**
	 * Constructs the test case.
	 */
	public function __construct()
	{
		// TODO Auto-generated constructor
	}

	/**
	 * Tests Staple_Config->__get()
	 */
	public function test__get()
	{
		// TODO Auto-generated Staple_ConfigTest->test__get()
		$this->markTestIncomplete("__get test not implemented");
		
		$this->Staple_Config->__get(/* parameters */);
	
	}

	/**
	 * Tests Staple_Config->__set()
	 */
	public function test__set()
	{
		// TODO Auto-generated Staple_ConfigTest->test__set()
		$this->markTestIncomplete("__set test not implemented");
		
		$this->Staple_Config->__set(/* parameters */);
	
	}

	/**
	 * Tests Staple_Config::get()
	 */
	public function testGet()
	{
		// TODO Auto-generated Staple_ConfigTest::testGet()
		$this->markTestIncomplete("get test not implemented");
		
		Staple_Config::get(/* parameters */);
	
	}

	/**
	 * Tests Staple_Config::getAll()
	 */
	public function testGetAll()
	{
		// TODO Auto-generated Staple_ConfigTest::testGetAll()
		$this->markTestIncomplete("getAll test not implemented");
		
		Staple_Config::getAll(/* parameters */);
	
	}

	/**
	 * Tests Staple_Config::getValue()
	 */
	public function testGetValue()
	{
		// TODO Auto-generated Staple_ConfigTest::testGetValue()
		$this->markTestIncomplete("getValue test not implemented");
		
		Staple_Config::getValue(/* parameters */);
	
	}

	/**
	 * Tests Staple_Config::set()
	 */
	public function testSet()
	{
		// TODO Auto-generated Staple_ConfigTest::testSet()
		$this->markTestIncomplete("set test not implemented");
		
		Staple_Config::set(/* parameters */);
	
	}

}

