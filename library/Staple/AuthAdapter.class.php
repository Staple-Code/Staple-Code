<?php

/** 
 * The AuthAdapter interface creates a preset list of functions that must be implemented
 * to allow authentication to pass through Auth. Adapters sent to Auth must
 * implement this class or an error will occur. 
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
 * 
 */
namespace Staple;

interface AuthAdapter
{
	/**
	 * This function checks the authorization based on the adapter at hand. The function must
	 * return a boolean true for the Auth object to view authentication as successful. If a
	 * non-boolean true is returned, authentication will fail.
	 * @param mixed $credentials
	 * @return bool
	 */
	public function getAuth($credentials);
	/**
	 * This function returns a numeric level of access. This level is used to determine action
	 * access based on account type.
	 * @param mixed $uid
	 * @return int
	 */
	public function getLevel($uid);
	
	/**
	 * Returns the UserId from the adapter
	 * @return mixed
	 */
	public function getUserId();
}