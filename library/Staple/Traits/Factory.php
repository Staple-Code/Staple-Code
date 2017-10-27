<?php
/**
 * Created by PhpStorm.
 * User: ironpilot
 * Date: 3/14/2015
 * Time: 7:31 PM
 */

namespace Staple\Traits;


trait Factory
{
	/**
	 * Factory Method
	 * @return static
	 */
	public static function make()
	{
		return new static();
	}

	/**
	 * Factory Method
	 * @return static
	 */
	public static function create()
	{
		return new static();
	}
}