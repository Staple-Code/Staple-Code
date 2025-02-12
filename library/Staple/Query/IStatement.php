<?php
/**
* Interface for statement objects
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

interface IStatement
{
	public function fetch(int $fetch_style = PDO::ATTR_DEFAULT_FETCH_MODE, int $cursor_orientation = PDO::FETCH_ORI_NEXT, int $cursor_offset = 0): mixed;
	public function fetchAll(int $fetch_style = PDO::ATTR_DEFAULT_FETCH_MODE, mixed ...$args);
	public function rowCount();
	public function foundRows();
	public function setDriver(string $driver);
	public function getDriver();
	public function getConnection(): Connection;
	public function setConnection(Connection $connection);
	public function bindColumn(int|string $column, mixed &$var, int $type = PDO::PARAM_STR, int $maxLength = NULL, mixed $driverOptions = NULL);
	public function bindParam(int|string $param, mixed &$var, int $type = PDO::PARAM_STR, int $maxLength = NULL, mixed $driverOptions = NULL);
	public function bindValue(int|string $param, mixed $value, int $type = PDO::PARAM_STR);
	public function execute (array|null $params = NULL);
	public function errorInfo();
}