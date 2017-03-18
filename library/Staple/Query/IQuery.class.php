<?php
/**
* Query class interface
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


interface IQuery
{
	//Execute the build function and return the result when converting to a string.
	public function __toString();
	//Get the query table
	public function getTable();
	//Retrieve Query Connection
	public function getConnection();
	//Set the query table
	public function setTable($table,$alias = NULL);
	//Set the query connection
	public function setConnection(IConnection $connection);
	//Build the query into a string.
	public function build();
	//Execute the query and returns the result.
	public function execute(IConnection $connection = NULL);
}