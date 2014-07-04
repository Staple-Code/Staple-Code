<?php
/**
 *
 * @author Ironpilot
 *        
 */
abstract class Staple_Log
{
	private $encryptLog = false;
	
	protected $dateAndTime;
	
	/**
	 */
	function __construct()
	{
		
	}
	
	
	
	abstract function Log();
}

?>