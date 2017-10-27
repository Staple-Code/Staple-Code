<?php

use Staple\Controller\RestfulController;
use Staple\Json;

class TestProvider extends RestfulController
{
	public function getText()
	{
		return 'test';
	}

	public function postJson()
	{
		return Json::success(new class {
			public $make = 'Honda';
			public $model = 'Accord';
		});
	}

	public function getSame()
	{
		return Json::success(new class {
			public $data = 'Same';
			public $status = 'Success';
		});
	}

	public function postSame()
	{
		return true;
	}

	public function putSame()
	{
		return 'Some Data';
	}

	public function optionsOptionsTest()
	{
		return true;
	}

	/**
	 * @protected
	 */
	public function getProtected()
	{
		return Json::success(new class {
			public $request = 'Successful';
			public $data = 'This is secure data.';
		});
	}
}