<?php

/**
 * Encapsulate all the helper functions into a trait that we can apply to multiple classes.
 * PHP 5.4 Functionality
 * @author ironpilot
 *        
 */
namespace Staple\Traits;

use \DateTime, Staple, Staple\Link, Staple\Dev;

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
}