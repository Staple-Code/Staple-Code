<?php

/** 
 * A development class for troubleshooting
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
 * 
 */
namespace Staple;

class Dev
{
	//Timer - @todo make this into it's own class.
	protected static $timer;
	
	/**
	 * This function takes multiple arguments and dumps them to the source code.
	 */
	public static function dump()
	{
		if(class_exists('\Staple\Config'))
		{
			if(Config::getValue('errors', 'devmode') == 1)
			{
				$args = func_get_args();
				echo "<pre>";
				foreach($args as $dumper)
				{
					var_dump($dumper);
					echo "\n";
				}
				echo "</pre>";
			}
		}
		else
		{
			$args = func_get_args();
			echo "<pre>";
			foreach($args as $dumper)
			{
				var_dump($dumper);
				echo "\n";
			}
			echo "</pre>";
		}
	}
	
	/**
	 * Starts a script timer.
	 */
	public static function startTimer()
	{
		self::$timer = microtime(true);
	}
	
	/**
	 * Stops a previously started timer.
	 */
	public static function stopTimer()
	{
		return microtime(true) - self::$timer;
	}
}
?>