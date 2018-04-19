<?php
/**
 * This class has helpers for making RESTful calls to web services.
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

namespace Staple\Rest;

use Curl\Curl;
use Staple\Exception\RestException;

class Rest
{
	const GET = "GET";
	const POST = "POST";
	const PUT = "PUT";
	const HEAD = "HEAD";
	const DELETE = "DELETE";
	const OPTIONS = "OPTIONS";
	const TRACE = "TRACE";
	protected static $restLog = array();
	/**
	 * Return an array of the supported SSL cipher list.
	 * @return array
	 */
	public static function getCipherArray()
	{
		return [
			'ECDHE-ECDSA-AES256-GCM-SHA384',
			'ECDHE-RSA-AES256-GCM-SHA384',
			'ECDHE-ECDSA-AES128-GCM-SHA256',
			'ECDHE-RSA-AES128-GCM-SHA256',
			'ECDHE-ECDSA-AES256-SHA384',
			'ECDHE-RSA-AES256-SHA384',
			'ECDHE-ECDSA-AES128-SHA256',
			'ECDHE-RSA-AES128-SHA256',
		];
	}
	private static function getCurlObject()
	{
		$curl = new Curl();
		$curl->setOpt(CURLOPT_SSLVERSION, 'CURL_SSLVERSION_TLSv1_2');
		$curl->setOpt(CURLOPT_SSL_CIPHER_LIST, implode(':',self::getCipherArray()));
		$curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);
		$curl->setOpt(CURLOPT_SSL_VERIFYHOST, 2);
		$curl->setOpt(CURLOPT_CAINFO, FOLDER_ROOT.DIRECTORY_SEPARATOR.'certificate'.DIRECTORY_SEPARATOR.'openssl-cafile.pem');
		return $curl;
	}
	/**
	 * Add a Curl object to the rest log.
	 * @param Curl $curl
	 * @return int
	 */
	protected static function addToLog(Curl $curl)
	{
		return array_push(self::$restLog,$curl);
	}
	/**
	 * Get the array of rest calls.
	 * @return array
	 */
	public static function getRestLog()
	{
		return self::$restLog;
	}
	/**
	 * Get the object from the last request.
	 * @return mixed
	 */
	public static function getLastRequest()
	{
		$log = array_pop(self::$restLog);
		array_push(self::$restLog,$log);
		return $log;
	}
	public static function addHeadersToRequest(Curl $curl, array $headers = [])
	{
		foreach($headers as $key=>$value)
		{
			$curl->setHeader($key,$value);
		}
		return $curl;
	}
	/**
	 * Encapsulated GET call via cURL
	 * @param $url
	 * @param array $data
	 * @param array $headers
	 * @return RestResponse|null
	 * @throws RestException
	 * @throws \ErrorException
	 */
	public static function get($url, $data = array(), array $headers = [])
	{
		//New Curl object
		$curl = self::getCurlObject();
		//Add the additional headers, if any
		self::addHeadersToRequest($curl,$headers);
		//Execute the GET request
		$curl->get($url, $data);
		//Log the object
		self::addToLog($curl);
		//Search for an error or return results.
		if($curl->error === true) {
			throw new RestException('Message: '.$curl->error_message.' Code: '.$curl->error_code);
		} else {
			if($curl->response != NULL && $curl->response != '') {
				if($curl->http_status_code = 200) {
					return $curl->response;
				} else {
					return new RestResponse($curl->http_status_code, $curl->response, $curl->response_headers);
				}
			} else {
				throw new RestException('No response was received from the server.');
			}
		}
	}
	public static function post()
	{
		//@todo incomplete function
	}
	/**
	 * Encapsulated PUT call via cURL
	 * @param $url
	 * @param $data
	 * @param array $headers
	 * @return RestResponse|null
	 * @throws RestException
	 */
	public static function put($url, $data, array $headers = [])
	{
		//New Curl object
		$curl = self::getCurlObject();
		//Add the additional headers, if any
		self::addHeadersToRequest($curl,$headers);
		//Execute the PUT request
		$curl->put($url, $data);
		//Log the object
		self::addToLog($curl);
		//Search for an error or return results.
		if($curl->error === true) {
			throw new RestException('Message: '.$curl->error_message.' Code: '.$curl->error_code);
		} else {
			if($curl->response != NULL && $curl->response != '') {
				if($curl->http_status_code = 200) {
					return $curl->response;
				} else {
					return new RestResponse($curl->http_status_code, $curl->response, $curl->response_headers);
				}
			} else {
				return NULL;
			}
		}
	}
}
