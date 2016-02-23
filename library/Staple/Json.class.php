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

use \stdClass;

class Json implements \JsonSerializable
{
	const SUCCESS = 'success';
	const ERROR = 'error';
	const FAILURE = 'fail';
	/**
	 * The Json data.
	 * @var mixed
	 */
	private $data;
	/**
	 * HTTP status code of the return.
	 * @var int
	 */
	protected $code;
	/**
	 * Message for JSend
	 * @var string
	 */
	protected $message;
	/**
	 * JSend status message
	 * @var string
	 */
	protected $status;

	/**
	 * Specify data which should be serialized to JSON
	 *
	 * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return mixed data which can be serialized by <b>json_encode</b>,
	 * which is a value of any type other than a resource.
	 * @since 5.4.0
	 */
	public function jsonSerialize()
	{
		return $this->getData();
	}

	/**
	 * @return mixed
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * @param mixed $data
	 */
	public function setData($data)
	{
		$this->data = $data;
	}

	/**
	 * @return int
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * @param int $code
	 */
	public function setCode($code)
	{
		$this->code = $code;
	}

	/**
	 * @return Json
	 */
	public static function make()
	{
		return new static();
	}

	public function jsend($status, $data, $message, $responseCode)
	{

	}

	public static function error($message, $responseCode)
	{

	}

	/**
	 * @param stdClass $object
	 * @return Json
	 */
	public static function object(stdClass $object)
	{
		$json = self::make();
		$json->setData($object);
		return $json;
	}
} 