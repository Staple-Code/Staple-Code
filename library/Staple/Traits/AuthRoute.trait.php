<?php
/**
 * Created by PhpStorm.
 * User: ironpilot
 * Date: 10/11/2017
 * Time: 1:29 PM
 */

namespace Staple\Traits;


use Staple\Route;

trait AuthRoute
{
	/**
	 * This is a default Auth Level validation algorithm. The required level is matched with the method's
	 * required level. If the level is greater than the required level then authorization is granted.
	 * @param Route $route
	 * @param $requiredLevel
	 * @param \ReflectionClass|null $reflectionClass
	 * @param \ReflectionMethod|null $reflectionMethod
	 * @return bool
	 */
	public function authRoute(Route $route, $requiredLevel, \ReflectionClass $reflectionClass = null, \ReflectionMethod $reflectionMethod = null): bool
	{
		$userLevel = $this->getLevel();
		if(is_numeric($requiredLevel))
		{
			if($userLevel >= $requiredLevel)
			{
				return true;
			}
		}
		elseif ($requiredLevel == $userLevel)
		{
			return true;
		}
		return false;
	}
}