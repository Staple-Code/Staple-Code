<?php
/**
 * Created by PhpStorm.
 * User: scott.henscheid
 * Date: 8/22/2017
 * Time: 11:36 AM
 */

namespace Staple\Tests;

use PHPUnit\Framework\TestCase;
use Staple\Exception\PageNotFoundException;
use Staple\Exception\RoutingException;
use Staple\Request;
use Staple\Route;

class ProviderTest extends TestCase
{
	const ROUTE_TEXT = 'test/text';
	const ROUTE_JSON = 'test/json';
	const ROUTE_SAME = 'test/same';
	const ROUTE_OPTIONS = 'test/options-test';

	public function testRouting()
	{
		//Text Route
		Request::fake(self::ROUTE_TEXT, Request::METHOD_GET);
		$route = Route::create(self::ROUTE_TEXT);
		ob_start();
		$route->execute();
		$textBuffer = ob_get_contents();
		ob_end_clean();

		$this->assertEquals('test', $textBuffer);

		//JSON Route
		Request::fake(self::ROUTE_JSON, Request::METHOD_POST);
		ob_start();
		Route::create(self::ROUTE_JSON)->execute();
		$jsonBuffer = ob_get_contents();
		ob_end_clean();

		$this->assertEquals('{"make":"Honda","model":"Accord"}', $jsonBuffer);
	}

	public function testRouteNotFound()
	{
		$this->expectException(PageNotFoundException::class);

		Request::fake(self::ROUTE_JSON, Request::METHOD_PUT);
		Route::create(self::ROUTE_JSON)->execute();
	}

	public function testDifferentReturnForDifferentMethods()
	{
		//Get Route
		Request::fake(self::ROUTE_SAME, Request::METHOD_GET);
		$route = Route::create(self::ROUTE_SAME);
		ob_start();
		$route->execute();
		$textBuffer = ob_get_contents();
		ob_end_clean();

		$this->assertEquals('{"data":"Same","status":"Success"}', $textBuffer);

		//POST Route
		Request::fake(self::ROUTE_SAME, Request::METHOD_POST);
		$route = Route::create(self::ROUTE_SAME);
		ob_start();
		$route->execute();
		$textBuffer = ob_get_contents();
		ob_end_clean();

		$this->assertEquals('', $textBuffer);

		//PUT Route
		Request::fake(self::ROUTE_SAME, Request::METHOD_PUT);
		$route = Route::create(self::ROUTE_SAME);
		ob_start();
		$route->execute();
		$textBuffer = ob_get_contents();
		ob_end_clean();

		$this->assertEquals('Some Data', $textBuffer);
	}

	public function testOptions()
	{
		//OPTIONS Route
		Request::fake(self::ROUTE_OPTIONS, Request::METHOD_OPTIONS);
		$route = Route::create(self::ROUTE_OPTIONS);
		ob_start();
		$route->execute();
		$textBuffer = ob_get_contents();
		ob_end_clean();

		$this->assertEquals('', $textBuffer);
	}

	public function testOptionsThrowsExceptionOnMissingOptionsMethod()
	{
		//POST Route
		$this->expectException(RoutingException::class);
		Request::fake(self::ROUTE_JSON, Request::METHOD_OPTIONS);
		Route::create(self::ROUTE_JSON)->execute();
	}
}