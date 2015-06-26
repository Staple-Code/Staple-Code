<?php
/**
 * Encryption class.
 * Default mode is MYCRYPT__MODE_ECB.
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

class Encrypt
{
	const AES = MCRYPT_RIJNDAEL_128;
    const AES256 = MCRYPT_RIJNDAEL_256;

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
		return hash('sha256',$hash);
	}
	
	/**
	 * Encrypt data with AES
	 * @param string $encrypt
	 * @param string $key
	 * @param string $cypher
	 * @param string $salt
	 * @param string $pepper
	 * @param string $iv
	 * @return string
	 */
	public static function encrypt($encrypt, $key, $cypher = MCRYPT_RIJNDAEL_128, $salt = '', $pepper = '', $iv = NULL)
    {
        if($iv == NULL)
        {
            $iv_size = mcrypt_get_iv_size($cypher, MCRYPT_MODE_ECB);
            $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        }

        //Add salt and pepper
        $encryptString = $pepper.$encrypt.$salt;

        return mcrypt_encrypt($cypher, $key, $encryptString, MCRYPT_MODE_ECB, $iv);
    }

    /**
     * @param $encrypt
     * @param $key
     * @param string $salt
     * @param string $pepper
     * @param null $iv
     * @return string
     * @deprecated
     */
	public static function AES_encrypt($encrypt, $key, $salt = '', $pepper = '', $iv = NULL)
	{
		return static::encrypt($encrypt, $key, MCRYPT_RIJNDAEL_128, $salt, $pepper, $iv);
	}
	
	/**
	 * Decrypt data using specified algorithm
	 * @param string $decrypt
	 * @param string $key
	 * @param string $cypher
	 * @param string $salt
	 * @param string $pepper
	 * @param string $iv
	 * @return string
	 */
	public static function decrypt($decrypt, $key, $cypher = MCRYPT_RIJNDAEL_128, $salt = '', $pepper = '', $iv = NULL)
    {
        if($iv == NULL)
        {
            $iv_size = mcrypt_get_iv_size($cypher, MCRYPT_MODE_ECB);
            $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        }

        //To correctly detect string length we trim the output.
        $decryptString = trim(mcrypt_decrypt($cypher, $key, $decrypt, MCRYPT_MODE_ECB, $iv));

        //Remove salt and pepper
        $start = strlen($pepper);
        $end = strlen($decryptString)-strlen($salt)-strlen($pepper);
		$decryptString = substr($decryptString, $start, $end);
        return $decryptString;
    }

    /**
     * Decrypt using AES 256
     * @param $decrypt
     * @param $key
     * @param string $salt
     * @param string $pepper
     * @param null $iv
     * @deprecated
     * @return string
     */
	public static function AES_decrypt($decrypt, $key, $salt = '', $pepper = '', $iv = NULL)
	{
		return static::decrypt($decrypt,$key, MCRYPT_RIJNDAEL_128,$salt,$pepper,$iv);
	}
	
	/**
	 * Generates a random hex value.
	 * @param int $length
	 * @return string
	 */
	public static function genHex($length = 16)
	{
		$hex = '';
		for ($i = 0; $i<$length; $i++)
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
		$arr = unpack("H*",$str);
		return $arr[1];
	}

	/**
	 * Converts a hexed string to a normal string
	 * @param $str
	 * @return string
	 */
	public static function strhex($str)
	{
		return pack("H*",$str);
	}
}