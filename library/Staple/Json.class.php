<?php
/**
 * A class for returning JSON strings from actions or routes.
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


use Staple\Traits\Factory;
use \Exception;

class Json implements \JsonSerializable
{
	use Factory;

	const SUCCESS = 'success';
	const ERROR = 'error';
	const DEFAULT_SUCCESS_CODE = 200;
	const DEFAULT_AUTH_ERROR_CODE = 403;
	const DEFAULT_ERROR_CODE = 500;

	protected $properties = [];

	/**
	 * Allows dynamic setting of properties
	 * @param string $name
	 * @param mixed $value
	 * @throws Exception
	 */
	public function __set($name, $value)
	{
		//Set the property dynamically
		$this->properties[$name] = $value;
	}

	/**
	 * Allows dynamic calling of properties
	 * @param string $name
	 * @throws Exception
	 * @return mixed
	 */
	public function __get($name)
	{
		return $this->properties[$name] ?? null;
	}

	/**
	 * Return the set status of the dynamic properties
	 * @param $name
	 * @return bool
	 */
	public function __isset($name)
	{
		return isset($this->properties[$name]);
	}

	/**
	 * Unset a dynamic property
	 * @param $name
	 */
	public function __unset($name)
	{
		if(isset($this->properties[$name]))
			unset($this->properties[$name]);
	}

	/**
	 * Convert the object to a JSON string
	 * @return string
	 */
	public function __toString()
	{
		return json_encode($this->jsonSerialize());
	}

	/**
	 * Returns an object to serialize via JSON.
	 * @return \stdClass
	 */
	function jsonSerialize()
	{
		$obj = new \stdClass();
		foreach($this->properties as $key=>$value)
		{
			$obj->$key = $value;
		}
		return $obj;
	}


	/**
	 * Encode the parameters as a JSend response: https://labs.omniti.com/labs/jsend
	 * @param string $status
	 * @param mixed $data
	 * @param string $message
	 * @param int $code
	 * @return string
	 */
	public static function JSend(string $status = self::SUCCESS, $data = NULL, string $message = NULL, int $code = self::DEFAULT_SUCCESS_CODE)
	{
		http_response_code($code);
		$obj = new \stdClass();
		$obj->status = $status;
		if(isset($data)) $obj->data = $data;
		if(isset($message)) $obj->message = $message;
		return json_encode($obj);
	}

	/**
	 * Return a successful JSON response and set the HTTP response code
	 * @param mixed $data
	 * @param int $code
	 * @return null|string
	 */
	public static function success($data = NULL, int $code = self::DEFAULT_SUCCESS_CODE)
	{
		return self::response($data, $code);
	}

	/**
	 * Return a JSON encoded HTTP response and set the HTTP response code.
	 * @param mixed $data
	 * @param int $code
	 * @return null|string
	 */
	public static function response($data = NULL, int $code = self::DEFAULT_SUCCESS_CODE)
	{
		http_response_code($code);
		if($data === null)
		{
			return null;
		}
		else
		{
			return json_encode($data);
		}
	}

	/**
	 * Return an error response JSON object and set the HTTP response code. This includes
	 * a optional message and detail keys in the JSON object.
	 * @param string $message
	 * @param int $code
	 * @param mixed $details
	 * @return string
	 */
	public static function error($message = null, int $code = self::DEFAULT_ERROR_CODE, $details = null)
	{
		http_response_code($code);

		$obj = new \stdClass();
		if(isset($message)) $obj->message = $message;
		if(isset($details)) $obj->details = $details;
		return json_encode($obj);
	}

	/**
	 * Return an error response JSON object and set the HTTP response code. This includes
	 * a optional message and detail keys in the JSON object.
	 * @param string $message
	 * @param int $code
	 * @param mixed $details
	 * @return string
	 */
	public static function authError($message = null, int $code = self::DEFAULT_AUTH_ERROR_CODE, $details = null)
	{
		http_response_code($code);

		$obj = new \stdClass();
		if(isset($message)) $obj->message = $message;
		if(isset($details)) $obj->details = $details;
		return json_encode($obj);
	}
} 