<?php
/**
 * Created by PhpStorm.
 * User: scott.henscheid
 * Date: 10/9/2017
 * Time: 10:06 AM
 */

namespace Staple\Tests;

use PHPUnit\Framework\TestCase;
use Staple\Auth\Auth;
use Staple\Auth\AuthAdapter;

class FakeAuthAdapter implements AuthAdapter
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

class AuthTest extends TestCase
{


	public function getAuth()
	{
		return new Auth();
	}

	public function testLoginWithArrayOfCredentials()
	{
		$auth = $this->getAuth();
		$auth->implementAuthAdapter(new FakeAuthAdapter());
		$credentials = [
			'username'	=>	'testusername',
			'password'	=>	'test&P@ssword'
		];
		$authed = $auth->doAuth($credentials);

		$this->assertTrue($authed);
		$this->assertEquals($credentials, $auth->getAuthId());
		$this->assertEquals(1, $auth->getAuthLevel());
		$this->assertTrue($auth->isAuthed());
	}

	public function testFailedLoginWithArrayOfCredentials()
	{
		$auth = $this->getAuth();
		$auth->implementAuthAdapter(new FakeAuthAdapter());
		$credentials = [
			'username'	=>	'testusername',
			'password'	=>	'noPassword'
		];
		$authed = $auth->doAuth($credentials);

		$this->assertFalse($authed);
		$this->assertEquals(null, $auth->getAuthId());
		$this->assertEquals(0, $auth->getAuthLevel());
		$this->assertFalse($auth->isAuthed());
	}

	public function testLoginAndLogOut()
	{
		$auth = $this->getAuth();
		$auth->implementAuthAdapter(new FakeAuthAdapter());
		$credentials = [
			'username'	=>	'testusername',
			'password'	=>	'test&P@ssword'
		];
		$authed = $auth->doAuth($credentials);

		$this->assertTrue($authed);
		$this->assertEquals($credentials, $auth->getAuthId());
		$this->assertEquals(1, $auth->getAuthLevel());
		$this->assertEquals('Authentication Successful', $auth->getMessage());
		$this->assertTrue($auth->isAuthed());

		//Clear the auth
		$auth->clearAuth();

		$this->assertFalse($auth->isAuthed());
		$this->assertEquals('Logged Out', $auth->getMessage());
		$this->assertEquals(null, $auth->getAuthId());
		$this->assertEquals(0, $auth->getAuthLevel());
	}
}
