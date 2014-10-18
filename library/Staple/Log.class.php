<?php
/**
 *
 * @author Ironpilot
 *        
 */
namespace Staple;

abstract class Log
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