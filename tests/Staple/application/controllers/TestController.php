<?php

use Staple\Controller\Controller;
use Staple\View;

class TestController extends Controller
{
	public function index()
	{
		return View::create();
	}

	/**
	 * @protected
	 */
	public function authenticated()
	{
		return View::create();
	}

	public function underscored_route($param = '')
	{
		return 'Underscored Route'.htmlentities($param);
	}
}