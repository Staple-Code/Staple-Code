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
use Staple\Exception\ConfigurationException;
use stdClass;

class ConfigTest extends TestCase
{
    /**
     * @throws ConfigurationException
     */
    protected function setUp(): void
	{
		Config::changeEnvironment(Config::DEFAULT_CONFIG_SET);
	}

    /**
     * Resets the environment configuration to the default state.
     *
     * @return void
     * @throws ConfigurationException
     */
    protected function tearDown(): void
	{
		Config::changeEnvironment(Config::DEFAULT_CONFIG_SET);
	}

    /**
     * @throws ConfigurationException
     */
    public function testConfigRead()
	{
		$this->assertCount(3, Config::get('application'));
		$this->assertArrayHasKey('host', Config::get('db'));
		$this->assertEquals('localhost', Config::getValue('db','host'));
		$this->assertEquals(NULL, Config::getValue('forms','elementViewAdapter'));
	}

    /**
     * @throws ConfigurationException
     */
    public function testConfigReadAsObject()
    {
        $expected = new stdClass();
        $expected->host = 'localhost';
        $expected->dsn = 'sqlite::memory:';
        $expected->driver = 'sqlite';
        $expected->username = null;
        $expected->password = null;
        $expected->db = 'staple';
        $expected->options = [];
        $this->assertEquals($expected, Config::get('db', true));
    }

    /**
     * @throws ConfigurationException
     */
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

    /**
     * @throws ConfigurationException
     */
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
