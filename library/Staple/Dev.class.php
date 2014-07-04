<?php

/** 
 * A development class for troubleshooting
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
 * 
 */
class Staple_Dev
{
	protected static $timer;
	
	/**
	 * This function takes multiple arguments and dumps them to the source code.
	 */
	public static function Dump()
	{
		if(class_exists('Staple_Config'))
		{
			if(Staple_Config::getValue('errors', 'devmode') == 1)
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
	public static function StartTimer()
	{
		self::$timer = microtime(true);
	}
	
	/**
	 * Stops a previously started timer.
	 */
	public static function StopTimer()
	{
		return microtime(true) - self::$timer;
	}
	
	public static function GetRouteInfo()
	{
		$blah = Staple_Main::getRoute();
	} 
	
	/*public static function ResetController(Staple_Controller $controllerRef)
	{
		//$controllerRef::__destruct();
		unset($controllerRef);
	}
	public static function ResetAuth()
	{
		$auth = Staple_Auth::get();
		$auth->clearAuth();
		$auth::__destruct();
		unset($auth);
	}*/
}
?>