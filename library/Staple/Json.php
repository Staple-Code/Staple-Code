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


use Exception;
use Staple\Traits\Factory;

class Json implements \JsonSerializable
{
	use Factory;

	const SUCCESS = 'success';
	const ERROR = 'error';
	const DEFAULT_SUCCESS_CODE = 200;
	const DEFAULT_AUTH_ERROR_CODE = 403;
	const DEFAULT_ERROR_CODE = 500;

	/**
	 * An array of dynamic properties that will be converted to a JSON object.
	 * @var array
	 */
	protected array $_properties = [];

	/**
	 * The data to encode for the JSON response.
	 * @var mixed
	 */
	protected mixed $_data;

	/**
	 * Flag for JSON_PRETTY_PRINT
	 * @var bool $pretty
	 */
	protected bool $pretty = false;

	/**
	 * Allows dynamic setting of properties
	 * @param string $name
	 * @param mixed $value
	 * @throws Exception
	 */
	public function __set(string $name, mixed $value): void
	{
		//Set the property dynamically
		$this->_properties[$name] = $value;
	}

	/**
	 * Allows dynamic calling of properties
	 * @param string $name
	 * @return mixed
	 * @throws Exception
	 */
	public function __get(string $name): mixed
	{
		return $this->_properties[$name] ?? null;
	}

	/**
	 * Allow Dynamic Setters and Getters
	 * @param string $name
	 * @param array $arguments
	 * @return mixed
	 * @throws Exception
	 */
	public function __call(string $name, array $arguments)
	{
		if(strtolower(substr($name, 0, 3)) == 'get')
		{
			$dataName = Utility::snakeCase(substr($name, 3));
			if(isset($this->_properties[$dataName]))
			{
				return $this->_data[$dataName];
			}
		}
		elseif(strtolower(substr($name, 0, 3)) == 'set')
		{
			$dataName = Utility::snakeCase(substr($name, 3));
			$this->_properties[$dataName] = array_shift($arguments);
			return $this;
		}

		throw new Exception(' Call to undefined method ' . $name);
	}


	/**
	 * Return the set status of the dynamic properties
	 * @param string $name
	 * @return bool
	 */
	public function __isset(string $name)
	{
		return isset($this->_properties[$name]);
	}

	/**
	 * Unset a dynamic property
	 * @param string $name
	 */
	public function __unset(string $name)
	{
		if(isset($this->_properties[$name]))
			unset($this->_properties[$name]);
	}

	/**
	 * Convert the object to a JSON string
	 * @return string
	 */
	public function __toString()
	{
		$flags = 0;
		if ($this->pretty)
		{
			$flags = JSON_PRETTY_PRINT;
		}
		return json_encode($this->jsonSerialize(), $flags);
	}

	/**
	 * Set the JSON_PRETTY_PRINT flag
	 * @param bool $pretty
	 */
	public function setPretty(bool $pretty): void
	{
		$this->pretty = $pretty;
	}

	/**
	 * Return the JSON_PRETTY_PRINT flag
	 * @return bool
	 */
	public function getPretty(): bool
	{
		return $this->pretty;
	}

	/**
	 * Returns an object to serialize via JSON.
	 * @return mixed
	 */
	function jsonSerialize(): mixed
	{
		if(isset($this->_data))
			return $this->_data;
		else
		{
			$obj = new \stdClass();
			foreach($this->_properties as $key => $value)
			{
				$obj->$key = $value;
			}
			return $obj;
		}
	}

	/**
	 * Set the HTTP Response code and add a property for the code in the response.
	 * @param int $code
	 * @param bool $addCodeToResponse
	 * @return Json
	 */
	public function setResponseCode(int $code, bool $addCodeToResponse = false): Json
	{
		http_response_code($code);
		if($addCodeToResponse)
			$this->code = $code;
		return $this;
	}

	/**
	 * Set the data/structure for the JSON object.
	 * @param $_data
	 * @return $this
	 */
	public function setData($_data): static
	{
		$this->_data = $_data;
		return $this;
	}

	/**
	 * Encode the parameters as a JSend response: https://labs.omniti.com/labs/jsend
	 * @param string $status
	 * @param mixed|null $data
	 * @param string|null $message
	 * @param int $code
	 * @return string
	 */
	public static function JSend(string $status = self::SUCCESS, mixed $data = NULL, string $message = NULL, int $code = self::DEFAULT_SUCCESS_CODE): string
	{
		$json = new static();
		$json->setResponseCode($code);
		$json->status = $status;
		if(isset($data)) $json->data = $data;
		if(isset($message)) $json->message = $message;
		return $json;
	}

	/**
	 * Return a successful JSON response and set the HTTP response code
	 * @param mixed|null $data
	 * @param int $code
	 * @return Json|string|null
	 */
	public static function success(mixed $data = NULL, int $code = self::DEFAULT_SUCCESS_CODE): Json|string|null
	{
		return self::response($data, $code);
	}

	/**
	 * Return a JSON-encoded HTTP response and set the HTTP response code.
	 * @param mixed|null $data
	 * @param int $code
	 * @return Json
	 */
	public static function response(mixed $data = NULL, int $code = self::DEFAULT_SUCCESS_CODE): Json
	{
		$json = new static();
		$json->setResponseCode($code);
		$json->_data = $data;
		return $json;
	}

	/**
	 * Return an error response JSON object and set the HTTP response code. This includes
	 * a optional message and detail keys in the JSON object.
	 * @param string|null $message
	 * @param int $code
	 * @param mixed|null $details
	 * @return string
	 */
	public static function error(string $message = null, int $code = self::DEFAULT_ERROR_CODE, mixed $details = null): string
	{
		$json = new static();
		$json->setResponseCode($code, true);
		if(isset($message)) $json->message = $message;
		if(isset($details)) $json->details = $details;
		return $json;
	}

	/**
	 * Return an error response JSON object and set the HTTP response code. This includes
	 * an optional message and detail keys in the JSON object.
	 * @param string|null $message
	 * @param int $code
	 * @param mixed|null $details
	 * @return string
	 */
	public static function authError(string $message = null, int $code = self::DEFAULT_AUTH_ERROR_CODE, mixed $details = null): string
	{
		return self::error($message, $code, $details);
	}
} 