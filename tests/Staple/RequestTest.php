<?php
/**
 * Created by PhpStorm.
 * User: scott.henscheid
 * Date: 10/5/2017
 * Time: 12:12 PM
 */

namespace Staple\Tests;

use PHPUnit\Framework\TestCase;
use Staple\Request;

class RequestTest extends TestCase
{
	const URI = 'test/test';
	const METHOD = 'GET';

	protected function getRequestObject()
	{
		return Request::fake(self::URI, self::METHOD);
	}

	public function testGetRequest()
	{
		$req = $this->getRequestObject();

		$this->assertInstanceOf('\\Staple\\Request', $req);
		$this->assertEquals(self::URI, $req->getUri());
		$this->assertEquals(self::METHOD, $req->getMethod());
	}
}
