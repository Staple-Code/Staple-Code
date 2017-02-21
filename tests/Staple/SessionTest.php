<?php
/**
 * Created by PhpStorm.
 * User: scott.henscheid
 * Date: 6/6/2016
 * Time: 2:08 PM
 */

namespace Staple\Tests;

use PHPUnit\Framework\TestCase;
use Staple\Session\Session;

class SessionTest extends TestCase
{
	public function testSessionStart()
	{
		$this->markTestIncomplete();

		//Setup
		Session::start(NULL,true);

		//Act
		$id = session_id();

		//Assert
		$this->assertNotNull($id);
		$this->assertNotEmpty($id);
	}
}
