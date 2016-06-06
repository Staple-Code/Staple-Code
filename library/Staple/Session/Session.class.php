<?php
/**
 * Staple Session management main class.
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

class Session
{
	/**
	 * @var Handler
	 */
	protected $handler;

	/**
	 * Session constructor. Optional session handler object parameter.
	 *
	 * @param Handler $handler
	 */
	public function __construct(Handler $handler = NULL)
	{
		if(isset($handler))
			$this->setHandler($handler);
		else
			$this->setHandler(new FileHandler());

		//Setup session handler functions
		session_set_save_handler($this->handler, true);
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
	 * Start the session.
	 * @param string $sessionId
	 * @return $this
	 */
	public function start($sessionId = NULL)
	{
		if(isset($sessionId)) session_id($sessionId);
		session_start();
	}
}