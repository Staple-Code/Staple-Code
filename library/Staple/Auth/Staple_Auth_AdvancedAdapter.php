<?php

/** 
 * @author Ironpilot
 * 
 * 
 */
class Staple_Auth_AdvancedAdapter
{
	/**
	 * This function must be implemented to check the authorization based on the adapter 
	 * at hand. The function must return a boolean true for the Staple_Auth object to view
	 * authentication as successful. If a non-boolean true is returned, authentication will
	 * fail.
	 * @return bool
	 */
	public function getAuth($credentials);
	/**
	 * 
	 * This function must be implemented to return a numeric level of access. This level is
	 * used to determine feature access based on account type.
	 * @return int
	 */
	public function getAccess($route);
	
	/**
	 * Returns the userid from the adapater
	 * @return string
	 */
	public function getUserId();
	
	public function noAccess();
	
	public function doLogin();
	
	public function afterLogin();
}

?>