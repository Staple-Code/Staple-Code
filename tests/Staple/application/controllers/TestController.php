<?php

use Staple\Controller;
use Staple\View;

class TestController extends Controller
{
	public function index()
	{
		return View::create();
	}
}