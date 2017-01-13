<?php
/**
 * Encryption class.
 * Default mode is MYCRYPT__MODE_ECB.
 * @todo Move to OpenSSL extension
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

use Staple\Exception\EncryptionException;

class Encrypt
{
	const AES = 'AES-128-CBC';
	const AES256 = 'AES-256-CBC';

	/**
	 * Simple MD5 hashing function
	 * @param string $hash
	 * @return string
	 */
	public static function md5($hash)
	{
		return md5($hash);
	}

	/**
	 * Simple SHA1 function
	 * @param string $hash
	 * @return string
	 */
	public static function sha1($hash)
	{
		return sha1($hash);
	}

	/**
	 * Simple SHA256 Hasher
	 * @param string $hash
	 * @return string
	 */
	public static function sha256($hash)
	{
		return hash('sha256', $hash);
	}

	/**
	 * Encrypt data with AES
	 * @param string $encrypt
	 * @param string $key
	 * @param string $cipher
	 * @param string $salt
	 * @param string $pepper
	 * @param string $nonce
	 * @param int $options
	 * @return string
	 * @throws EncryptionException
	 */
	public static function encrypt($encrypt, $key, $cipher = self::AES, $salt = '', $pepper = '', string $nonce = '', int $options = 0)
	{
		//Check for OpenSSL extension
		if(!function_exists('openssl_encrypt'))
			throw new EncryptionException('OpenSSL Module not loaded.');

		//Check for cipher existence
		$availableMethods = openssl_get_cipher_methods();
		if(in_array($cipher, $availableMethods) === false)
			throw new EncryptionException('Encryption cipher is not available on this system.');

		//Add salt and pepper
		$encryptString = $pepper . $encrypt . $salt;

		return openssl_encrypt($encryptString, $cipher, $key, $options, $nonce);
	}

	/**
	 * Decrypt data using specified algorithm
	 * @param string $decrypt
	 * @param string $key
	 * @param string $cipher
	 * @param string $salt
	 * @param string $pepper
	 * @param string $nonce
	 * @param int $options
	 * @return string
	 * @throws EncryptionException
	 */
	public static function decrypt($decrypt, $key, $cipher = self::AES, $salt = '', $pepper = '', $nonce = NULL, int $options = 0)
	{
		//Check for OpenSSL extension
		if(!function_exists('openssl_decrypt'))
			throw new EncryptionException('OpenSSL Module not loaded.');

		//Check for cipher existance
		$availableMethods = openssl_get_cipher_methods();
		if(in_array($cipher, $availableMethods) === false)
			throw new EncryptionException('Encryption cipher is not available on this system.');

		//To correctly detect string length we trim the output.
		$decryptString = openssl_decrypt($decrypt, $cipher, $key, $options, $nonce);

		//Remove salt and pepper
		$start = strlen($pepper);
		$end = strlen($decryptString) - strlen($salt) - strlen($pepper);
		return substr($decryptString, $start, $end);
	}

	/**
	 * Generates a random hex value.
	 * @param int $length
	 * @return string
	 */
	public static function genHex($length = 16)
	{
		$hex = '';
		for ($i = 0; $i < $length; $i++)
		{
			$rnd = dechex(rand(0, 16));
			$hex .= $rnd;
		}
		return $hex;
	}

	/**
	 * Converts a string to a hex value
	 * @param string $str
	 * @return string
	 */
	public static function hexstr($str)
	{
		$arr = unpack("H*", $str);
		return $arr[1];
	}

	/**
	 * Converts a hexed string to a normal string
	 * @param $str
	 * @return string
	 */
	public static function strhex($str)
	{
		return pack("H*", $str);
	}
}