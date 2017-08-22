<?php
/**
 * Created by PhpStorm.
 * User: scott.henscheid
 * Date: 8/22/2017
 * Time: 12:49 PM
 */

namespace Staple\Tests;

use PHPUnit\Framework\TestCase;
use Staple\Route;

class RouteTest extends TestCase
{
	private function getRouteObject($route = null)
	{
		return new Route($route);
	}

	public function testCreateRoute()
	{
		$defaultRoute = $this->getRouteObject();
		$route1 = $this->getRouteObject('billing');
		$route2 = $this->getRouteObject('customer/new');
		$route3 = $this->getRouteObject('MyController/MyAction/param1/Param2');
		$route4 = $this->getRouteObject(['MyController','MyAction','param1','Param2']);
		$route5 = $this->getRouteObject(['MyController','123','param1','Param2']);

		$this->assertEquals(Route::DEFAULT_CONTROLLER, $defaultRoute->getController());
		$this->assertEquals(Route::DEFAULT_ACTION, $defaultRoute->getAction());
		$this->assertEquals('billing',$route1->getController());
		$this->assertEquals(Route::DEFAULT_ACTION,$route1->getAction());
		$this->assertEquals('customer',$route2->getController());
		$this->assertEquals('new',$route2->getAction());
		$this->assertEquals([],$route2->getParams());
		$this->assertEquals('MyController',$route3->getController());
		$this->assertEquals('MyAction',$route3->getAction());
		$this->assertEquals(['param1','Param2'],$route3->getParams());
		$this->assertEquals('MyController',$route4->getController());
		$this->assertEquals('MyAction',$route4->getAction());
		$this->assertEquals(['param1','Param2'],$route4->getParams());
		$this->assertEquals('MyController',$route5->getController());
		$this->assertEquals(Route::DEFAULT_ACTION,$route5->getAction());
		$this->assertEquals(['123','param1','Param2'],$route5->getParams());
	}
}