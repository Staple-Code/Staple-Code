<?php
/**
 * Staple Session management main class.
 *
 * Configuration options [session]:
 * max_lifetime = 20 		The length in minutes that the session lives for.
 * handler = [class name]	The handler class that is used to handle the session.
 * 				Built in options: Staple\Session\DatabaseHandler,
 * 				Staple\Session\FileHandler, Staple\Session\RedisHandler
 *
 * @author Ironpilot
 * @copyright Copyright (c) 2016, STAPLE CODE
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

namespace Staple\Session;

use Staple\Auth\Auth;
use Staple\Config;
use Staple\Controller\Controller;
use Staple\Exception\SessionException;
use Staple\Traits\Singleton;

class Session
{
	use Singleton;
	/**
	 * @var Handler
	 */
	protected $handler;
	/**
	 * The session ID
	 * @var string
	 */
	protected $sessionId;
	/**
	 * The session name;
	 * @var string
	 */
	protected $sessionName;
	/**
	 * The number of seconds for session life.
	 * @var int
	 */
	protected $maxLifetime = 1440;
	/**
	 * Booleon value to signify if the session has already been started or not.
	 * @var bool
	 */
	protected $sessionStarted = false;

	/**
	 * Session constructor. Optional session handler object parameter.
	 *
	 * @param Handler $handler
	 * @param string $name
	 * @throws SessionException
	 */
	public function __construct(Handler $handler = NULL, $name = NULL)
	{
		//Setup the session handler
		if(isset($handler))
			$this->setHandler($handler);
		elseif (($configHandler = Config::getValue('session','handler',false)) != NULL)
			$this->setHandler(new $configHandler());
		else
			$this->setHandler(new FileHandler());

		//Set the optional session name
		if(isset($name))
		{
			if(php_sapi_name() != 'cli')
				session_name($name);
			$this->setSessionName($name);
		}
		elseif(($configName = Config::getValue('session', 'name', false)) != null)
		{
			if(php_sapi_name() != 'cli')
				session_name($name);
			$this->setSessionName($name);
		}

		//Set the session max lifetime
		if(Config::exists('session', 'max_lifetime'))
			$this->setMaxLifetime(Config::getValue('session', 'max_lifetime'));
		else
			$this->setMaxLifetime(ini_get('session.gc_maxlifetime'));

		//Setup session handler functions
		if(php_sapi_name() != 'cli')
		{
			if(!headers_sent())
			{
				$handlerSetup = @session_set_save_handler($this->handler, true);
				if(!$handlerSetup)
					throw new SessionException('Failed to setup session save handler: ' . get_class($this->handler));
			}
			else
			{
				throw new SessionException('Headers already sent: ' . implode('/n', headers_list()));
			}
		}
	}

	public function __wakeup()
	{
		$this->setSessionStarted(false);
	}

	/**
	 * Get the session handler object
	 * @return Handler | FileHandler
	 */
	public function getHandler()
	{
		return $this->handler;
	}

	/**
	 * Set the session handler object. Must be an instance of Staple\Session\Handler abstract class.
	 * @param Handler $handler
	 * @return $this
	 */
	public function setHandler(Handler $handler)
	{
		$this->handler = $handler;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSessionId()
	{
		return $this->sessionId;
	}

	/**
	 * @param string $sessionId
	 * @return $this
	 */
	public function setSessionId($sessionId)
	{
		$this->sessionId = $sessionId;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSessionName()
	{
		return $this->sessionName;
	}

	/**
	 * @param string $sessionName
	 * @return $this
	 */
	public function setSessionName($sessionName)
	{
		//Don't allow digit only session names
		if(!ctype_digit($sessionName) && ctype_alnum($sessionName))
		{
			$this->sessionName = (string)$sessionName;
		}
		return $this;
	}

	/**
	 * Get the session max lifetime
	 * @return int
	 */
	public function getMaxLifetime()
	{
		return $this->maxLifetime;
	}

	/**
	 * Set the max session lifetime
	 * @param int $maxLifetime
	 * @return $this
	 */
	public function setMaxLifetime($maxLifetime)
	{
		$this->maxLifetime = (int)$maxLifetime;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function isSessionStarted()
	{
		return $this->sessionStarted;
	}

	/**
	 * @param boolean $sessionStarted
	 * @return $this
	 */
	protected function setSessionStarted($sessionStarted)
	{
		$this->sessionStarted = (bool)$sessionStarted;
		return $this;
	}

	/**
	 * Set a session variable value.
	 * @param string $key
	 * @param mixed $value
	 */
	public static function set($key, $value)
	{
		$_SESSION[(string)$key]	= $value;
	}

	/**
	 * Retrieve a value from the session object.
	 * @param string $key
	 * @return mixed
	 */
	public static function get($key)
	{
		if(array_key_exists($key, $_SESSION))
		{
			return $_SESSION[$key];
		}
		return NULL;
	}

	/**
	 * Get the internal session data object for Staple.
	 * @return InternalSessionData
	 */
	public static function getInternalData() : InternalSessionData
	{
		if(!self::getInstance()->isSessionStarted())
			self::start();

		if(isset($_SESSION['Staple']))
			if($_SESSION['Staple'] instanceof InternalSessionData)
				return $_SESSION['Staple'];

		return self::createInternalDataObject();
	}

	/**
	 * Create or retrieve the internal session data object for Staple.
	 * @return InternalSessionData
	 */
	protected static function createInternalDataObject() : InternalSessionData
	{
		$_SESSION['Staple'] = new InternalSessionData();
		return $_SESSION['Staple'];
	}

	/**
	 * Register a controller with the session.
	 * @param Controller $controller
	 * @return Controller
	 */
	public static function registerController(Controller $controller) : Controller
	{
		$data = self::getInternalData();
		return $data->registerController($controller);
	}

	/**
	 * @param string $controller
	 * @return Controller|NULL
	 */
	public static function getController($controller)
	{
		$data = self::getInternalData();
		return $data->getController($controller);
	}

	/**
	 * Set Auth in the session
	 * @param Auth $auth
	 * @return Auth
	 */
	public static function auth(Auth $auth = NULL)
	{
		$data = self::getInternalData();
		if($auth instanceof Auth)
			return $data->setAuth($auth);
		else
			return $data->getAuth();
	}

	/**
	 * Set, retrieve or clear a form identity from the session.
	 * @param string $name
	 * @param string $value
	 * @param bool $clear
	 * @return string
	 */
	public static function formIdentity($name, $value = null, $clear = false)
	{
		$data = self::getInternalData();
		if(isset($value))
			return $data->setFormIdentity($name, $value);
		elseif($clear == true)
			return $data->clearFormIdentity($name);
		else
			return $data->getFormIdentity($name);
	}

	/**
	 * Check for form submission
	 * @param string $name
	 * @param string $hash
	 * @return bool
	 */
	public static function checkForm($name, $hash)
	{
		$data = self::getInternalData();
		return $data->checkFormIdentity($name, $hash);
	}

	/**
	 * Set or retrieve a registry key
	 * @param string $key
	 * @param mixed $value
	 * @return mixed
	 */
	public static function register($key, $value = null)
	{
		$data = self::getInternalData();
		if(isset($value))
			return $data->setRegistryKey($key, $value);
		else
			return $data->getRegistryKey($key);
	}

	/**
	 * Start the session.
	 * @param string $sessionId
	 * @param bool $suppressThrow
	 * @return $this
	 * @throws SessionException
	 */
	public static function start($sessionId = NULL, $suppressThrow = false)
	{
		$session = self::getInstance();
		if($session->isSessionStarted() == false)
		{
			if (!headers_sent())
			{
				//Create a new Session
				if (isset($sessionId))
				{
					session_id($sessionId);
					$session->setSessionId($sessionId)
						->setSessionStarted(true);
					if (!session_start())
						throw new SessionException('Failed to start session.');
				}
				else
				{
					if (!session_start())
						throw new SessionException('Failed to start session.');
					$session->setSessionId(session_id())
						->setSessionStarted(true);
				}
			}
			elseif(php_sapi_name() == 'cli')
			{
				//Ignore sessions in the CLI
				return $session;
			}
			else
			{
				if (!$suppressThrow)
					throw new SessionException('Session headers have already been sent by output.');
			}
		}
		return $session;
	}

	/**
	 * Destroy the session.
	 */
	public static function destroy()
	{
		session_destroy();
	}

	/**
	 * Regenerate the session ID
	 * @param bool $deleteOldSession
	 * @return bool
	 */
	public function regenerate($deleteOldSession = true)
	{
		if($this->sessionStarted)
		{
			session_regenerate_id($deleteOldSession);
			return true;
		}
		return false;
	}
}