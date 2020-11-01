<?php
/**
 * Created by PhpStorm.
 * User: scott.henscheid
 * Date: 3/8/2016
 * Time: 4:16 PM
 */

namespace Staple\Tests;

use PHPUnit\Framework\TestCase;
use Staple\Config;

class ConfigTest extends TestCase
{
	protected function setUp(): void
	{
		Config::changeEnvironment(Config::DEFAULT_CONFIG_SET);
	}

	protected function tearDown(): void
	{
		Config::changeEnvironment(Config::DEFAULT_CONFIG_SET);
	}

	public function testConfigRead()
	{
		$this->assertCount(3,Config::get('application'));
		$this->assertArrayHasKey('host',Config::get('db'));
		$this->assertEquals('localhost',Config::getValue('db','host'));
		$this->assertEquals(NULL,Config::getValue('forms','elementViewAdapter'));
	}

	public function testEnvironmentConfig()
	{
		Config::changeEnvironment('dev');

		$this->assertCount(2,Config::get('application'));
		$this->assertArrayHasKey('host',Config::get('db'));
		$this->assertEquals('BootstrapViewAdapter',Config::getValue('forms','elementViewAdapter'));
		$this->assertEquals(1,Config::getValue('errors','enable_timer'));

		Config::changeEnvironment('test');

		$this->assertCount(2,Config::get('application'));
		$this->assertArrayHasKey('host',Config::get('db'));
		$this->assertEquals('test',Config::getValue('layout','default'));
		$this->assertEquals(0,Config::getValue('errors','enable_timer'));
	}

	public function testSetValue()
	{
		$this->assertCount(3,Config::get('application'));
		$this->assertArrayHasKey('host',Config::get('db'));
		$this->assertEquals('localhost',Config::getValue('db','host'));
		$this->assertEquals(NULL,Config::getValue('forms','elementViewAdapter'));

		Config::setValue('db','host','remotehost');
		Config::setValue('forms','elementViewAdapter','MyViewAdapter');

		$this->assertEquals('remotehost',Config::getValue('db','host'));
		$this->assertEquals('MyViewAdapter',Config::getValue('forms','elementViewAdapter'));
	}
}
