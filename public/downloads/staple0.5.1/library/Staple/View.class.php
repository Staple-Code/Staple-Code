<?php
/**
 * 
 * The view object. This object renders the view if found. It also provides functionality for
 * stripping tags and html escaping. It holds a dynamic data store for controllers to add items
 * to the view on the fly.
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
class Staple_View
{
	/**
	 * 
	 * Whether or not to render the view. Set to false to skip view rendering.
	 * @var bool
	 */
	protected $_render = true;
	/**
	 * 
	 * The dynamic datastore.
	 * @var array
	 */
	protected $_store = array();
	
	public function __set($key,$value)
	{
		$this->_store[$key] = $value;
	}
	
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
	public function escape($estring, $strip = false)
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
		return Staple_Main::get()->link($link,$get);
	}
	
	/**
	 * 
	 * This function renders the view. If accepts a string representing the controller and
	 * a string representing the requested action. With this information the correct view
	 * is selected and rendered.
	 * @param string $class
	 * @param string $view
	 */
	public function build($class,$view)
	{
		if($this->_render === true)
		{
			$theView = PROGRAM_ROOT.'views/'.$class.'/'.$view.'.phtml';
			if(file_exists($theView))
			{
				include $theView;
			}
			else
			{
				//Remove this line to make the view optional.
				//echo "View Not Found: $theView</p>";
			}
		}
		else
		{
			//skip rendering of an additional views
			$this->_render = false;
		}
	}
}