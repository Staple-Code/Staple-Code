<?php
/**
 * Basic session file handler class.
 *
 * Configuration Options [session]:
 * file_location = ''		The location of the session files on the server.
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

use Staple\Config;
use Staple\Exception\ConfigurationException;

class FileHandler implements Handler
{
	/**
	 * The location where the session files will be stored.
	 * @var string
	 */
	private string $fileLocation;

	/**
	 * FileHandler constructor.
	 *
	 * @param string|null $location
	 * @throws ConfigurationException
	 */
	public function __construct(string $location = NULL)
	{
		if(isset($location))
			$this->setFileLocation($location);
		elseif(Config::exists('session','file_location'))
			$this->setFileLocation(Config::getValue('session','file_location'));
		else
			$this->setFileLocation(session_save_path());
	}

	/**
	 * Get the file location for session storage
	 *
	 * @return string
	 */
	public function getFileLocation(): string
	{
		return $this->fileLocation;
	}

	/**
	 * Set the file location for the session store.
	 * @param string $fileLocation
	 * @return $this
	 */
	protected function setFileLocation(string $fileLocation): self
	{
		$this->fileLocation = $fileLocation;
		return $this;
	}

	/**
	 * Close the session
	 *
	 * @link http://php.net/manual/en/sessionhandlerinterface.close.php
	 * @return bool <p>
	 * The return value (usually TRUE on success, FALSE on failure).
	 * Note this value is returned internally to PHP for processing.
	 * </p>
	 * @since 5.4.0
	 */
	public function close(): bool
	{
		return true;
	}

	/**
	 * Destroy a session
	 *
	 * @link http://php.net/manual/en/sessionhandlerinterface.destroy.php
	 * @param string $id The session ID being destroyed.
	 * @return bool <p>
	 * The return value (usually TRUE on success, FALSE on failure).
	 * Note this value is returned internally to PHP for processing.
	 * </p>
	 * @since 5.4.0
	 */
	public function destroy(string $id): bool
	{
		$file = $this->fileLocation.DIRECTORY_SEPARATOR.'session_'.$id;
		if (file_exists($file))
		{
			unlink($file);
		}

		return true;
	}

	/**
	 * Cleanup old sessions
	 *
	 * @link http://php.net/manual/en/sessionhandlerinterface.gc.php
	 * @param int $max_lifetime <p>
	 * Sessions that have not updated for
	 * the last maxlifetime seconds will be removed.
	 * </p>
	 * @return false|int <p>
	 * The return value (usually TRUE on success, FALSE on failure).
	 * Note this value is returned internally to PHP for processing.
	 * </p>
	 * @since 5.4.0
	 */
	public function gc(int $max_lifetime): false|int
	{
		foreach (glob($this->fileLocation.DIRECTORY_SEPARATOR.'session_*') as $file)
		{
			if (filemtime($file) + $max_lifetime < time() && file_exists($file))
			{
				unlink($file);
			}
		}

		return true;
	}

	/**
	 * Initialize session
	 *
	 * @link http://php.net/manual/en/sessionhandlerinterface.open.php
	 * @param string $path The path where to store/retrieve the session.
	 * @param string $name The session id.
	 * @return bool <p>
	 * The return value (usually TRUE on success, FALSE on failure).
	 * Note this value is returned internally to PHP for processing.
	 * </p>
	 * @since 5.4.0
	 */
	public function open(string $path, string $name): bool
	{
		if (!is_dir($this->fileLocation))
		{
			mkdir($this->fileLocation, 0777);
		}

		return true;
	}

	/**
	 * Read session data
	 *
	 * @link http://php.net/manual/en/sessionhandlerinterface.read.php
	 * @param string $id The session id to read data for.
	 * @return string <p>
	 * Returns an encoded string of the read data.
	 * If nothing was read, it must return an empty string.
	 * Note this value is returned internally to PHP for processing.
	 * </p>
	 * @since 5.4.0
	 */
	public function read(string $id): string
	{
		$session_file = $this->fileLocation.DIRECTORY_SEPARATOR.'session_'.$id;
		if(file_exists($session_file))
		{
			return (string)@file_get_contents($session_file);
		}
		else
		{
			return (string)'';
		}
	}

	/**
	 * Write session data
	 *
	 * @link http://php.net/manual/en/sessionhandlerinterface.write.php
	 * @param string $id The session id.
	 * @param string $data <p>
	 * The encoded session data. This data is the
	 * result of the PHP internally encoding
	 * the $_SESSION superglobal to a serialized
	 * string and passing it as this parameter.
	 * Please note sessions use an alternative serialization method.
	 * </p>
	 * @return bool <p>
	 * The return value (usually TRUE on success, FALSE on failure).
	 * Note this value is returned internally to PHP for processing.
	 * </p>
	 * @since 5.4.0
	 */
	public function write(string $id, string $data): bool
	{
		return !(file_put_contents($this->fileLocation . DIRECTORY_SEPARATOR . 'session_' . $id, $data) === false);
	}
}