<?php
/**
 * A mock version of the Connection object. It will allow a connection to the database
 * to be established, but it will return mock results when anything is executed or
 * queried.
 *
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
 */

namespace Staple\Query;

use PDOStatement, SplObjectStorage;

class MockConnection extends Connection implements IConnection
{
	/**
	 * Results to be returned from a query or exec call.
	 * @var mixed
	 */
	private $results;
	/**
	 * @param $dsn
	 * @param string $username
	 * @param string $password
	 * @param array $options
	 */
	public function __construct($dsn, $username = null, $password = null, array $options = array())
	{
		self::$_observers = new SplObjectStorage();

		try
		{
			if(!isset($this->driver)) $this->setDriver(self::getDriverFromDsn($dsn));

			if(isset($username))
				$this->setUsername($username);
			if(isset($password))
				$this->setPassword($password);

			//Set the options property
			$this->setOptions($options);

			parent::__construct($dsn, $username, $password, $options);
		}
		catch(\PDOException $e)
		{
			//This is normal. Ignore and move on.
		}
	}

	/**
	 * @param string $statement
	 * @return PDOStatement | boolean
	 */
	public function exec($statement)
	{
		$statement = (string)$statement;
		$this->addQueryToLog($statement);
		$this->notify();
		return $this->getResults();
	}

	/**
	 * @param string $statement
	 * @return Statement
	 */
	public function query($statement)
	{
		$statement = (string)$statement;
		$this->addQueryToLog($statement);
		$this->notify();
		return $this->getResults();
	}

	public function prepare($statement, $options = NULL)
	{
		$statement = (string)$statement;
		$this->addQueryToLog($statement);
		$this->notify();
		return new MockStatement();
	}

	/**
	 * Get the preset results
	 * @return mixed
	 */
	private function getResults()
	{
		return $this->results;
	}

	/**
	 * @param mixed $results
	 * @return $this
	 */
	public function setResults($results)
	{
		$this->results = $results;
		return $this;
	}

	public function quote($string, $parameter_type = \PDO::PARAM_STR)
	{
		if(is_string($string) || is_float($string))
		{
			return "'".$string."'";
		}
		else
		{
			return $string;
		}
	}
}