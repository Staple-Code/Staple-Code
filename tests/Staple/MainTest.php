<?php
/**
 * Created by PhpStorm.
 * User: Ironpilot
 * Date: 4/9/2015
 * Time: 7:07 PM
 */

namespace Staple\Tests;


use Staple\Main;

class MainTest extends \PHPUnit_Framework_TestCase
{
	public function testConstruct()
	{
		$main = Main::get();

		$this->assertInstanceOf('Staple\Main',$main);
	}
}
