<?php
/** 
 * This is the packaged database authorization adapter. This adapter requires the following
 * settings to be included in the configuration file:
 * 
 * enabled - Set to 1 or 0 to enable or disable authentication. 1 is the default setting, if excluded.
 * adapter - Tells the Staple_Main class which AuthAdapter to load.
 * authtable - Specifies the database table where auth credentials reside.
 * uidfield - Defines the username or user identifer field.
 * pwfield - Defines the password field.
 * pwenctype - The type of encryption used on the password. Values include 'MD5', 'SHA1', 'AES', and 'none'. 
 * rolefield - (optional) This field specifies the database table that holds the access level. If no field is provided or it is null, 1 will be returned.
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
 * 
 */
namespace Staple;

use \Exception;

class DBAuthAdapter implements AuthAdapter
{
	/**
	 * Settings Array
	 * @deprecated
	 * @var array
	 */
	private $_settings = array();
	/**
	 * Store the user identifier. Usually the username.
	 * @var string
	 */
	private $uid;
	
	
	/**
	 * 
	 * The constructor loads and checks the adapter configuration.
	 * @throws Exception
	 */
	public function __construct()
	{
		if($this->checkConfig(Config::get('auth')))
		{
			$this->_settings = Config::get('auth');
		}
		else
		{
			throw new Exception('Staple_DBAuthAdapter critical failure.',Error::AUTH_ERROR);
		}

	}
	
	/**
	 * getAuth checks the database for valid credentials and returns true if they are found.
	 * @param array $cred
	 * @return bool
	 * @see Staple_AuthAdapter::getAuth()
	 */
	public function getAuth($cred)
	{
		if($this->checkConfig($this->_settings))
		{
			if(array_key_exists('username', $cred) AND array_key_exists('password', $cred))
			{
				$db = DB::get();
				$this->uid = $cred['username'];
				switch($this->_settings['pwenctype'])
				{
					case 'MD5':
						$pass = md5($cred['password']);
						break;
					case 'SHA1':
						$pass =sha1($cred['password']);
						break;
					//case 'AES':
					//	$pass = Staple_Encrypt::AES_encrypt(($cred['password']),'');
					//	break;
					default:
						$pass = $cred['password'];
				}
				$sql = 'SELECT '.$db->real_escape_string($this->_settings['uidfield']).','.$db->real_escape_string($this->_settings['pwfield']).'
							FROM '.$db->real_escape_string($this->_settings['authtable']).'
						WHERE '.$db->real_escape_string($this->_settings['uidfield']).' = '.
							'\''.$db->real_escape_string($cred['username']).'\'
							AND '.$db->real_escape_string($this->_settings['pwfield']).' = '.
							'\''.$db->real_escape_string($pass).'\';';
				if(($result = $db->query($sql)) !== false)
				{
					$myrow = $result->fetch_array();
					//Secondary check to make sure the results did not differ from MySQL's response.
					if($myrow[$this->_settings['uidfield']] == $this->uid && $myrow[$this->_settings['pwfield']] == $pass)
					{
						return true;
					}
				}
			}
		}
		return false;	
	}

	/**
	 * Gets the access level for the supplied $uid.
	 * @param string $uid
	 * @return int
	 * @see Staple_AuthAdapter::getLevel()
	 */
	public function getLevel($uid)
	{
		if($this->checkConfig($this->_settings))
		{
			if(array_key_exists('rolefield', $this->_settings))
			{
				$db = DB::get();
				$sql = 'SELECT '.$db->real_escape_string($this->_settings['rolefield']).' 
						FROM '.$db->real_escape_string($this->_settings['authtable']).'
						WHERE '.$db->real_escape_string($this->_settings['uidfield']).' = '.
							'\''.$db->real_escape_string($uid).'\';';
				$result = $db->query($sql);
				if($result !== false)
				{
					$myrow = $result->fetch_array();
					$level = (int)$myrow[$this->_settings['rolefield']];
					if($level < 0)
					{
						return 0;
					}
					else 
					{
						return $level;
					}
				}
				else
				{
					return 0;
				}
			}
			else
			{
				return 1;
			}
		}
		return 0;
	}

	/**
	 * Checks the configuration fields for validity
	 * @param array $config
	 * @throws Exception
	 * @return bool
	 */
	protected function checkConfig(array $config)
	{
		$keys = array('enabled','adapter','authtable','uidfield','pwfield','pwenctype');
		foreach($keys as $value)
		{
			if(!array_key_exists($value, $config))
			{
				throw new Exception('Staple_DBAuthAdapter configuration error.',Error::AUTH_ERROR);
			}
		}
		return true;
	}
	
	/**
	 * Returns the User ID from the adapter.
	 * @return string
	 * @see Staple_AuthAdapter::getUserId()
	 */
	public function getUserId()
	{
		return $this->uid;
	}

}