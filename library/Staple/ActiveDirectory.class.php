<?php
/**
 * 
 * The Staple_AD class
 * 
 * host - the hostname or IP of the Active Directory server to connect to.
 * username - Active Directory username to use to connect to the database with management privledges.
 * password - Active Directory password to use to connect to Active Directory.
 * LDAPSenabled - Determines if LDAPS is to be used to connect to Active Directory. See LDAPS notes below.
 * domain - FQDN of Active Directory
 * baseDN - Active Directory baseDN
 * 
 * **LDAPS Notes**
 * LDAPS must be enable to perform any password management functions. In order to use LDAPS certificate services must 
 * be running on your active directory server and the system running this script must accecpt the security certificate.
 *  
 * 
 * @author Hans Heeling
 * @copyright Copyright (c) 2011, STAPLE CODE
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
 */
namespace Staple;

use \Exception;

class ActiveDirectory
{
	/**
	 * 
	 * Holds the singleton instance for the object.
	 * @var ActiveDirectory
	 * @static
	 */
	protected static $conn;
	/**
	 * 
	 * Hostname for the AD server
	 * @var string
	 */
	protected $host;
	/**
	 * 
	 * Username to access to the AD server
	 * @var string
	 */
	protected $username;
	/**
	 * 
	 * Password to connect to Active Directory
	 * @var string
	 */
	protected $password;
	
	/**
	 * 
	 * Domain name
	 * @var string
	 */
	protected $domain;
		
	/**
	 * 
	 * Domain base DN
	 * @var string
	 */
	protected $baseDN;
	
	/**
	 * 
	 * True or False if connections are to use LDAPS
	 * @var string
	 */
	protected $LDAPSenabled;
	
	/**
	 * 
	 * Holds the connection identifier to the Active Directory server
	 * @var resource
	 */
	protected $LDAPConn;
	
	/**
	 * @return string $host
	 */
	public function getHost()
	{
		return $this->host;
	}

	/**
	 * @param string $host
	 * @return $this
	 */
	public function setHost($host)
	{
		$this->host = $host;
		return $this;
	}

	/**
	 * @return string $username
	 */
	public function getUsername()
	{
		return $this->username;
	}

	/**
	 * @param string $username
	 * @return $this
	 */
	public function setUsername($username)
	{
		$this->username = $username;
		return $this;
	}

	/**
	 * @return string $domain
	 */
	public function getDomain()
	{
		return $this->domain;
	}
	
	/**
	 * @return string $baseDN
	 */
	public function getbaseDN()
	{
		return $this->baseDN;
	}
	
	public function setbaseDN($baseDN)
	{
		$this->baseDN = $baseDN;
		return $this;
	}

	/**
	 * @return string $LDAPSenabled
	 */
	public function getLDAPSenabled()
	{
		return $this->LDAPSenabled;
	}
	
	/**
	 * @param string $LDAPSenabled
	 * @return $this
	 */
	public function setLDAPSenabled($LDAPSenabled)
	{
		$this->LDAPSenabled = $LDAPSenabled;
		return $this;
	}
	
	/**
	 * @param string $domain
	 * @return $this
	 */
	public function setDomain($domain)
	{
		$this->domain = $domain;
		return $this;
	}

	/**
	 * Sets the database password parameter.
	 * @param string $password
	 */
	public function setPassword($password)
	{
		$this->password = $password;
	}
	
	/**
	 * 
	 * Loads the AD configuration information and esablishes connection to Active Directory Server
	 * 
	 * @throws Exception
	 */
	protected function __construct()
	{
		$settings = Config::get('ActiveDirectory');
		
		$this->host = $settings['host'];
		$this->username = $settings['username'];
		$this->password = $settings['password'];
		$this->domain = $settings['domain'];
		$this->baseDN = $settings['baseDN'];
		$this->LDAPSenabled = $settings['LDAPSenabled'];

		
		if($this->LDAPSenabled == 1)
		{
			$LDAPServer = "ldaps://" . $this->host;
			$this->LDAPConn = ldap_connect($LDAPServer, 636);
		}
		else 
		{
			$LDAPServer = "ldap://" . $this->host;
			$this->LDAPConn = ldap_connect($LDAPServer, 389);
		}
		
		ldap_set_option($this->LDAPConn, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($this->LDAPConn, LDAP_OPT_REFERRALS, 0); 
		
		$this->mgmtbind();
	}
	
	/** 
	 * 
	 * Closes connection to LDAP server to free up resources.
	 */
	public function __destruct()
	{
		ldap_close($this->LDAPConn);
	}
	
	/**
	 * 
	 * Maintains a single instance of the AD connection.
	 */
	public function __clone()
    {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }
	
    /**
     * 
     * Creates a singleton instance of the ActiveDirectory object.
     * @return ActiveDirectory
     */
	public static function get()
	{
		if (!isset(self::$conn)) {
            $c = __CLASS__;
            self::$conn = new $c();
        }
		return self::$conn;
	}
	
	/** 
	 * 
	 * Returns the full user login name for the specified username
	 * @param string $username
	 * @return string
	 * @static
	 */
	public function usrlogin($username)
	{
		$usrlogin = $username . "@" . $this->domain;
		return $usrlogin;
	}
	
	/**
	 * Establishes bind to ldap server with management credentials
	 */	
	protected function mgmtbind()
	{
		try 
		{
			if(ldap_bind($this->LDAPConn, $this->usrlogin($this->username), $this->password))
			{
				return TRUE;
			}
			else 
			{
				throw new Exception("Active Directory Management Connection Error: Check Management Credentials");
			}
		}
		catch(Exception $e)
		{
			throw new Exception("Active Directory Management Connection Error: Check Management Credentials");
		}
	}
	
	/**
	 * Checks the configuration file to make sure that all required keys exist.
	 * @param array $config
	 * @throws Exception
	 * @return bool
	 */
	protected function checkConfig(array $config)
	{
		$keys = array('host','domain','baseDN','LDAPSenabled','username','password');
		foreach($keys as $value)
		{
			if(!array_key_exists($value, $config))
			{
				throw new Exception('Staple_AD configuration error.',Error::DB_ERROR);
			}
		}
		return true;
	}
	
	/**
	 * 
	 * Checks the supplied value for invalid chars, returns TRUE if string is valid
	 * @param $value
	 * @return bool
	 */
	public static function validchars($value)
	{
		if(preg_match('/[^A-Za-z0-9!@$%^&*]/', $value) == 0)
		{
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * 
	 * Unicodes the supplied password
	 * @param $password
	 * @return string
	 */
	public static function unicodePwd($password)
	{	
		$uniPword = "";
		$Pword = "\"$password\"";
		for($i=0; $i<10; $i++)
		{
			$uniPword .= "{$Pword{$i}}\000";	
		}
		return $uniPword;
	}
	
	/** 
	 * 
	 * Verifies user credentials based on username and password provided, then resestablishes
	 * management user connection to Active Directory.
	 * @param string $uname
	 * @param string $pword
	 * @return bool
	 */
	public function bind($uname, $pword)
	{
		try
		{
			if(ldap_bind($this->LDAPConn, self::usrlogin($uname), $pword))
			{
				$this->mgmtbind();
				return TRUE;
			}
			else
			{
				$this->mgmtbind();			
				return FALSE;
			}
		}
		catch(Exception $e)
		{
			$this->mgmtbind();
			return FALSE;
		}
	}
	
	/**
	 * 
	 * Returns an array containing the first and last name for the given username
	 * @param $username
	 * @return array | bool
	 */
	public function firstlast($username)
	{
		$UserInfo = ldap_get_entries($this->LDAPConn, $this->search("(sAMAccountName=$username)", "sn"));
		if(@$UserInfo[0]["sn"][0] != "" && $UserInfo[0]["givenname"][0] != "")
		{
			$nameInfo = array();
			$nameInfo['lastname'] = $UserInfo[0]["sn"][0];
			$nameInfo['firstname'] = $UserInfo[0]["givenname"][0];
			return $nameInfo;
		}
		else
		{
			return FALSE;
		}
		
	}
	
	/**
	 * 
	 * Escapes strings for LDAP Filters and entry. 
	 * 
	 * Caution: This filter is still basic, it is suggested that you create whitelist filters on all 
	 * user input. 
	 * 
	 * @param $value
	 * @todo incomplete function
	 */
	
	public function ldapescape($value)
	{
		$value = str_replace(array('\\', '*', '(', ')'), array('\5c', '\2a', '\28', '\29'), $value);
		foreach(array($value) as $val)
		{
			if(ord($val) < 32)
			{
				$hex = dechex(ord($val));
				if(strlen($hex) == 1)
				{
					$hex = '0' . $hex;
					$val = '\\' . $hex;
				}
			}
		}
		
	}
	
	/**
	 * 
	 * This method will change the supplied users password to the provided password. Will return a TRUE for success or a FALSE for
	 * failure to change the password. 
	 */
	
	public function ChgUsrPwd($username, $password)
	{
		$filter = "(sAMAccountName=" . $username . ")";
		$Result = $this->search($filter, "dn", array("DN"));
		$UDN = $Result[0]['dn'];
		$encodePWD = $this->unicodePwd($password);
		$AccInfo["unicodePwd"] = $encodePWD;
		try 
		{
			if(ldap_mod_replace($this->LDAPConn, $UDN, $AccInfo))
			{
				return TRUE;
			}
			else 
			{
				return FALSE;
			}
		}
		catch (Exception $e)
		{
			return FALSE;
		}
	}
	
	/**
	 * 
	 * This method allows for the disabling of a user. Will return True if disabled and False if failes to disable
	 * @param string $username
	 */
	public function UsrDisable($username)
	{
		//@todo incomplete method
	}
	
	/**
	 * 
	 * This method allows for the re-enableing of a user account. Will return True is user account is enabled and False if it fails to enable.
	 * 
	 */
	public function UserEnable($username)
	{
		//@todo incomplete method
	}
	
	/**
	 * 
	 * User creation **UNDER CONSTRUCTION**
	 */
	public function UserCreate()
	{
		//@todo incomplete method
	}
	
	/**
	 * 
	 * Simple LDAP search function. Provide the serach term for which you want to serach the LDAP Directory
	 * as well how you want the returned array sorted. Attributes can be only those items you want to search, and limit
	 * is how many entries you want returned. returns multi-dimensional array
	 * 
	 * @param string $searchterm - Also known as filter
	 * @param string $sortby
	 * @param array $attributes - Entries to return
	 * @param string $location
	 * @param int $limit - unused
	 * @return array
	 */
	public function search($searchterm, $sortby, $attributes = array(), $location = null, $limit = 0)
	{
		if($location === null)
		{
			$result = ldap_search($this->LDAPConn, $this->baseDN, $searchterm, $attributes);
		}
		else
		{
			if(stripos($location, $this->baseDN) > 0)
			{
				$DetLocal = $location;
			}
			else
			{
				$DetLocal = $location . "," . $this->baseDN;
			}
			$result = ldap_search($this->LDAPConn, $DetLocal, $searchterm, $attributes);
		}
		ldap_sort($this->LDAPConn, $result, $sortby);
		$entries = ldap_get_entries($this->LDAPConn, $result);
		return $entries;
	}
	
	/**
	 * 
	 * Provides a list of users for the given directory DN location when added to the already supplied BaseDN. Will return a multi-dimensional array
	 * of the returned values. 
	 * 
	 * @param $sortby - can be sorted by sn, givenName, samAccountName
	 * @param $location - Detail DN excluding BaseDN
	 * @return array
	 */
	
	public function listusers($sortby, $location = null)
	{
		$filter = "(objectClass=user)";
		$attributes = array("sn","givenName","samAccountName");
		$entries = $this->search($filter, $sortby, $attributes, $location);
		return $entries;
	}
	
	public function listous($location = null)
	{
		$results = array();
		$filter = "(objectClass=organizationalUnit)";
		$attributes = array("dn");
		$entries = $this->search($filter, "dn", $attributes, $location);
		for($i=0; $i<$entries['count']; $i++)
		{
			$results[$i]['cn'] = substr($entries[$i]['dn'], 3, strpos($entries[$i]['dn'], ",")-3);
			$results[$i]['dn'] = $entries[$i]['dn'];
		}
		$count = $entries['count'];
		$results['count'] = $count;
		return $results;
		
	}
	/**
	 * Verifies the existance of the provided user in Active Directory
	 * 
	 * @param $username 
	 * @return bool
	 */
	public function userexit($username)
	{
		$usersam = "(sAMAccountName=" . $username . ")";
		try 
		{	
			if($this->search($usersam, "dn", array("DN")))
			{
				return TRUE;
			}
			else 
			{
				return FALSE;
			}
		}
		catch (Exception $e)
		{
			return FALSE;
		}
	}
	
	/**
	 * Returns the user DN for the given username.
	 * @param $username
	 * @return string
	 */
	public function userDN($username)
	{
		$UserInfo = $this->search("(sAMAccountName=$username)", "sn", array('dn'));
		return $UserInfo[0]['dn'];
	}
}	
