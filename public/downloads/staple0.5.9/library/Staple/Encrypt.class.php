<?php
/**
 * Encryption class. This class is incomplete.
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
class Staple_Encrypt
{	
	const AES = MCRYPT_RIJNDAEL_256;
	
	public static function MD5($encrypt)
	{
		return md5($encrypt);
	}
	
	public static function SHA1($encrypt)
	{
		return sha1($encrypt);
	}
	
	public static function Encrypt()
	{
		
	}
	public static function Decrypt()
	{
		
	}
	
	public static function AES_encrypt($encrypt, $key, $salt = '', $pepper = '', $iv = NULL)
	{
		if($iv == NULL)
		{
    		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
    		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		}
    	
    	//Add salt and pepper
    	$encstring = $pepper.$encrypt.$salt;

    	return mcrypt_encrypt(self::AES, $key, $encstring, MCRYPT_MODE_ECB, $iv);
	}
	
	public static function AES_decrypt($decrypt, $key, $salt = '', $pepper = '', $iv = NULL)
	{
		if($iv == NULL)
		{
    		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
    		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		}
		
		//To correctly detect string length we trim the output.
    	$encstring = trim(mcrypt_decrypt(self::AES, $key, $decrypt, MCRYPT_MODE_ECB, $iv));
    	
    	//Remove salt and pepper
    	$start = strlen($pepper);
    	$end = strlen($encstring)-strlen($salt)-strlen($pepper);
    	$encstring = substr($encstring, $start, $end);
		return $encstring;
	}
	
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
}