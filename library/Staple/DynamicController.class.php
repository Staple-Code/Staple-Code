<?php
/** 
 * This controller is designed to react to input without predefined actions attached to it.
 * For every inaccessible action that is called the dynamo() function is called instead. Thus,
 * there will be no "Page Not Found" errors when calling this controller with any user specified
 * action. The first parameter will be the action that was called. The remaining params will be
 * any paramenters sent to the action.
 * 
 * @author Ironpilot
 * @copyright Copywrite (c) 2011, STAPLE CODE
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
abstract class Staple_DynamicController extends Staple_Controller
{
	public function __call($action, $arguments)
	{
		$this->view->setView($action);
		array_unshift($arguments, $action);
		call_user_func_array(array($this,"dynamo"), $arguments);
	}
	
	abstract public function dynamo();
}
?>