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
	public function fetch($fetch_style = PDO::ATTR_DEFAULT_FETCH_MODE, $cursor_orientation = PDO::FETCH_ORI_NEXT, $cursor_offset = 0);
	public function fetchAll($fetch_style = PDO::ATTR_DEFAULT_FETCH_MODE, $fetch_argument = NULL, $ctor_args = array());
	public function rowCount();
	public function foundRows();
	public function setDriver($driver);
	public function getDriver();
	public function bindColumn($column, &$param, $type = NULL, $maxlen = NULL, $driverdata = NULL);
	public function bindParam($parameter, &$variable, $data_type = PDO::PARAM_STR, $length = NULL, $driver_options = NULL);
	public function bindValue($parameter, $value, $data_type = PDO::PARAM_STR);
}