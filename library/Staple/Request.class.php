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

use Exception;

class Request
{
	const METHOD_GET = 'GET';
	const METHOD_POST = 'POST';
	const METHOD_PUT = 'PUT';
	const METHOD_PATCH = 'PATCH';
	const METHOD_DELETE = 'DELETE';
	const METHOD_OPTIONS = 'OPTIONS';

	protected static $inst;

	/** @var string */
	protected $uri;
	protected $server_port;
	protected $remote_port;
	protected $path;

	/** @var string */
	protected $method;
	protected static $secure;
	
	protected $referrer;
	protected $userAgent;
	protected $IP;
	
	protected $auth;
	
	protected function __construct()
	{
		$this->setUri($_SERVER['REQUEST_URI'] ?? '');
		$this->server_port = $_SERVER['SERVER_PORT'] ?? null;
		$this->remote_port = $_SERVER['REMOTE_PORT'] ?? null;
		$this->setMethod($_SERVER['REQUEST_METHOD'] ?? self::METHOD_GET);
		$this->path = trim($_SERVER['PATH_INFO'] ?? '');
		$this->IP = $_SERVER['REMOTE_ADDR'] ?? null;
		$this->userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
		$this->referrer = $_SERVER['HTTP_REFERER'] ?? null;
		$this->isSecure();
		$this->auth = [
			'basicuser' => $_SERVER['PHP_AUTH_USER'] ?? null,
			'basicpw' => $_SERVER['PHP_AUTH_PW'] ?? null,
			'digest' => $_SERVER['PHP_AUTH_DIGEST'] ?? null
		];
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

	/**
	 * @return Request
	 */
	public static function get()
	{
		if (!isset(self::$inst)) 
		{
	            $c = __CLASS__;
	            self::$inst = new $c();
	        }
		return self::$inst;
	}

	/**
	 * Make a fake request object for testing. Should be refactored to an interface and new object in the future.
	 * @param string $uri
	 * @param string $method
	 * @return Request
	 */
	public static function fake($uri, $method)
	{
		self::$inst = new self();
		self::$inst->setUri($uri);
		self::$inst->setMethod($method);
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

	/**
	 * Grab the contents of the POST body.
	 * @return bool|string
	 */
	public static function BodyContent()
	{
		return file_get_contents('php://input');
	}

	/**
	 * Grab the content of the request and JSON decode it to a PHP object or array.
	 * @return \stdClass|array|null
	 */
	public static function JsonContent()
	{
		$post = self::BodyContent();
		return ($post === false) ? null : json_decode($post);
	}

	/**
	 * @return string
	 */
	public function getUri()
	{
		return $this->uri;
	}

	/**
	 * @param string $uri
	 * @return Request
	 */
	protected function setUri(string $uri)
	{
		$this->uri = $uri;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getMethod()
	{
		return $this->method;
	}

	/**
	 * @param string $method
	 */
	protected function setMethod(string $method)
	{
		$this->method = $method;
	}
}