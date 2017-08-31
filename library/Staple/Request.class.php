<?php
/**
 * @todo This class obviously needs work.
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
namespace Staple;

use \Exception;

class Request
{
	const METHOD_GET = 'GET';
	const METHOD_POST = 'POST';
	const METHOD_PUT = 'PUT';
	const METHOD_PATCH = 'PATCH';
	const METHOD_DELETE = 'DELETE';
	const METHOD_OPTIONS = 'OPTIONS';

	protected static $inst;
	
	protected $uri;
	protected $sport;
	protected $rport;
	protected $path;
	
	protected $method;
	protected static $secure;
	
	protected $referrer;
	protected $userAgent;
	protected $IP;
	
	protected $auth;
	
	public function __construct()
	{
		$this->uri = $_SERVER['REQUEST_URI'];
		$this->sport = $_SERVER['SERVER_PORT'];
		$this->rport = $_SERVER['REMOTE_PORT'];
		$this->method = $_SERVER['REQUEST_METHOD'];
		if(array_key_exists('PATH_INFO', $_SERVER))
		{
			$this->path = trim($_SERVER['PATH_INFO']);
		}
		$this->IP = $_SERVER['REMOTE_ADDR'];
		$this->userAgent = $_SERVER['HTTP_USER_AGENT'];
		if(array_key_exists('HTTP_REFERER', $_SERVER))
		{
			$this->referrer = $_SERVER['HTTP_REFERER'];
		}
		$this->isSecure();
		$this->auth = array();
		if(array_key_exists('PHP_AUTH_USER', $_SERVER))
			$this->auth['basicuser'] = $_SERVER['PHP_AUTH_USER'];
		if(array_key_exists('PHP_AUTH_PW', $_SERVER))			 
			$this->auth['basicpw'] = $_SERVER['PHP_AUTH_PW'];
		if(array_key_exists('PHP_AUTH_DIGEST', $_SERVER)) 
			$this->auth['digest'] = $_SERVER['PHP_AUTH_DIGEST'];
	}
	
	public function __get($name)
	{
		$method = 'get' . $name;
		if (!method_exists($this, $method))
		{
			throw new Exception('Request Object Not Found');
		}
		return $this->$method();
	}
	
	public function Get()
	{
		if (!isset(self::$inst)) {
            $c = __CLASS__;
            self::$inst = new $c();
        }
		return self::$inst;
	}
	
	/**
	 * Checks the PHP $_SERVER var for the presence of HTTPS. Returns a boolean.
	 * @return boolean
	 */
	public static function isSecure()
	{
		if(!isset(self::$secure))
		{
			if(array_key_exists('HTTPS', $_SERVER))
			{
				if($_SERVER['HTTPS'] == 'on')
				{
					self::$secure = true;
				}
				else
				{
					self::$secure = false;
				}
			}
			elseif(array_key_exists('SERVER_PORT', $_SERVER))
			{
				if($_SERVER['SERVER_PORT'] == '443')
				{
					self::$secure = true;
				}
				else
				{
					self::$secure = false;
				}
			}
			else
			{
				self::$secure = false;
			}
		}
		return (bool)self::$secure;
	}
	
	public function Path()
	{
		return $this->path;
	}
	
	public function URI()
	{
		return $this->URI();
	}
	
	public function IP()
	{
		return $this->IP;
	}
	
	public function Agent()
	{
		return $this->userAgent;
	}
	
	public static function getGETString(array $exclude = array())
	{
		$getstring = '';
		foreach($_GET as $key=>$value)
		{
			if(!in_array($key, $exclude))
			{
				if($getstring == '')
				{
					$getstring = urlencode($key).'='.urlencode($value);
				}
				else
				{
					$getstring .= '&'.urlencode($key).'='.urlencode($value);
				}
			}
		}
	}
	
	public static function Redirect(Route $route, array $get = array())
	{
		$to = (string)$route;
		if($get != array())
		{
			$to .= '?'.Link::getArrayToString($get);
		}
		header('Location: '.$to);
		exit(0);
	}
}