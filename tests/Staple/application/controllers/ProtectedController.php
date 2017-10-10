<?php

use Staple\Controller\Controller;
use Staple\View;

/**
 * Class ProtectedController
 * @protected
 */
class ProtectedController extends Controller
{
	/**
	 * @open
	 * @return View
	 */
	public function index()
	{
		return View::create();
	}

	public function data()
	{
		return View::create();
	}
}