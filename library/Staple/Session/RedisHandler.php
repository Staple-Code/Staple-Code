<?php
/**
 * Redis session handler class.
 * Note: Requires the Predis package for Redis session handling functionality.
 * 
 * Configuration Options:
 * ;handler = 'Staple\Session\RedisHandler'
 * scheme = 'tcp'			Method of connection to the Redis server
 * host = 'localhost'		Redis cache server hostname
 * port = '6379'			Redis server port number
 * password = ''			Password for use to authenticate to the server.
 * encrypt_key = ''			Encryption key to encrypt sessions at rest in the cache.
 * prefix = 'session:'		The prefix for the Redis keys
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

use Predis\Autoloader;
use Predis\Client;
use Staple\Config;
use Staple\Encrypt;
use Staple\Exception\ConfigurationException;

class RedisHandler implements Handler
{
	const DEFAULT_PREFIX = 'session:';
	/**
	 * @var Client
	 */
	private $redis;
	/**
	 * The redis key prefix to use.
	 * @var string
	 */
	private $prefix;

	/**
	 * RedisHandler constructor.
	 * @param Client $client
	 * @param string $prefix
	 */
	public function __construct(Client $client = NULL, $prefix = NULL)
	{
		//@todo For some reason Predis does not load properly through composer. Remove this once this problem is solved.
		require_once VENDOR_ROOT.'predis/predis/src/Autoloader.php';
		if(isset($client))
		{
			//Use the supplied Predis Client object.
			$this->setRedis($client);
		}
		else
		{
			//@todo remove once the autoload works properly.
			Autoloader::register(true);
			try
			{
				$options = [
					'scheme' => Config::getValue('session', 'scheme'),
					'host'   => Config::getValue('session', 'host'),
					'port'   => Config::getValue('session', 'port'),
				];

				//Check for encryption password
				if(strlen(Config::getValue('session','password',false)) >= 1)
					$options['password'] = Config::getValue('session','password');

				//Check for SSL Configuration
				if(strlen(Config::getValue('session','cafile',false)) >= 1)
				{
					$options['scheme'] = 'tls';
					$options['ssl'] = [
						'cafile' => Config::getValue('session', 'cafile'),
						'verify_peer' => true
					];
				}

				$client = new Client($options);

				//Create a new Predis Client object with values from the configuration file.
				$this->setRedis($client);
			}
			catch (ConfigurationException $e)
			{
				//Fall back to a default client if a configured client cannot be created.
				$this->setRedis(new Client());
			}
		}

		//Set the Redis key prefix
		if(isset($prefix))
			$this->setPrefix($prefix);
		elseif(Config::exists('session','prefix'))
			$this->setPrefix(Config::getValue('session','prefix'));
		else
			$this->setPrefix(self::DEFAULT_PREFIX);
	}

	/**
	 * Get the Predis Client
	 * @return Client
	 */
	public function getRedis()
	{
		return $this->redis;
	}

	/**
	 * Set the Predis Client object
	 * @param Client $redis
	 * @return $this
	 */
	public function setRedis(Client $redis)
	{
		$this->redis = $redis;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPrefix()
	{
		return $this->prefix;
	}

	/**
	 * @param string $prefix
	 */
	public function setPrefix($prefix)
	{
		$this->prefix = $prefix;
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
	public function close()
	{
		$this->redis->disconnect();
		return true;
	}

	/**
	 * Destroy a session
	 *
	 * @link http://php.net/manual/en/sessionhandlerinterface.destroy.php
	 * @param string $session_id The session ID being destroyed.
	 * @return bool <p>
	 * The return value (usually TRUE on success, FALSE on failure).
	 * Note this value is returned internally to PHP for processing.
	 * </p>
	 * @since 5.4.0
	 */
	public function destroy($session_id)
	{
		$this->redis->del($this->getPrefix().$session_id);
		return true;
	}

	/**
	 * Cleanup old sessions
	 *
	 * @link http://php.net/manual/en/sessionhandlerinterface.gc.php
	 * @param int $maxlifetime <p>
	 * Sessions that have not updated for
	 * the last maxlifetime seconds will be removed.
	 * </p>
	 * @return bool <p>
	 * The return value (usually TRUE on success, FALSE on failure).
	 * Note this value is returned internally to PHP for processing.
	 * </p>
	 * @since 5.4.0
	 */
	public function gc($maxlifetime)
	{
		return true;
	}

	/**
	 * Initialize session
	 *
	 * @link http://php.net/manual/en/sessionhandlerinterface.open.php
	 * @param string $save_path The path where to store/retrieve the session.
	 * @param string $session_id The session id.
	 * @return bool <p>
	 * The return value (usually TRUE on success, FALSE on failure).
	 * Note this value is returned internally to PHP for processing.
	 * </p>
	 * @since 5.4.0
	 */
	public function open($save_path, $session_id)
	{
		return true;
	}

	/**
	 * Read session data
	 *
	 * @link http://php.net/manual/en/sessionhandlerinterface.read.php
	 * @param string $session_id The session id to read data for.
	 * @return string <p>
	 * Returns an encoded string of the read data.
	 * If nothing was read, it must return an empty string.
	 * Note this value is returned internally to PHP for processing.
	 * </p>
	 * @since 5.4.0
	 */
	public function read($session_id)
	{
		//Redis Key
		$sessionId = $this->getPrefix().$session_id;
		//Get Session Data
		$sessionData = $this->redis->get($sessionId);
		//Reset the Expiration
		$this->redis->expire($sessionId, Session::getInstance()->getMaxLifetime());
		
		//Return Session Data to PHP
		if (Config::exists('session', 'encrypt_key'))
			return Encrypt::decrypt(base64_decode($sessionData), Config::getValue('session', 'encrypt_key'));
		else
			return $sessionData;
	}

	/**
	 * Write session data
	 *
	 * @link http://php.net/manual/en/sessionhandlerinterface.write.php
	 * @param string $session_id The session id.
	 * @param string $session_data <p>
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
	public function write($session_id, $session_data)
	{
		if(Config::exists('session','encrypt_key'))
			$payload = base64_encode(Encrypt::encrypt($session_data,Config::getValue('session','encrypt_key')));
		else
			$payload = $session_data;

		//Setup Redis Key
		$sessionId = $this->getPrefix().$session_id;
		//Write the session data to Redis.
		$this->redis->set($sessionId, $payload);
		//Set the Expiration
		$this->redis->expire($sessionId, Session::getInstance()->getMaxLifetime());
		//Let PHP know that it succeeded.
		return true;
	}
}