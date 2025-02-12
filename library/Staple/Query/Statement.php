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
	public function __get($name)
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
    public function getDriver()
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
     * Returns the connection object
     *
     * @return Connection The connection object used by the application
     */
	public function getConnection(): Connection
	{
		return $this->connection;
	}

    /**
     * Sets the connection for the statement
     *
     * @param Connection $connection The connection to set for the statement
     * @return IStatement Returns the instance of the statement for method chaining
     */
	public function setConnection(Connection $connection): IStatement
	{
		$this->connection = $connection;
		return $this;
	}

    /**
     * Binds a parameter to the statement.
     *
     * @param int|string $param The parameter identifier.
     * @param mixed $var Reference to the variable to bind.
     * @param int $type (optional) Data type of the parameter (default is PDO::PARAM_STR).
     * @param int|null $maxLength (optional) A hint for the driver to optimize performance (default is null).
     * @param mixed|null $driverOptions (optional) Additional driver options (default is null).
     * @return bool True on success, false on failure.
     */
	public function bindParam(int|string $param, mixed &$var, int $type = PDO::PARAM_STR, int $maxLength = null, mixed $driverOptions = null): bool
    {
		$this->params[$param] = $var;
		return parent::bindParam($param, $var, $type, $maxLength, $driverOptions);
	}

    /**
     * Binds a column to a variable for retrieval with a specific data type, maximum length, and driver options.
     *
     * @param int|string $column The column to bind to
     * @param mixed &$var The variable to bind the column value to
     * @param int $type The data type of the variable (default: PDO::PARAM_STR)
     * @param int $maxLength The maximum length of the data (default: null)
     * @param mixed $driverOptions The driver options for the binding (default: null)
     * @return mixed
     */
	public function bindColumn(int|string $column, mixed &$var, int $type = PDO::PARAM_STR, int $maxLength = null, mixed $driverOptions = null): bool
    {
		$this->params[$column] = $var;
		return parent::bindColumn($column, $var, $type, $maxLength, $driverOptions);
	}

	public function bindValue(int|string $param, mixed $value, int $type = PDO::PARAM_STR): bool
    {
		$this->params[$param] = $value;
		return parent::bindValue($param, $value, $type);
	}

	/**
	 * Mysqli style associative array fetch style
	 * @return mixed
	 * @deprecated
	 */
	public function fetch_assoc(): mixed
    {
		return $this->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * Mysqli style standard array fetch style
	 * @return mixed
	 * @deprecated
	 */
	public function fetch_array()
	{
		return $this->fetch(PDO::FETCH_BOTH);
	}

    /**
     * Returns the number of rows found in the previous query.
     * @return string
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
	public function execute(array|null $params = null): bool
    {
		$this->getConnection()->addQueryToLog($this->queryString, $this->params);
		return parent::execute($params);
	}


}