<?php
use Staple\Auth;
use Staple\AuthController;

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
class accountController extends AuthController
{
	public function _start()
	{
		parent::_start();
		$this->_openMethod('signin');
	}
	public function index()
	{
		$this->view->message = Auth::get()->getMessage();
	}
	public function signin()
	{
		$username = $_POST['user'];
		$password = $_POST['pass'];
		
		$auth = Auth::get();
		$granted = $auth->doAuth(array(
				'username'=>$username,
				'password'=>$password,
				));
		if($granted === true)
		{
			$this->_redirect('index/index');
		}
		else
		{
			$this->_redirect('account/index');
		}
	}
}