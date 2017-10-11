<?php
/**
 * Unit Tests for \Staple\Controller object
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
 */
namespace Staple\Tests;

use PHPUnit\Framework\TestCase;
use Staple\Auth\Auth;
use Staple\Auth\AuthAdapter;
use Staple\Request;
use Staple\Route;

class FakeCtrlAuthAdapter implements AuthAdapter
{
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

class ControllerTest extends TestCase
{
	const ROUTE_VIEW = 'test/index';
	const ROUTE_AUTHENTICATED = 'test/authenticated';
	const ROUTE_PROTECTED = 'protected/data';
	const ROUTE_UNPROTECTED_VIEW = 'protected/index';

	public function testRouting()
	{
		//View Route
		Request::fake(self::ROUTE_VIEW, Request::METHOD_GET);
		$route = Route::create(self::ROUTE_VIEW);
		ob_start();
		$route->execute();
		$textBuffer = ob_get_contents();
		ob_end_clean();

		$this->assertEquals('This is a test View.', $textBuffer);
	}

	public function testAuthenticatedRouting()
	{
		//Setup Auth Object
		$auth = Auth::get();
		$auth->implementAuthAdapter(new FakeCtrlAuthAdapter());
		$auth->setDefaultUnauthenticatedRoute(Route::create(self::ROUTE_VIEW));

		//Authed Route - Not Logged In.
		Request::fake(self::ROUTE_AUTHENTICATED, Request::METHOD_GET);
		$route = Route::create(self::ROUTE_AUTHENTICATED);
		ob_start();
		$route->execute();
		$textBuffer = ob_get_contents();
		ob_end_clean();

		$this->assertFalse($auth->isAuthed());
		$this->assertNotEquals('Authenticated Content', $textBuffer);

		$auth->doAuth([
			'username' => 'testusername',
			'password' => 'test&P@ssword'
		]);
		Request::fake(self::ROUTE_AUTHENTICATED, Request::METHOD_GET);
		$route = Route::create(self::ROUTE_AUTHENTICATED);
		ob_start();
		$route->execute();
		$textBuffer = ob_get_contents();
		ob_end_clean();

		$this->assertEquals('Authenticated Content', $textBuffer);
		$this->assertTrue($auth->isAuthed());
	}

	public function testAuthenticatedRoutingWithGlobalControllerProtection()
	{
		//View Route
		Request::fake(self::ROUTE_UNPROTECTED_VIEW, Request::METHOD_GET);
		$route = Route::create(self::ROUTE_UNPROTECTED_VIEW);
		ob_start();
		$route->execute();
		$textBuffer = ob_get_contents();
		ob_end_clean();

		$this->assertEquals('This is a test View.', $textBuffer);

		//Setup Auth Object
		$auth = Auth::get();
		$auth->implementAuthAdapter(new FakeCtrlAuthAdapter());
		$auth->setDefaultUnauthenticatedRoute(Route::create(self::ROUTE_VIEW));

		//Authed Route - Not Logged In.
		Request::fake(self::ROUTE_PROTECTED, Request::METHOD_GET);
		$route = Route::create(self::ROUTE_PROTECTED);
		ob_start();
		$route->execute();
		$textBuffer = ob_get_contents();
		ob_end_clean();

		$this->assertFalse($auth->isAuthed());
		$this->assertEquals('This is a test View.', $textBuffer);

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

		$this->assertEquals('Authenticated Content', $textBuffer);
		$this->assertTrue($auth->isAuthed());
	}
}