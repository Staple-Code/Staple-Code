<?php

/** 
 * @author Ironpilot
 * 
 * 
 */
trait Staple_Singleton
{
	private static $inst;
	
	public static function getInstance()
	{
		//@todo finish this function
		return static::$inst;
	}
}

?>