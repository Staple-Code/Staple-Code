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
	 * 
	 * Function accepts a string and returns the htmlentities version of the string. The
	 * optional bool $strip will also return the string with HTML tags stripped.
	 * @param string $estring
	 * @param bool $strip
	 */
	public static function escape($estring, $strip = false)
	{
		$estring = htmlentities($estring);
		if($strip === true)
		{
			$estring = strip_tags($estring);
		}
		return $estring;
	}
	
	/**
	 * This function allows you to disable the rendering of the view from the controller.
	 */
	public function noRender()
	{
		$this->_render = false;
	}
	
	/**
	 * 
	 * If an array is supplied, a link is created to a controller/action. If a string is
	 * supplied, a file link is specified.
	 * @param string | array $link
	 * @param array $get
	 */
	public function link($link,array $get = array())
	{
		return Staple_Link::get($link,$get);
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
			$theView = PROGRAM_ROOT.'views/'.$this->controller.'/'.$this->view.'.phtml';
			if(file_exists($theView))
			{
				include $theView;
			}
			//no else because views are optional.
		}
		else
		{
			//skip rendering of an additional views
			$this->_render = false;
		}
	}
}