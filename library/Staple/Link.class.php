<?php

/** 
 * This class manages url links between controllers and actions.
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
namespace Staple;

use \Exception;

class Link
{
	protected $link;
	
	protected static $upper = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
	
	protected static $lower = array('-a','-b','-c','-d','-e','-f','-g','-h','-i','-j','-k','-l','-m','-n','-o','-p','-q','-r','-s','-t','-u','-v','-w','-x','-y','-z');
	
	public function __construct($route = NULL, array $get = array())
	{
		if(isset($route))
		{
			$this->link = self::get($route,$get);
		}
	}
	
	public function __toString()
	{
		return $this->link;
	}
	
	public static function Create($route, array $get = array())
	{
		return new static($route, $get);
	}
	
	/**
	 * Creates the shortest link possible to the specified controller/action. 
	 * The array must include a controller, followed by an action, followed by any parameters
	 * sent to the action as unique items in the array. Parameters may only be of type string
	 * or int.
	 * 
	 * @param mixed $route
	 * @param array $get
	 * @return string
	 */
	public static function get($route, array $get = array())
	{
		//Convert Get array to get string.
		$getString = self::getArraytoString($get);
		
		//Set the link base
		$base = Config::getValue('application', 'public_location');
		
		//Is the link an array or a string?
		if(!is_array($route))
		{
			//Return a string link.
			$link = $base.$route;
		}
		else
		{
			//Process and Controller/Action/Parameter route
			
			//Setup the default link and the case-insensitive replacements.
			$link = '#';
			
			
			
			//Count the route elements
			$routesize = count($route);
			if($routesize == 0)
			{
				$link = $base;			//An empty array returns a base link.
			}
			elseif($routesize == 1)
			{
				if(ctype_alnum((string)$route[0]))
				{
					$controller = (string)$route[0];
					if($controller == 'index')
					{
						$link = $base;
					}
					else
					{
						$controller = self::urlCase($controller);
						$link = $base.$controller;
					}
				}
				else
				{
					throw new Exception('Bad Link',Error::LINK_ERROR);
				}
			}
			else
			{
				//Extract the Controller, Action and Parameters.
				$controller = (string)array_shift($route);
				$action = (string)array_shift($route);
				//URL Encode parameter values.
				$params = array();
				foreach($route as $value)
				{
					$params[] = urlencode($value);
				}
				
				//Check that the route follows valid syntax are valid.
				if(ctype_alnum($controller) && ctype_alnum($action))
				{
					if($controller == 'index' && $action == 'index' && $params == array())
					{
						$link = $base;
					}
					elseif($controller != 'index' && $action == 'index' && $params == array())
					{
						$link = $base.$controller;
					}
					else 
					{
						if(count($params) > 0)
						{
							$paramstring = '/'.implode('/', $params);
						}
						else
						{
							$paramstring = '';
						}
						//Convert action to case-insensitive value
						$controller = self::urlCase($controller);
						$action = self::urlCase($action);
						
						$link = $base.$controller.'/'.$action.$paramstring;
					}
				}
				else
				{
					throw new Exception('Bad Link',Error::LINK_ERROR);
				}
			}
		}
		
		//Finally append the get string
		if(strlen($getString) > 2)
		{
			$link .= '?'.$getString;
		}
		
		//Return the link.
		return $link;
	}
	
	/**
	 * Creates a URL case-insensitive version of the supplied string.
	 * 
	 * @param string $url
	 * @return string;
	 */
	public static function urlCase($url)
	{
		return str_replace(self::$upper, self::$lower, $url);
	}
	
	/**
	 * Converts a url controller or action name back to the case sensitive version.
	 * 
	 * @param string $method
	 * @return string
	 */
	public static function methodCase($method)
	{
		return str_replace(self::$lower, self::$upper, $method);
	}
	
	/**
	 * Converts a get array key/value pairset to a get string.
	 * 
	 * @param array $get
	 * @return string
	 */
	public static function getArraytoString(array $get)
	{
		$getString = '';
		foreach($get as $gkey=>$gvalue)
		{
			$getString .= urlencode($gkey).'='.urlencode($gvalue).'&';
		}
		if(substr($getString, strlen($getString)-1,1) == '&')
		{
			$getString = substr($getString,0,strlen($getString)-1);
		}
		return $getString;
	}
}

?>