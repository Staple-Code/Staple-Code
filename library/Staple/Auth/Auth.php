<?PHP
/**
 * 
 * This class is the central class for site-wide authentication.
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

use Exception;
use Staple\Config;
use Staple\Error;
use Staple\Exception\AuthException;
use Staple\Exception\ConfigurationException;
use Staple\Exception\NotAuthorizedException;
use Staple\Exception\PageNotFoundException;
use Staple\Exception\RoutingException;
use Staple\Exception\SessionException;
use Staple\Exception\SystemException;
use Staple\Route;
use Staple\Session\Session;
use ReflectionClass, ReflectionMethod;

class Auth implements IAuthService
{
	const AUTH_FLAG_PROTECTED = '@protected';
	const AUTH_FLAG_OPEN = '@open';
	const AUTH_FLAG_LEVEL = '@auth-level';
	/**
	 * 
	 * Holds the singleton instance for the Auth class
	 * @var Auth
	 */
	private static $instance;
	
	/**
	 * 
	 * Holds a reference to the AuthAdapter object.
	 * @var AuthAdapter
	 */
	private $adapter;
	
	/**
	 * 
	 * Private variable holds the Auth status.
	 * @var bool
	 */
	private $authed = false;
	
	/**
	 * 
	 * Status message from the Auth class.
	 * @var string
	 */
	protected $message = '';
	/**
	 * This is the route that will be executed when authentication is unsuccessful.
	 * @var Route
	 */
	protected $defaultUnauthenticatedRoute;
	/**
	 * This is the last route that was attempted by the authentication system.
	 * @var Route
	 */
	protected $lastAttemptedRoute;
	
	/**
	 * 
	 * The private constructor ensures that the object is created as a Singleton. On initialization
	 * the class checks for a configuration file. If none is found it throws an Exception. If a
	 * config file is found, it parses the file and checks for the config keys required by this
	 * class.
	 * 
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->authed = false;
		try
		{
			$this->setDefaultUnauthenticatedRoute(Route::create(Config::getValue('auth', 'route')));
		}
		catch(Exception $e)
		{
			$this->defaultUnauthenticatedRoute = new Route('index/index');
		}
	}
	
	/**
	 * The destructor store the auth instance in the session.
	 */
	public function __destruct()
	{
		if(isset(self::$instance))
			Session::auth(self::$instance);
	}
	
	/**
	 * 
	 * Gets the singleton instance of the object. Checks the session to see if a current auth
	 * object already exists. If not a new Auth object is created.
	 * @throws SessionException
	 * @return Auth
	 */
	public static function get()
	{
		if(!(self::$instance instanceof Auth))
		{
			//Start session if not already done.
			if(Session::getInstance()->isSessionStarted() == false)
				Session::start();

			//Restore the Auth session, if exists.
			self::restoreAuthSession();

			//If none, make a new auth instance.
			if(!(self::$instance instanceof Auth))
				self::$instance = new Auth();
		}
		return self::$instance;
	}
	
	/**
	 * 
	 * Returns a boolean representing authorization status. True for any level of authorization,
	 * false for no authorization.
	 * @return bool
	 */
	public function isAuthed()
	{
		return $this->authed;
	}

	/**
	 * This is a pass-through method to the AuthAdapter authRoute() method.
	 * @param Route $route
	 * @param $requiredLevel
	 * @param ReflectionClass $reflectionClass
	 * @param ReflectionMethod $reflectionMethod
	 * @return bool
	 */
	public function authRoute(Route $route, $requiredLevel, ReflectionClass $reflectionClass, ReflectionMethod $reflectionMethod)
	{
		return $this->adapter->authRoute($route, $requiredLevel, $reflectionClass, $reflectionMethod);
	}

	/**
	 * @return AuthAdapter
	 */
	public function getAuthAdapter()
	{
		return $this->adapter;
	}
	
	/**
	 * Returns and integer representing the level of access. Defaults to 0 for no auth and 1 
	 * for general authorization. This is derived from information gathered by the AuthAdapter.
	 * @return mixed
	 */
	public function getAuthLevel()
	{
		if(!($this->adapter instanceof AuthAdapter))
		{
			$this->createAuthAdapter();
		}
		return $this->adapter->getLevel();
	}
	
	/**
	 * 
	 * Returns the Auth ID
	 * @return int | string
	 */
	public function getAuthId()
	{
		if(!($this->adapter instanceof AuthAdapter))
		{
			$this->createAuthAdapter();
		}
		return $this->adapter->getUserId();
	}

	/**
	 * Return the default unauthenticated route.
	 * @return Route
	 */
	protected function getDefaultUnauthenticatedRoute(): Route
	{
		return $this->defaultUnauthenticatedRoute;
	}

	/**
	 * Set a new route for the default unauthenticated route.
	 * @param Route $defaultUnauthenticatedRoute
	 * @return Auth
	 */
	public function setDefaultUnauthenticatedRoute(Route $defaultUnauthenticatedRoute): Auth
	{
		$this->defaultUnauthenticatedRoute = $defaultUnauthenticatedRoute;

		return $this;
	}

	/**
	 * Get the last attempted route by the authentication system.
	 * @return Route|null
	 */
	public function getLastAttemptedRoute()
	{
		return $this->lastAttemptedRoute;
	}

	/**
	 * Set the last attempted route.
	 * @param Route $lastAttemptedRoute
	 * @return Auth
	 */
	protected function setLastAttemptedRoute(Route $lastAttemptedRoute): Auth
	{
		if ($lastAttemptedRoute->getType() == Route::ROUTE_FUNCTIONAL)
		{
			$this->lastAttemptedRoute = Route::create($lastAttemptedRoute->getRouteString());
		}
		else
		{
			$this->lastAttemptedRoute = $lastAttemptedRoute;
		}

		return $this;
	}

	/**
	 * 
	 * Attempts authorization, accepting credentials and forwarding them to the AuthAdapter.
	 * Throws and Exception if the AuthAdapter is not implemented from Staple_AuthAdapter. 
	 * Returns a boolean to signify if authorization succeeded.
	 * @param mixed $credentials
	 * @throws SessionException
	 * @throws SystemException
	 * @throws ConfigurationException
	 * @throws NotAuthorizedException
	 * @return bool
	 */
	public function doAuth($credentials)
	{
		//Make sure an adapter is loaded.
		if(!($this->adapter instanceof AuthAdapter))
		{
			$this->createAuthAdapter();
		}
		
		//Check Auth against the adapter
		if($this->adapter->getAuth($credentials) === true)
		{
			Session::getInstance()->regenerate(true);
			$this->authed = true;
			$this->message = "Authentication Successful";
			self::writeSession();
			return true;
		}
		else
		{
			$this->authed = false;
			$this->message = "Authentication Failed";
			self::writeSession();
			return false;
		}
	}

	/**
	 * Create the AuthAdapter with the option to supply a custom adapter.
	 * @param AuthAdapter|null $adapter
	 * @throws SystemException
	 * @throws ConfigurationException
	 * @return bool
	 */
	private function createAuthAdapter(AuthAdapter $adapter = null)
	{
		if($adapter instanceof AuthAdapter)
		{
			$this->adapter = $adapter;
			return true;
		}
		else
		{
			$configuredAdapter = Config::getValue('auth', 'adapter');
			if(class_exists($configuredAdapter))
			{
				$this->adapter = new $configuredAdapter();
				if(!($this->adapter instanceof AuthAdapter))
				{
					throw new SystemException('Invalid Authentication Adapter', Error::AUTH_ERROR);
				}
				return true;
			}
			else
			{
				throw new SystemException('Adapter Class Not Found', Error::AUTH_ERROR);
			}
		}
	}

	/**
	 * Implement a new authentication adapter. This also clears any current authentication that exists.
	 * @param AuthAdapter $adapter
	 * @throws SystemException
	 * @throws ConfigurationException
	 * @return bool
	 */
	public function implementAuthAdapter(AuthAdapter $adapter)
	{
		return $this->resetAuth($adapter);
	}

	/**
	 * Returns the instance type of the auth adapter.
	 * @return string
	 */
	public function getAdapterImplementation()
	{
		return get_class($this->adapter);
	}
	
	/**
	 * In the event that authorization fails, this method is called by the framework. noAuth() 
	 * dispatches to the AuthController -> index action.
	 * This method accepts an optional route parameter that can be sent forward to the auth controller
	 * which will allow the developer to react to the route that was requested.
	 * @param Route $attemptedRoute
	 * @param Route $routeTo
	 * @return bool
	 * @throws AuthException
	 * @throws RoutingException
	 * @throws PageNotFoundException
	 */
	public function noAuth(Route $attemptedRoute = null, Route $routeTo = null)
	{
		//Break a potential infinite loop
		if($this->getLastAttemptedRoute() instanceof Route)
		{
			$defaultRoute = $this->getDefaultUnauthenticatedRoute();
			if($attemptedRoute->getController() === $this->getLastAttemptedRoute()->getController()
				&& $attemptedRoute->getAction() === $this->getLastAttemptedRoute()->getAction()
				&& $attemptedRoute->getController() !== $defaultRoute->getController()
				&& $attemptedRoute->getAction() !== $defaultRoute->getAction())
			{
				throw new AuthException('Not Authorized');
			}
		}

		$this->setLastAttemptedRoute($attemptedRoute);
		if($routeTo instanceof Route)
		{
			return $routeTo->execute();
		}
		else
		{
			$route = $this->getDefaultUnauthenticatedRoute();
			return $route->execute();
		}
	}

	/**
	 * General log out or clear credentials function.
	 * @param AuthAdapter|null $adapter
	 * @return bool
	 * @throws ConfigurationException
	 * @throws SystemException
	 */
	public function clearAuth(AuthAdapter $adapter = null): bool
	{
		if (isset($this->adapter))
		{
			$this->adapter->clear();
		}
		$this->authed = false;
		$this->message = 'Logged Out';
		return true;
	}

	/**
	 * General log out or clear credentials function.
	 * @param AuthAdapter|null $adapter
	 * @return bool
	 * @throws ConfigurationException
	 * @throws SystemException
	 */
	public function resetAuth(AuthAdapter $adapter = null): bool
	{
		$this->createAuthAdapter($adapter);
		$this->authed = false;
		$this->message = 'Logged Out';
		return true;
	}
	
	/**
	 * 
	 * Returns the Auth message from the class
	 * @return string
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * Write the auth object to the session
	 * @throws SessionException
	 */
	private static function writeSession()
	{
		//Start session if not already done.
		if(Session::getInstance()->isSessionStarted() == false)
			Session::start();

		Session::auth(self::$instance);
	}

	/**
	 * Restore the session to the instance static variable.
	 * @return self
	 */
	private static function restoreAuthSession()
	{
		//Restore the auth object from the session.
		$auth = Session::auth();
		if($auth instanceof Auth)
			self::$instance = Session::auth();
		return $auth;
	}
}