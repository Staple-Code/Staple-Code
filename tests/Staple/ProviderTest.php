<?php
/**
 * Created by PhpStorm.
 * User: scott.henscheid
 * Date: 8/22/2017
 * Time: 11:36 AM
 */

namespace Staple\Tests;

use PHPUnit\Framework\TestCase;
use Staple\Auth\Auth;
use Staple\Auth\AuthAdapter;
use Staple\Exception\AuthException;
use Staple\Exception\PageNotFoundException;
use Staple\Exception\RoutingException;
use Staple\Request;
use Staple\Route;
use Staple\Traits\AuthRoute;

class FakeProviderAuthAdapter implements AuthAdapter
{
	use AuthRoute;

	const USER_KEY = 'username';
	const PASS_KEY = 'password';
	const TEST_USERNAME = 'testusername';
	const TEST_PASSWORD = 'test&P@ssword';

	/** @var mixed */
	private $userId;
	/** @var int */
	private $userLevel = 0;

	/**
	 * This function must be implemented to check the authorization based on the adapter
	 * at hand. The function must return a boolean true for the Staple_Auth object to view
	 * authentication as successful. If a non-boolean true is returned, authentication will
	 * fail.
	 *
	 * @param mixed $credentials
	 * @return bool
	 */
	public function getAuth($credentials): bool
	{
		if(is_array($credentials))
		{
			if(strlen($credentials[self::USER_KEY]) > 0 && strlen($credentials[self::PASS_KEY]) > 0)
			{
				$hashedPass = password_hash(self::TEST_PASSWORD, PASSWORD_DEFAULT);
				if(strtolower($credentials[self::USER_KEY]) == self::TEST_USERNAME)
				{
					if(password_verify($credentials[self::PASS_KEY], $hashedPass))
					{
						$this->setUserId($credentials);
						$this->setUserLevel(1);
						return true;
					}
				}
			}
		}
		return false;
	}

	/**
	 * This function must be implemented to return a numeric level of access. This level is
	 * used to determine feature access based on account type.
	 *
	 * @return int
	 */
	public function getLevel()
	{
		return $this->userLevel;
	}

	/**
	 * @param $level
	 */
	private function setUserLevel($level)
	{
		$this->userLevel = (int)$level;
	}

	/**
	 * Returns the User ID from the adapter.
	 *
	 * @return mixed
	 */
	public function getUserId()
	{
		return $this->userId;
	}

	/**
	 * @param mixed $user
	 */
	private function setUserId($user)
	{
		$this->userId = $user;
	}
}

class ProviderTest extends TestCase
{
	const ROUTE_TEXT = 'test/text';
	const ROUTE_JSON = 'test/json';
	const ROUTE_SAME = 'test/same';
	const ROUTE_OPTIONS = 'test/options-test';
	const ROUTE_PROTECTED = 'test/protected';

	protected function setUp()
	{
		//Clear auth before each test.
		Auth::get()->clearAuth();
	}

	public function testRouting()
	{
		//Text Route
		Request::fake(self::ROUTE_TEXT, Request::METHOD_GET);
		$route = Route::create(self::ROUTE_TEXT);
		ob_start();
		$route->execute();
		$textBuffer = ob_get_contents();
		ob_end_clean();

		$this->assertEquals('test', $textBuffer);

		//JSON Route
		Request::fake(self::ROUTE_JSON, Request::METHOD_POST);
		ob_start();
		Route::create(self::ROUTE_JSON)->execute();
		$jsonBuffer = ob_get_contents();
		ob_end_clean();

		$this->assertEquals('{"make":"Honda","model":"Accord"}', $jsonBuffer);
	}

	public function testRouteNotFound()
	{
		$this->expectException(PageNotFoundException::class);

		Request::fake(self::ROUTE_JSON, Request::METHOD_PUT);
		Route::create(self::ROUTE_JSON)->execute();
	}

	public function testDifferentReturnForDifferentMethods()
	{
		//Get Route
		Request::fake(self::ROUTE_SAME, Request::METHOD_GET);
		$route = Route::create(self::ROUTE_SAME);
		ob_start();
		$route->execute();
		$textBuffer = ob_get_contents();
		ob_end_clean();

		$this->assertEquals('{"data":"Same","status":"Success"}', $textBuffer);

		//POST Route
		Request::fake(self::ROUTE_SAME, Request::METHOD_POST);
		$route = Route::create(self::ROUTE_SAME);
		ob_start();
		$route->execute();
		$textBuffer = ob_get_contents();
		ob_end_clean();

		$this->assertEquals('', $textBuffer);

		//PUT Route
		Request::fake(self::ROUTE_SAME, Request::METHOD_PUT);
		$route = Route::create(self::ROUTE_SAME);
		ob_start();
		$route->execute();
		$textBuffer = ob_get_contents();
		ob_end_clean();

		$this->assertEquals('Some Data', $textBuffer);
	}

	public function testOptions()
	{
		//OPTIONS Route
		Request::fake(self::ROUTE_OPTIONS, Request::METHOD_OPTIONS);
		$route = Route::create(self::ROUTE_OPTIONS);
		ob_start();
		$route->execute();
		$textBuffer = ob_get_contents();
		ob_end_clean();

		$this->assertEquals('', $textBuffer);
	}

	public function testOptionsThrowsExceptionOnMissingOptionsMethod()
	{
		//POST Route
		$this->expectException(RoutingException::class);
		Request::fake(self::ROUTE_JSON, Request::METHOD_OPTIONS);
		Route::create(self::ROUTE_JSON)->execute();
	}

	public function testAccessingProtectedEndpointWithoutAuthReturnsNotAuthorized()
	{
		//Get Route
		Request::fake(self::ROUTE_PROTECTED, Request::METHOD_GET);
		$route = Route::create(self::ROUTE_PROTECTED);
		$this->expectException(AuthException::class);
		$route->execute();
	}

	public function testAuthenticatedRoutingWithMethodProtection()
	{
		//Setup Auth Object
		$auth = Auth::get();
		$auth->implementAuthAdapter(new FakeCtrlAuthAdapter());

		$auth->doAuth([
			'username' => 'testusername',
			'password' => 'test&P@ssword'
		]);
		Request::fake(self::ROUTE_PROTECTED, Request::METHOD_GET);
		$route = Route::create(self::ROUTE_PROTECTED);
		ob_start();
		$route->execute();
		$textBuffer = ob_get_contents();
		ob_end_clean();

		$this->assertEquals('{"request":"Successful","data":"This is secure data."}', $textBuffer);
		$this->assertTrue($auth->isAuthed());
	}
}