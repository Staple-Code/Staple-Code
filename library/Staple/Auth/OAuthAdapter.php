<?php
/**
 * Created by PhpStorm.
 * User: ironpilot
 * Date: 10/21/2017
 * Time: 11:45 AM
 */

namespace Staple\Auth;


use Auth0\SDK\JWTVerifier;
use Exception;
use Staple\Config;
use Staple\Exception\ConfigurationException;
use Staple\Request;

class OAuthAdapter implements AuthAdapter
{
	const AUTHORIZATION_HEADER = 'Authorization';
	use AuthRoute;

	private mixed $userInfo;

	/**
	 * @param Request $request
	 * @return bool
	 * @throws ConfigurationException
	 */
	public function getAuth(mixed $request): bool
	{
		try {
			$verifier = new JWTVerifier([
				'supported_algs' => Config::getValue('oauth','supported_algs'),
				'valid_audiences' => Config::getValue('oauth','valid_audiences'),
				'authorized_iss' => Config::getValue('oauth','authorized_iss'),
			]);
			$authHeader = $request->findHeader(self::AUTHORIZATION_HEADER);
			$token = trim(str_ireplace('Bearer', '', $authHeader));

			$this->userInfo = $verifier->verifyAndDecode($token);
			return true;
		}
		catch(Exception $e) {
			return false;
		}
	}

	/**
	 * @return int
	 */
	public function getLevel(): int
	{
		return 1;
	}

	/**
	 * @return object
	 */
	public function getUserId(): mixed
	{
		return $this->userInfo;
	}

	public function clear(): bool
	{
		// TODO: Implement clear() method.
		return false;
	}
}