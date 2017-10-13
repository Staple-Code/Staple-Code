<?php
/**
 * Created by PhpStorm.
 * User: ironpilot
 * Date: 8/25/2017
 * Time: 8:23 AM
 */

namespace Staple\Tests;

use PHPUnit\Framework\TestCase;
use Staple\Json;

class JsonTest extends TestCase
{
	private function getJSONObject()
	{
		return new Json();
	}

	public function testCreateAndReturnObject()
	{
		$json = $this->getJSONObject();
		$obj = $json::create();

		$this->assertInstanceOf('\\Staple\\Json', $obj);
		$this->assertInstanceOf('\\Staple\\Json', $json);
	}

	public function testReturnSuccess()
	{
		$json = $this->getJSONObject();
		$response = $json::success(new class {
			public $make = 'Toyota';
			public $model = 'Tundra';
		});
		$code = http_response_code();

		$this->assertEquals(200, $code);
		$this->assertEquals('{"make":"Toyota","model":"Tundra"}', $response);
	}

	public function testReturnError()
	{
		$json = $this->getJSONObject();
		$response = $json::error('Invalid document.', 500, 'The supplied data did not make sense.');
		$code = http_response_code();

		$this->assertEquals(500, $code);
		$this->assertEquals('{"code":500,"message":"Invalid document.","details":"The supplied data did not make sense."}', (string)$response);
	}

	public function testReturnJsend()
	{
		$json = $this->getJSONObject();
		$response = $json::JSend($json::SUCCESS, new class {
			public $kingdom = "Animalia";
			public $phylum = "Mollusca";
			public $class = "Cephalopoda";
			public $order = "Octopoda";
		}, 'This is an Octopus.');
		$code = http_response_code();

		$this->assertEquals(200, $code);
		$this->assertEquals('{"status":"success","data":{"kingdom":"Animalia","phylum":"Mollusca","class":"Cephalopoda","order":"Octopoda"},"message":"This is an Octopus."}', (string)$response);
	}
}
