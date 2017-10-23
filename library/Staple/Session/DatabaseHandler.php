<?php
/**
 * Database session handler class.
 *
 * Configuration options [session]:
 * connection = ''			The named connection to use
 * table = 'sessions'		Session table name
 * encrypt_key = ''			Encryption key to encrypt sessions at rest in the database.
 *
 * Database table structure:
 *  - id VARCHAR
 *  - payload TEXT
 *  - last_activity INT/TIMESTAMP
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

use PDO;
use Staple\Config;
use Staple\Encrypt;
use Staple\Exception\ConfigurationException;
use Staple\Exception\SessionException;
use Staple\Query\Condition;
use Staple\Query\Connection;
use Staple\Query\Query;

class DatabaseHandler implements Handler
{
	/**
	 * The location where the session files will be stored.
	 * @var Connection
	 */
	private $connection;
	/**
	 * @var string
	 */
	private $table;

	/**
	 * DatabaseHandler constructor.
	 *
	 * @param Connection $connection
	 * @param string $table
	 */
	public function __construct(Connection $connection = NULL, $table = NULL)
	{
		try
		{
			//Set the database connection
			if (isset($connection))
				$this->setConnection($connection);
			elseif (Config::exists('session', 'connection'))
				$this->setConnection(Connection::getNamedConnection(Config::getValue('session', 'connection')));
			else
				$this->setConnection(Connection::get());
		}
		catch (ConfigurationException $e)
		{
			$this->setConnection(Connection::get());
		}

		//Set the database table.
		try
		{
			if (isset($table))
				$this->setTable($table);
			else
				$this->setTable(Config::getValue('session', 'table'));
		}
		catch (ConfigurationException $e)
		{
			$this->setTable('sessions');
		}
	}

	/**
	 * Return the database connection object
	 * @return string
	 */
	public function getConnection()
	{
		return $this->connection;
	}

	/**
	 * Set the database connection for the session store.
	 * @param Connection $connection
	 * @return $this
	 */
	protected function setConnection(Connection $connection)
	{
		$this->connection = $connection;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTable()
	{
		return $this->table;
	}

	/**
	 * @param string $table
	 * @return $this
	 */
	public function setTable($table)
	{
		$this->table = $table;
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
	public function close()
	{
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
		$query = Query::delete($this->getTable())
			->whereEqual('id',$session_id);

		if($query->execute() !== false)
			return true;
		else
			return false;
	}

	/**
	 * Cleanup old sessions
	 *
	 * @link http://php.net/manual/en/sessionhandlerinterface.gc.php
	 * @param int $php_maxLifetime
	 * @return bool
	 * The return value (usually TRUE on success, FALSE on failure).
	 * Note this value is returned internally to PHP for processing.
	 * @since 5.4.0
	 */
	public function gc($php_maxLifetime)
	{
		$sessionMaxLifeTime = Session::getInstance()->getMaxLifetime();
		$query = Query::delete($this->getTable(),$this->getConnection())
			->whereCondition('last_activity',Condition::GREATER, time() - ($sessionMaxLifeTime));

		if($query->execute() !== false)
			return true;
		else
			return false;
	}

	/**
	 * Initialize session
	 *
	 * @link http://php.net/manual/en/sessionhandlerinterface.open.php
	 * @param string $save_path The path where to store/retrieve the session.
	 * @param string $session_name The session name.
	 * @return bool
	 * The return value (usually TRUE on success, FALSE on failure).
	 * Note this value is returned internally to PHP for processing.
	 * @since 5.4.0
	 */
	public function open($save_path, $session_name)
	{
		return ($this->getConnection() instanceof Connection) ? true : false;
	}

	/**
	 * Read session data
	 *
	 * @link http://php.net/manual/en/sessionhandlerinterface.read.php
	 * @param string $session_id The session id to read data for.
	 * @return string
	 * Returns an encoded string of the read data.
	 * If nothing was read, it must return an empty string.
	 * Note this value is returned internally to PHP for processing.
	 * @since 5.4.0
	 */
	public function read($session_id)
	{
		$query = Query::select($this->getTable(),['payload'],$this->getConnection())
			->whereEqual('id',$session_id);

		if(($result = $query->execute()) !== false)
		{
			$record = $result->fetch(PDO::FETCH_OBJ);
			if(is_object($record))
			{
				if (Config::exists('session', 'encrypt_key'))
					return (string)Encrypt::decrypt(base64_decode($record->payload), Config::getValue('session', 'encrypt_key'));
				else
					return (string)$record->payload;
			}
		}
		return (string)'';
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
	 * @throws SessionException
	 */
	public function write($session_id, $session_data)
	{
		if(Config::exists('session','encrypt_key'))
			$payload = base64_encode(Encrypt::encrypt($session_data,Config::getValue('session','encrypt_key')));
		else
			$payload = $session_data;

		//Session Data
		$data = [
			'id'			=>	$session_id,
			'payload'		=>	$payload,
			'last_activity'	=>	time(),
		];

		//Find Query
		$queryFind = Query::select($this->getTable(),['payload'],$this->getConnection())
			->whereEqual('id',$session_id);

		//Write Query
		$query = Query::insert($this->getTable(),$data,$this->getConnection());

		if(($resultFind = $queryFind->execute()) !== false)
		{
			if(count($resultFind->fetchAll()) >= 1)
			{
				$query = Query::update($this->getTable(),$data,$this->getConnection())
					->whereEqual('id',$session_id);
			}
		}

		//Execute the write
		if($query->execute() !== false)
			return true;
		else
			throw new SessionException('Failed to write session data.');
	}
}