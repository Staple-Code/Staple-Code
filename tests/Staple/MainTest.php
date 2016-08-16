<?php
/**
 * Created by PhpStorm.
 * User: scott.henscheid
 * Date: 8/16/2016
 * Time: 4:27 PM
 */

namespace Staple\Tests;

use Staple\Main;

class MainTest extends \PHPUnit_Framework_TestCase
{
	public function testInDevMode()
	{
		$devMode = Main::get()->inDevMode();

		$this->assertTrue($devMode);
	}
}
