<?php
/**
 * 
 * The view object. This object renders the view if found. It also provides functionality for
 * stripping tags and html escaping. It holds a dynamic data store for controllers to add items
 * to the view on the fly.
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
 */
class Staple_View 
{
	use Staple_Helpers;
	
	/**
	 * Whether or not to render the view. Set to false to skip view rendering.
	 * @var bool
	 */
	protected $_render = true;
	/**
	 * The dynamic datastore.
	 * @var array
	 */
	protected $_store = array();
	
	/**
	 * A string containing the name of the view to build.
	 * @var string
	 */
	protected $view;
	/**
	 * A string containing the name of the controller under which to look for the view.
	 * @var string
	 */
	protected $controller;
	
	/**
	 * Overloaded __set allows for dynamic addition of properties.
	 * @param string | int $key
	 * @param mixed $value
	 */
	public function __set($key,$value)
	{
		$this->_store[$key] = $value;
	}
	
	/**
	 * Retrieves a stored property.
	 * @param string | int $key
	 */
	public function __get($key)
	{
		if(array_key_exists($key,$this->_store))
		{
			return $this->_store[$key];
		}
		else
		{
			return NULL;
		}
	}
	
	/**
	 * 
	 * Don't save any data in the session.
	 */
	public function __sleep()
	{
		return array();
	}
	
	/**
	 * Converts the object into a string by calling the build() method.
	 * @return string
	 */
	public function __toString()
	{
		try {
			ob_start();
			$this->build();
			$buffer = ob_get_contents();
			ob_end_clean();
			return $buffer;
		}
		catch (Exception $e)
		{
			$msg = '<p class=\"viewerror\">The View threw an Uncaught Exception when converting to a string....</p>';
			if(Staple_Config::getValue('errors', 'devmode'))
			{
				$msg .= '<p>'.$e->getMessage().'</p>';
			}
			return $msg;
		}
		
	}
	
	/**
	 * This function allows you to disable the rendering of the view from the controller.
	 */
	public function noRender()
	{
		$this->_render = false;
	}
	
	/**
	 * Sets the view string
	 * @param string $view
	 */
	public function setView($view)
	{
		$this->view = $view;
		return $this;
	}
	/**
	 * Returns the view string
	 * @return string
	 */
	public function getView()
	{
		return $this->view;
	}
	/**
	 * Returns isset on the $view parameter
	 * @return boolean
	 */
	public function hasView()
	{
		return isset($this->view);
	}
	/**
	 * Sets the controller string
	 * @param string $controller
	 */
	public function setController($controller)
	{
		$this->controller = $controller;
		return $this;
	}
	/**
	 * Returns the controller string.
	 * @return string
	 */
	public function getController()
	{
		return $this->controller;
	}
	/**
	 * Returns isset on the $controller parameter.
	 * @return boolean
	 */
	public function hasController()
	{
		return isset($this->controller);
	}
	
	/**
	 * 
	 * This function renders the view. If accepts a string representing the controller and
	 * a string representing the requested action. With this information the correct view
	 * is selected and rendered.
	 * @param string $class
	 * @param string $view
	 */
	public function build()
	{
		if($this->_render === true)
		{
			//Load the view from the default loader
			$view = Staple_Main::get()->getLoader()->loadView($this->controller,$this->view);
			if(strlen($view) >= 1)
			{
				include $view;
			}
		}
		else
		{
			//skip rendering of an additional views
			$this->_render = false;
		}
	}
}