<?php
/** 
 * This is the packaged Active Directory authorization adapter. This adapter requires the following
 * settings to be included in the application.ini or the auth.ini file:
 * 
 * enabled - Set to 1 or 0 to enable or disable authentication. 1 is the default setting, if excluded.
 * adapter - Tells the Staple_Main class which AuthAdapter to load.
 * authtable - (optional) Specifies the database table where auth credentials reside.
 * uidfield - (optional) Defines the username or user identifer field.
 * 
 * 
 * @author Hans Heeling
 * @copyright Copywrite (c) 2011, STAPLE CODE
 * 
 * This file is part of the STAPLE Framework.
 * 
 * The STAPLE Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by the 
 * Free Software Foundation, either version 3 of the License, or (at your option)
 * any later version.
 * 
 * The STAPLE Framework is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for 
 * more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with the STAPLE Framework.  If not, see <http://www.gnu.org/licenses/>.
 * 
 */
class Staple_ADAuthAdapter implements Staple_AuthAdapter
{
	/**
	 * 
	 * Enter description here ...
	 * @var array
	 */
	private $_settings = array();
	/**
	 * 
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
		if(file_exists(CONFIG_ROOT.'application.ini'))
		{
			$curConfig = parse_ini_file(CONFIG_ROOT.'application.ini',true);
			if($this->checkConfig($curConfig['auth']))
			{
				$this->_settings = $curConfig['auth'];
			}
		}
		elseif(file_exists(CONFIG_ROOT.'auth.ini'))
		{
			$curConfig = parse_ini_file(CONFIG_ROOT.'auth.ini');
			if($this->checkConfig($curConfig))
			{
				$this->_settings = $curConfig;
			}
		}
		else
		{
			throw new Exception('Staple_ADAuthAdapter critical failure.',500);
		}

	}
	
	/**
	 * getAuth checks Active Directory for valid credentials and returns true if they are found.
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
				if(strlen($cred['username']) >= 1 && strlen($cred['password']) >= 1)
				{
					if(Staple_AD::validchars($cred['username']) == TRUE && Staple_AD::validchars($cred['password']) == TRUE)
					{
						$pass = $cred['password'];
						$LDAP = Staple_AD::get();
						$this->uid = $cred['username'];
						if($LDAP->bind($this->uid, $pass))
						{
							return true;
						}
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
				$db = Staple_DB::get();
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
	}

	/**
	 * 
	 * Checks the configuration fields for validity
	 * @param array $config
	 * @throws Exception
	 */
	protected function checkConfig(array $config)
	{
		$keys = array('enabled','adapter');
		foreach($keys as $value)
		{
			if(!array_key_exists($value, $config))
			{
				throw new Exception('Staple_ADAuthAdapter configuration error.',Staple_Error::AUTH_ERROR);
			}
		}
		if($config['adapter'] != get_class($this))
		{
			throw new Exception('Staple_ADAuthAdapter configuration error.',Staple_Error::AUTH_ERROR);
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

?>