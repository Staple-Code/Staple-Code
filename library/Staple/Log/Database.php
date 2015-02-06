<?php
/** 
 * A class for logging information to the database.
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
namespace Staple\Log;

use \Staple\DB;
use \Staple\Config;

class Database extends Log
{
	public function last_DBError($applicationID = NULL)
	{
		$db = DB::get();
		$errmsg = $db->errno.': '.$db->error;
		return self::log_DBError($errmsg,$db->last_query,$applicationID);
	}
	
	public function Log($errmsg, $errsql = NULL, $applicationID = NULL)
	{
		$db = DB::get();
		$dbenc = Config::getValue('encrypt', 'key');
	
		$columns = 'occurred,error';
		$values = "NOW(), '".$db->escape_string($errmsg)."'";
	
		if(isset($errsql))
		{
			$ssnregex = '/^\d{3}\-\d{2}\-\d{4}$/';
			$errsql = preg_replace($ssnregex, 'SSN', $errsql);
			$columns .= ',`sql`';
			$values .= ",AES_ENCRYPT('".$db->escape_string($errsql)."','".$db->real_escape_string($dbenc)."')";
		}
		if(isset($applicationID))
		{
			$columns .= ',applicationID';
			$values .= ",'".((int)$applicationID)."'";
		}
	
		$sql = "INSERT INTO log_database_err ($columns) VALUES ($values)";
		if(($result = $db->query($sql)) === true)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}

?>