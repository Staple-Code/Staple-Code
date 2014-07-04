<?php

/** 
 * The Staple_AuthAdapter interface creates a preset list of functions that must be implemented
 * to allow authentication to pass through Staple_Auth. Adapters sent to Staple_Auth must
 * implement this class or an error will occur. 
 *  
 * @author Ironpilot
 * @copyright Copywrite (c) 2011, STAPLE CODE
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
 * 
 */
interface Staple_AuthAdapter
{
	/**
	 * This function must be implemented to check the authorization based on the adapter 
	 * at hand. The function must return a boolean true for the Staple_Auth object to view
	 * authentication as successful. If a non-boolean true is returned, authentication will
	 * fail.
	 * @return bool
	 */
	public function getAuth(array $credentials);
	/**
	 * 
	 * This function must be implemented to return a numeric level of access. This level is
	 * used to determine feature access based on account type.
	 * @return int
	 */
	public function getLevel($uid);
	
	/**
	 * Returns the userid from the adapater
	 * @return string
	 */
	public function getUserId();
}

?>