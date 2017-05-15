<?php
/**
* An extension of the PDO classes to supply a database connection to the framework.
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

use \SplSubject;

interface IConnection extends SplSubject
{
	public static function get();
	public function getDriver();
	public function getLastQuery();
	public function setLastQuery($lastQuery);
	public function exec($statement);
	public function query($statement);
	public function getDriverOptions();
	public function errorInfo();
	public function errorCode();
	public function getSchema();
	public function setSchema($schema);
}