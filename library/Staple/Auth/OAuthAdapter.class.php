<?php
/**
 * Created by PhpStorm.
 * User: ironpilot
 * Date: 10/21/2017
 * Time: 11:45 AM
 */

namespace Staple\Auth;


use Auth0\SDK\Exception\CoreException;
use Auth0\SDK\JWTVerifier;
use Staple\Config;
use Staple\Request;
use Staple\Traits\AuthRoute;

class OAuthAdapter implements AuthAdapter
{
	const AUTHORIZATION_HEADER = 'Authorization';
	use AuthRoute;

	private $userInfo;
	/**
	 * @param Request $request
	 * @return bool
	 */
	public function getAuth($request): bool
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
		catch(CoreException $e) {
			return false;
		}
	}

	/**
	 * @return mixed
	 */
	public function getLevel()
	{
		return 1;
	}

	/**
	 * @return object
	 */
	public function getUserId()
	{
		return $this->userInfo;
	}
}