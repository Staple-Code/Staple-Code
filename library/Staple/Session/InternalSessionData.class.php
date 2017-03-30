<?php
/**
 * An object to hold data internal to the Staple framework operation in the
 * session.
 *
 * Configuration options [session]:
 * max_lifetime = 20 		The length in minutes that the session lives for.
 * handler = [class name]	The handler class that is used to handle the session.
 * 				Built in options: Staple\Session\DatabaseHandler,
 * 				Staple\Session\FileHandler, Staple\Session\RedisHandler
 *
 * @author Ironpilot
 * @copyright Copyright (c) 2016, STAPLE CODE
 *
 * This file is part of the STAPLE Framework.
 *
 * The STAPLE Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by the
 * Free Software Foundation, either version 3 of the License, or (at your option)
 * any later version.
 *
 * The STAPLE Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with the STAPLE Framework.  If not, see <http://www.gnu.org/licenses/>.
 */


namespace Staple\Session;

use Staple\Auth;
use Staple\Controller;
use stdClass;

class InternalSessionData
{
	/**
	 * The internal Auth object
	 * @var Auth
	 */
	protected $auth;
	/**
	 * Array of instantiated controllers
	 * @var Controller[]
	 */
	protected $controllers = [];
	/**
	 * Form Identities.
	 * @var stdClass
	 */
	protected $forms;
	/**
	 * The session registry array.
	 * @var array
	 */
	protected $registry = [];

	public function __construct()
	{
		$this->forms = new stdClass();
	}

	/**
	 * Retrieve form identity value
	 * @param string $formName
	 * @return string|null
	 */
	public function getFormIdentity($formName)
	{
		return $this->forms->$formName ?? NULL;
	}

	/**
	 * Set form identity value
	 * @param string $formName
	 * @param string $identity
	 * @return string|null
	 */
	public function setFormIdentity($formName, $identity)
	{
		$this->forms->$formName = $identity;
		return $identity;
	}

	/**
	 * Clear the form identity
	 * @param string $formName
	 * @return bool
	 */
	public function clearFormIdentity($formName)
	{
		if(isset($this->forms->$formName))
		{
			$this->forms->$formName = null;
			unset($this->forms->$formName);
		}
		return true;
	}

	/**
	 * Check for form submission.
	 * @param string $formName
	 * @param string $hash
	 * @return bool
	 */
	public function checkFormIdentity($formName, $hash)
	{
		if(isset($this->forms->$formName))
			if($this->forms->$formName == $hash)
				return true;
		return false;
	}

	/**
	 * Return a controller object or null;
	 * @param string $name
	 * @return Controller
	 */
	public function getController($name)
	{
		return $this->controllers[$name] ?? NULL;
	}

	/**
	 * Registers a controller with the session that was previously instantiated.
	 * @param Controller $controller
	 * @return Controller
	 */
	public function registerController(Controller $controller) : Controller
	{
		$class_name = strtolower(substr(get_class($controller),0,strlen(get_class($controller))-10));
		if(!array_key_exists($class_name, $this->controllers))
		{
			$this->controllers[$class_name] = $controller;
		}
		return $controller;
	}

	/**
	 * Set the Auth Object
	 * @param Auth $auth
	 * @return Auth
	 */
	public function setAuth(Auth $auth)
	{
		$this->auth = $auth;
		return $auth;
	}

	/**
	 * Get the Auth Object
	 * @return Auth
	 */
	public function getAuth()
	{
		return $this->auth;
	}

	/**
	 * Set a register value.
	 * @param string $key
	 * @param mixed $value
	 * @return mixed
	 */
	public function setRegistryKey($key, $value)
	{
		$this->registry[$key] = $value;
		return $value;
	}

	/**
	 * Retrieve a registered value.
	 * @param string $key
	 * @return mixed
	 */
	public function getRegistryKey($key)
	{
		return $this->registry[$key] ?? NULL;
	}
}