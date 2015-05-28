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

use \PDOStatement, \PDO;

class Statement extends PDOStatement
{
    /**
     * The database driver that is currently in use.
     * @var string
     */
    protected $driver;

	/*
	 * Magic method to fake MySQLi property functions
	 * @deprecated
	 */
	public function __get($name)
	{
		switch($name)
		{
			case 'num_rows':
				return $this->rowCount();
				break;
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
    public function setDriver($driver)
    {
        $this->driver = $driver;
    }

	/**
	 * Mysqli style associative array fetch style
	 * @return mixed
	 * @deprecated
	 */
	public function fetch_assoc()
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
     */
    public function foundRows()
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
	 */
	public function rowCount()
	{
		switch($this->getDriver())
		{
			case Connection::DRIVER_SQLSRV:
				return (int)Query::raw('SELECT @@Rowcount')->fetchColumn(0);
			default:
				return parent::rowCount();
		}
	}
}