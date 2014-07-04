<?php
/**
 *
 * @author Ironpilot
 *        
 */
class Staple_Log_Database extends Staple_Log
{
	public function last_DBError($applicationID = NULL)
	{
		$db = Staple_DB::get();
		$errmsg = $db->errno.': '.$db->error;
		return self::log_DBError($errmsg,$db->last_query,$applicationID);
	}
	
	public function Log($errmsg, $errsql = NULL, $applicationID = NULL)
	{
		$db = Staple_DB::get();
		$dbenc = Staple_Config::getValue('encrypt', 'key');
	
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