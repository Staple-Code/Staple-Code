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
}