<?php

require_once 'PHPUnit\Framework\TestSuite.php';

require_once 'tests\TestSuite\Staple_MailTest.php';

/**
 * Static test suite.
 */
class StapleTests extends PHPUnit_Framework_TestSuite
{

	/**
	 * Constructs the test suite handler.
	 */
	public function __construct()
	{
		$this->setName('StapleTests');
		
		$this->addTestSuite('Staple_MailTest');
	
	}

	/**
	 * Creates the suite.
	 */
	public static function suite()
	{
		return new self();
	}
}

