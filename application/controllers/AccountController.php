<?php

use Staple\Auth\Auth;
use Staple\Controller\Controller;
use Staple\Route;
use Staple\View;

/** 
 * @author Ironpilot
 * @copyright Copyright (c) 2011, STAPLE CODE
 *  
 * 
 * This file is part of the STAPLE Framework.
 * 
 * The STAPLE Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by the 
 * Free Software Foundation, either version 3 of the License, or (at your option)
 * any later version.
 * 
 * The STAPLE Framework is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public License for 
 * more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with the STAPLE Framework.  If not, see <http://www.gnu.org/licenses/>.
 * 
 */
class AccountController extends Controller
{
	/**
	 * @protected
	 * @return View
	 */
	public function index()
	{
		return View::create()->data([
			'message' => Auth::get()->getMessage()
		]);
	}

	/**
	 * @return Route
	 */
	public function signin()
	{
		$username = $_POST['user'] ?? null;
		$password = $_POST['pass'] ?? null;
		
		$auth = Auth::get();
		$granted = $auth->doAuth([
				'username'=>$username,
				'password'=>$password,
				]);
		if($granted === true)
		{
			return Route::create('index/index');
		}
		else
		{
			return Route::create('account/index');
		}
	}
}