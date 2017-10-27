<?php
/**
 * Encapsulate all the helper functions into a trait that we can apply to multiple classes.
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
namespace Staple\Traits;

use DateTime;
use Staple;
use Staple\Config;
use Staple\Dev;
use Staple\Link;

trait Helpers
{
	/**
	 * Generate a relative link within the framework.
	 * @param mixed $route
	 * @param array $get
	 * @return string
	 */
	public function link($route, array $get = array())
	{
		return Link::get($route,$get);
	}
	
	/**
	 * Function accepts a string and returns the htmlentities version of the string. The
	 * optional bool $strip will also return the string with HTML tags stripped.
	 * @param string $str
	 * @param bool $strip
	 * @return string
	 */
	public function escape($str, $strip = false)
	{
		if($str instanceof DateTime)
		{
			return htmlentities($str->format('Y-m-d H:i:s'));
		}
		elseif($strip === true)
		{
			return htmlentities(strip_tags($str));
		}
		elseif(is_array($str))
		{
			return implode('',$str);
		}
		else
		{
			return htmlentities($str);
		}
	}
	
	/**
	 * Encapsulate object instantiation to enable chaining. Given a class this function simply returns the class reference.
	 * @param object $obj
	 * @return object
	 */
	public static function with($obj)
	{
		return $obj;
	} 
	
	public static function dump()
	{
	    Dev::dump(func_get_args());
	}

	/**
	 * Returns a boolean if in development mode.
	 * @return bool
	 */
	protected static function isInDevMode()
	{
		return (bool)Config::getValue('errors','devmode');
	}
}