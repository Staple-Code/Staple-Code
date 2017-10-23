<?php
/**
 * Created by PhpStorm.
 * User: ironpilot
 * Date: 10/22/2017
 * Time: 9:46 AM
 */

namespace Staple\Auth;


use Staple\Exception\AuthException;
use Staple\Route;

interface IAuthService
{
	/**
	 * Returns a boolean representing authorization status. True for any level of authorization,
	 * false for no authorization.
	 * @return bool
	 */
	public function isAuthed();

	/**
	 * This is a pass-through method to the AuthAdapter authRoute() method.
	 * @param Route $route
	 * @param $requiredLevel
	 * @param \ReflectionClass $reflectionClass
	 * @param \ReflectionMethod $reflectionMethod
	 * @return bool
	 */
	public function authRoute(Route $route, $requiredLevel, \ReflectionClass $reflectionClass, \ReflectionMethod $reflectionMethod);

	/**
	 * Returns and integer representing the level of access. Defaults to 0 for no auth and 1
	 * for general authorization. This is derived from information gathered by the AuthAdapter.
	 * @return mixed
	 */
	public function getAuthLevel();

	/**
	 * Returns the Auth ID
	 * @return int | string
	 */
	public function getAuthId();

	/**
	 * Set a new route for the default unauthenticated route.
	 * @param Route $defaultUnauthenticatedRoute
	 * @return Auth
	 */
	public function setDefaultUnauthenticatedRoute(Route $defaultUnauthenticatedRoute): Auth;

	/**
	 * Get the last attempted route by the authentication system.
	 * @return Route|null
	 */
	public function getLastAttemptedRoute();

	/**
	 *
	 * Attempts authorization, accepting credentials and forwarding them to the AuthAdapter.
	 * Throws and Exception if the AuthAdapter is not implemented from Staple_AuthAdapter.
	 * Returns a boolean to signify if authorization succeeded.
	 * @param mixed $credentials
	 * @throws AuthException
	 * @return bool
	 */
	public function doAuth($credentials);

	/**
	 * Implement a new authentication adapter. This also clears any current authentication that exists.
	 * @param AuthAdapter $adapter
	 * @return bool
	 */
	public function implementAuthAdapter(AuthAdapter $adapter);

	/**
	 * In the event that authorization fails, this method is called by the framework. noAuth()
	 * dispatches to the AuthController -> index action.
	 * This method accepts an optional route parameter that can be sent forward to the auth controller
	 * which will allow the developer to react to the route that was requested.
	 * @param Route $attemptedRoute
	 * @param Route $routeTo
	 * @return bool
	 * @throws AuthException
	 */
	public function noAuth(Route $attemptedRoute = null, Route $routeTo = null);

	/**
	 *
	 * General log out or clear credentials function.
	 */
	public function clearAuth();

	/**
	 * Returns the Auth message from the class
	 * @return string
	 */
	public function getMessage();
}