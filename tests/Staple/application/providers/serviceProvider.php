<?php
use Staple\Json;
use Staple\Provider;

class serviceProvider extends Provider
{
	public function getTest()
	{
		return 'Test';
	}

	public function getReturnJson()
	{
		$obj = new stdClass();
		$obj->state = 'California';
		$obj->city = 'Sacramento';
		return Json::object($obj);
	}
}