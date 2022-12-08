<?php

/** 
 * The AuthAdapter interface creates a preset list of functions that must be implemented
 * to allow authentication to pass through Staple\Auth\Auth. Adapters sent to Staple\Auth\Auth must
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
namespace Staple\Auth;

use Staple\Exception\NotAuthorizedException;
use Staple\Route;

interface AuthAdapter
{
	/**
	 * This function must be implemented to check the authorization based on the adapter 
	 * at hand. The function must return a boolean true for the Staple_Auth object to view
	 * authentication as successful. If a non-boolean true is returned, authentication will
	 * fail.
	 * @param mixed $credentials
	 * @throws NotAuthorizedException
	 * @return bool
	 */
	public function getAuth($credentials): bool;
	/**
	 * 
	 * This function must be implemented to return a numeric level of access. This level is
	 * used to determine feature access based on account type.
	 * @return mixed
	 */
	public function getLevel();
	/**
	 * Returns the User ID from the adapter.
	 * @return mixed
	 */
	public function getUserId();
	/**
	 * This method should return a boolean true or false if the supplied auth level is able
	 * to access the supplied route. Additionally the reflection for the class and method
	 * are also supplied to this method for further verification. If there are no roles or
	 * levels in the supplied application, this method can simply return true to disable this
	 * check.
	 * @param Route $route
	 * @param $requiredLevel
	 * @param \ReflectionClass|null $reflectionClass
	 * @param \ReflectionMethod|null $reflectionMethod
	 * @return bool
	 */
	public function authRoute(Route $route, $requiredLevel, \ReflectionClass $reflectionClass = null, \ReflectionMethod $reflectionMethod = null): bool;

	/**
	 * This method should clear out any existing authentication stored in the adapter and reset
	 * it to it's starting state. Returns a bool true on success or false on failure.
	 * @return bool
	 */
	public function clear(): bool;
}