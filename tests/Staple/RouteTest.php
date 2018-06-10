<?php
/**
 * Created by PhpStorm.
 * User: scott.henscheid
 * Date: 8/22/2017
 * Time: 12:49 PM
 */

namespace Staple\Tests;

use PHPUnit\Framework\TestCase;
use Staple\Json;
use Staple\Route;

class RouteTest extends TestCase
{
	const FUNCTIONAL_ROUTE1_RESULT = 'STAPLE Framework';
	const FUNCTIONAL_ROUTE2_RESULT = '{"make":"Toyota","model":"Corolla","options":{"Air Conditioning":"Yes","ABS":"No"}}';
	const FUNCTIONAL_ROUTE3_RESULT = '{"make":"Toyota","model":"Corolla","options":{"Air Conditioning":"Yes","ABS":"No"}}';
	const FUNCTIONAL_ROUTE4_RESULT = 'This is a test View.';
	const FUNCTIONAL_ROUTE5_RESULT = 'This is a test View.';

	private function getRouteObject($route = null)
	{
		return new Route($route);
	}

	/**
	 * @return bool
	 * @throws \Staple\Exception\RoutingException
	 */
	private function addStaticRoutes()
	{
		Route::add('json/result', function () {
			$object = new class {
				public $make = 'Toyota';
				public $model = 'Corolla';
				public $options = [
					'Air Conditioning' => 'Yes',
					'ABS' => 'No'
				];
			};
			return Json::create()->setData($object);
		});

		Route::add('product/{id}/details', function($id) {
			switch($id)
			{
				case 2637:
					return Json::success(new class {
						public $name = 'Whirlwind Blender';
						public $description = 'The best blender you will ever buy.';
					});
					break;
				case 89234:
					return Json::success(new class {
						public $name = 'Super Shovel';
						public $description = 'For all your shovelling needs.';
					});
					break;
			}
			return Json::error('Product Not Found');
		});

		Route::add('redirect', function() {
			return Route::create(['test','index'])->execute();
		});

		Route::add('new-customer', function() {
			return Route::create(['test','index']);
		});

		return true;
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

	public function testStaticRouting()
	{
		$this->addStaticRoutes();

		$route1 = $this->getRouteObject('text');
		$route2 = $this->getRouteObject('json/result');
		$route3 = $this->getRouteObject(['product','2637','details']);
		$route4 = $this->getRouteObject(['redirect']);
		$route5 = $this->getRouteObject('new-customer');

		//Route 1
		ob_start();
		$route1->execute();
		$route1Result = ob_get_contents();
		ob_end_clean();

		//Route 2
		ob_start();
		$route2->execute();
		$route2Result = ob_get_contents();
		ob_end_clean();

		//Route 3
		/*try
		{
			ob_start();
			$route3->execute();
			$route3Result = ob_get_contents();
			ob_end_clean();
		}
		catch(\Exception $e)
		{
			$this->fail('Route Threw Exception: '.$e->getMessage());
		}*/

		//Route 4
		ob_start();
		$route4->execute();
		$route4Result = ob_get_contents();
		ob_end_clean();

		//Route 5
		ob_start();
		$route5->execute();
		$route5Result = ob_get_contents();
		ob_end_clean();

		$this->assertEquals(self::FUNCTIONAL_ROUTE1_RESULT, $route1Result);
		$this->assertEquals(self::FUNCTIONAL_ROUTE2_RESULT, $route2Result);
		//$this->assertEquals(self::FUNCTIONAL_ROUTE3_RESULT, $route3Result);
		$this->assertEquals(self::FUNCTIONAL_ROUTE4_RESULT, $route4Result);
		$this->assertEquals(self::FUNCTIONAL_ROUTE5_RESULT, $route5Result);
	}
}