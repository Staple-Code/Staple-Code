<?php
/**
 * Encryption class. This class is incomplete.
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
class Staple_Encrypt
{	
	public static function MD5($encrypt)
	{
		return md5($encrypt);
	}
	
	public static function SHA1($encrypt)
	{
		return sha1($encrypt);
	}
	
	public static function AES($encrypt)
	{
		$enc = $this->loadEncrypt();
    	$iv = $enc['AES_IV'];
    	$key = $enc['AES_Key'];

    	return mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $encrypt, MCRYPT_MODE_ECB, $iv);
	}
	
	protected function loadEncrypt()
	{
		if(file_exists(CONFIG_ROOT.'application.ini'))
		{
			$enc = parse_ini_file(CONFIG_ROOT.'application.ini',true);
			if($this->checkConfig($enc['encrypt']))
			{
				return $enc['encrypt'];
			}
		}
		elseif (file_exists(CONFIG_ROOT.'encrypt.ini'))
		{
			$enc = parse_ini_file(CONFIG_ROOT.'encrypt.ini');
			if($this->checkConfig($enc))
			{
				return $enc;
			}
		}
		else
		{
			throw new Exception('Encryption Module Failure');
		}
	}
	
	private function checkConfig($conf)
	{
		if(array_key_exists('AES_Key', $conf) AND array_key_exists('AES_IV', $conf))
		{
			return true;
		}
		else
		{
			throw new Exception('Encryption Module Failure');
		}
	}
}