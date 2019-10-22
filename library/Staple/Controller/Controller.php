<?php
/**
 * The controller object handles user actions.
 * 
 * @author Ironpilot
 * @copyright Copyright (c) 2011, STAPLE CODE
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
 */
namespace Staple\Controller;

use Staple\Auth\Auth;
use Staple\Auth\AuthHelpers;
use Staple\Autoload;
use Staple\Config;
use Staple\Error;
use Staple\Exception\AuthException;
use Staple\Exception\ConfigurationException;
use Staple\Exception\RoutingException;
use Staple\Layout;
use Staple\Route;
use Staple\Traits\Helpers;
use ReflectionException, ReflectionMethod, ReflectionClass;

abstract class Controller
{
	use Helpers;
	use AuthHelpers;

	/**
	 * Holds and instance of a Staple Layout object.
	 * @var Layout
	 */
	public $layout;
	
	/**
	 * 
	 * Controller constructor creates an instance of View and saves it in the $view
	 * property. It then calls the overridable method _start() for additional boot time
	 * procedures.
	 * @throws ConfigurationException
	 */
	public function __construct()
	{
		//Assign the default layout to the controller, if specified in config.
		$layout = Config::getValue('layout','default', false);
		if($layout != '')
		{
			$this->_setLayout($layout);
			$pageSettings = Config::get('page');
			if(array_key_exists('title', $pageSettings))
			{
				$this->layout->setTitle($pageSettings['title']);
			}
		}
	}
	
	/**
	 * Overridable and empty method allows for additional boot time procedures.
	 * This method is called by the front controller.
	 */
	public function _start()
	{
		
	}
	
	/*----------------------------------------Auth Functions----------------------------------------*/
	
	/**
	 * Returns a boolean true or false whether the method requires authentication
	 * before being dispatched from the front controller.
	 * @param string $method
	 * @return bool
	 * @throws AuthException
	 * @throws RoutingException
	 * @throws ReflectionException
	 */
	public function _auth($method)
	{
		$method = (string)$method;
		if(!ctype_alnum(str_ireplace('_', '', $method)))
		{
			throw new AuthException('Authentication Validation Error', Error::AUTH_ERROR);
		}
		else
		{
			if(method_exists($this,$method))
			{
				$auth = Auth::get();
				$reflectMethod = new ReflectionMethod($this, $method);
				$reflectClass = new ReflectionClass($this);
				$classComments = $reflectClass->getDocComment();
				$methodComments = $reflectMethod->getDocComment();

				//Auth Level
				$routeAuth = true;
				if(stripos($classComments, Auth::AUTH_FLAG_LEVEL) !== false)
				{
					$levelSplit = explode(Auth::AUTH_FLAG_LEVEL, $classComments);
					$eolSplit = explode($levelSplit[1], '\n');
					$authLevel = trim($eolSplit[0]);
					$routeAuth = $auth->authRoute(Route::create([str_ireplace(Autoload::CONTROLLER_SUFFIX, '', $reflectClass->getName()),$method]), $authLevel, $reflectClass, $reflectMethod);

				}
				elseif(stripos($methodComments, Auth::AUTH_FLAG_LEVEL) !== false)
				{
					$levelSplit = explode(Auth::AUTH_FLAG_LEVEL, $methodComments);
					$eolSplit = explode($levelSplit[1], '\n');
					$authLevel = trim($eolSplit[0]);
					$routeAuth = $auth->authRoute(Route::create([str_ireplace(Autoload::CONTROLLER_SUFFIX, '', $reflectClass->getName()),$method]), $authLevel, $reflectClass, $reflectMethod);
				}

				//Auth Protection
				if(stripos($classComments, Auth::AUTH_FLAG_PROTECTED) !== false)			//The entire Controller is protected.
				{
					if(stripos($methodComments, Auth::AUTH_FLAG_OPEN) !== false)					//Controller is protected but the method is open.
						return true;
					return $auth->isAuthed() && $routeAuth;
				}
				elseif(stripos($methodComments, Auth::AUTH_FLAG_PROTECTED) !== false)    //The method is protected.
				{
					return $auth->isAuthed() && $routeAuth;
				}
				return true;
			}
			else
			{
				throw new AuthException('Authentication Validation Error', Error::AUTH_ERROR);
			}
		}
	}
	
	/*----------------------------------------Layout Functions----------------------------------------*/

	/**
	 *
	 * Sets up a new layout object and associates the controllers view with the layout. Accepts a
	 * string name for the layout to load.
	 * @param string $layout
	 */
	protected function _setLayout($layout)
	{
		$this->layout = new Layout($layout);
	}

	/**
	 * Removes the layout object from the controller.
	 */
	protected function _removeLayout()
	{
		$this->layout = NULL;
	}
}