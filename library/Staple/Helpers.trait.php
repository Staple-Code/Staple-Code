<?php

/**
 * Encapsulate all the helper functions into a trait that we can apply to multiple classes.
 * PHP 5.4 Functionality
 * @author ironpilot
 *        
 */
trait Staple_Helpers
{
	/**
	 * Generate a relative link within the framework.
	 * @param mixed $route
	 * @param array $get
	 * @return string
	 */
	public function link($route, array $get = array())
	{
		return Staple_Link::get($route,$get);
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
			$estring = htmlentities(strip_tags($estring));
		}
		else
		{
			return htmlentities($str);
		}
	}
	
	/**
	 * Encapsulate object instantiation to enable chaining. Given a class this function simply returns the class reference.
	 * @param stdClass $obj
	 * @return stdClass
	 */
	public function with(stdClass $obj)
	{
		return $obj;
	} 
}

?>