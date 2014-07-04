<?php
/**
 * Encryption class.
 * Default mode is MYCRYPT__MODE_ECB.
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
	const AES = MCRYPT_RIJNDAEL_128;
	const ECB = MCRYPT_MODE_ECB;
	const CBC = MCRYPT_MODE_CBC;
	const CFB = MCRYPT_MODE_CFB;
	const OFB = MCRYPT_MODE_OFB;
	const NOFB = MCRYPT_MODE_NOFB;
	const STREAM = MCRYPT_MODE_STREAM;
	
	/**
	 * The cypher to be used during encryption.
	 * @var string
	 */
	private $cypher;
	/**
	 * Salt to added to/removed from the end of the encrypted value.
	 * @var string
	 */
	private $salt;
	/**
	 * Pepper to be added to/removed from the beginning of the encrypted value.
	 * @var string
	 */
	private $pepper;
	/**
	 * PHP MCRYPT mode. Should be one of the predefined mode constants for PHP. Defaults to MCRYPT_MODE_ECB.
	 * @var string
	 */
	private $mode = MCRYPT_MODE_ECB;
	/**
	 * Encryption key
	 * @var string
	 */
	private $key;
	
	/**
	 * Simple MD5 hashing function
	 * @param string $encrypt
	 * @return string
	 */
	public static function MD5($hash)
	{
		return md5($hash);
	}
	
	/**
	 * Simple SHA1 function
	 * @param string $encrypt
	 * @return string
	 */
	public static function SHA1($hash)
	{
		return sha1($hash);
	}
	
	/**
	 * Simple SHA256 Hasher
	 * @param string $hash
	 * @return string
	 */
	public static function SHA256($hash)
	{
		return hash('sha256',$hash);
	}
	
	/**
	 * @return the $cypher
	 */
	public function getCypher()
	{
		return $this->cypher;
	}

	/**
	 * @param string $cypher
	 */
	public function setCypher($cypher)
	{
		$this->cypher = $cypher;
		return $this;
	}

	/**
	 * @return the $salt
	 */
	public function getSalt()
	{
		return $this->salt;
	}

	/**
	 * @param string $salt
	 */
	public function setSalt($salt)
	{
		$this->salt = $salt;
		return $this;
	}

	/**
	 * @return the $pepper
	 */
	public function getPepper()
	{
		return $this->pepper;
	}

	/**
	 * @param string $pepper
	 */
	public function setPepper($pepper)
	{
		$this->pepper = $pepper;
		return $this;
	}

	/**
	 * @return the $mode
	 */
	public function getMode()
	{
		return $this->mode;
	}

	/**
	 * @param string $mode
	 */
	public function setMode($mode)
	{
		$this->mode = $mode;
		return $this;
	}

	/**
	 * @param string $key
	 */
	public function setKey($key)
	{
		$this->key = $key;
		return $this;
	}

	/**
	 * @todo Function is incomplete
	 * Encrypt Data
	 * @param string $text
	 * @param string $key
	 * @throws Exception
	 * @return string
	 */
	public static function Encrypt($text, $key = NULL)
	{
		if(isset($key))
		{
			$enckey = $key;
		}
		elseif(isset($this->key))
		{
			$enckey = $this->key;
		}
		else
		{
			throw new Exception('No Encryption Key', Staple_Error::APPLICATION_ERROR);
		}
		
	}
	
	/**
	 * @todo Function is incomplete
	 * Decrypt data
	 * @param string $encryptedText
	 * @param string $key
	 * @throws Exception
	 * @return string
	 */
	public static function Decrypt($encryptedText, $key = NULL)
	{
		if(isset($key))
		{
			$enckey = $key;
		}
		elseif(isset($this->key))
		{
			$enckey = $this->key;
		}
		else
		{
			throw new Exception('No Decryption Key', Staple_Error::APPLICATION_ERROR);
		}
	}
	
	/**
	 * Encrypt data with AES
	 * @param string $encrypt
	 * @param string $key
	 * @param string $salt
	 * @param string $pepper
	 * @param string $iv
	 * @return string
	 */
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
	
	/**
	 * Decrypt data using AES
	 * @param string $decrypt
	 * @param string $key
	 * @param string $salt
	 * @param string $pepper
	 * @param string $iv
	 * @return string
	 */
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
	 * @param string $str
	 */
	public static function strhex($str)
	{
		return pack("H*",$str);
	}
}