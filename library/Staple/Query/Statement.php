<?php
/**
 * An extension of the PDOStatement class.
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

use PDO;
use PDOStatement;
use Staple\Exception\ConfigurationException;

class Statement extends PDOStatement implements IStatement
{
    /**
     * The database driver that is currently in use.
     * @var string
     */
    protected string $driver;

    /**
	 * The data store connection.
     * @var Connection
     */
    protected Connection $connection;

	/**
	 * The bound parameters of the query
	 * @var array
	 */
	protected array $params = [];

	/**
	 * Magic method to fake MySQLi property functions
	 * @deprecated
	 * @param string $name
	 * @return int|null
	 * @throws ConfigurationException
	 */
	public function __get(string $name)
	{
		switch($name)
		{
			case 'num_rows':
				return $this->rowCount();
			default:
				return NULL;
		}
	}

    /**
     * Get the driver string
     * @return string
     */
    public function getDriver(): string
    {
        return $this->driver;
    }

    /**
     * Set the driver string
     * @param string $driver
     */
    public function setDriver(string $driver): void
	{
        $this->driver = $driver;
    }

	/**
	 * @return IConnection
	 */
	public function getConnection(): IConnection
	{
		return $this->connection;
	}

	/**
	 * @param IConnection $connection
	 * @return IStatement
	 */
	public function setConnection(IConnection $connection): IStatement
	{
		$this->connection = $connection;
		return $this;
	}

	public function bindParam(string|int $param, mixed &$var, int $type = PDO::PARAM_STR, int $maxLength = null, mixed $driverOptions = null): bool
	{
		$this->params[$param] = $var;
		return parent::bindParam($param, $var, $type, $maxLength, $driverOptions);
	}

	public function bindColumn(int|string $column, mixed &$var, $type = PDO::PARAM_STR, $maxLength = null, $driverOptions = null): bool
	{
		$this->params[$column] = $var;
		return parent::bindColumn($column, $var, $type, $maxLength, $driverOptions);
	}

	public function bindValue(string|int $param, mixed $value, int $type = PDO::PARAM_STR): bool
	{
		$this->params[$param] = $value;
		return parent::bindValue($param, $value, $type);
	}

    /**
     * Returns the number of rows found in the previous query.
     * @return int|string
	 * @throws ConfigurationException
     */
    public function foundRows(): int|string
	{
        switch($this->getDriver())
        {
            case Connection::DRIVER_MYSQL:
                return (int)Query::raw('SELECT FOUND_ROWS()')->fetchColumn(0);
            case Connection::DRIVER_SQLSRV:
                return (int)Query::raw('SELECT @@Rowcount')->fetchColumn(0);
            default:
                return count($this->fetchAll(PDO::FETCH_COLUMN, 0));
        }
    }

	/**
	 * Override the PDOStatement rowCount() method to return
	 * @return int
	 * @throws ConfigurationException
	 */
	public function rowCount(): int
	{
		switch($this->getDriver())
		{
			case Connection::DRIVER_SQLSRV:
				return (parent::rowCount() == -1) ? (int)Query::raw('SELECT @@Rowcount')->fetchColumn(0) : parent::rowCount();
			default:
				return parent::rowCount();
		}
	}

	/**
	 * @param array|null $params
	 * @return bool
	 */
	public function execute(array $params = null): bool
	{
		$this->getConnection()->addQueryToLog($this->queryString, $this->params);
		return parent::execute($params);
	}


}