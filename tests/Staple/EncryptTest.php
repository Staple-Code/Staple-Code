<?php
/**
 * Unit Tests for \Staple\Encrypt object
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


use Staple\Encrypt;

class EncryptTest extends \PHPUnit_Framework_TestCase
{
	private $key = 'kASMCL^TRB8A<UQwOcgsHDKhgUs[ZtMe';
	private $salt = 'askdfRIUF';
	private $pepper = 'orpDjk34';

	public function testAESEncrypt()
	{
		$string = 'Blah encrypted string.';

		$encrypted = Encrypt::AES_encrypt($string, $this->key, $this->salt, $this->pepper);

		$this->assertEquals('132bff3f0fc8e47035b89f246ef030ec541d3774910f183242b7fa475523b5d05ae66c78a2c26fd5bae9e7682d99b513e030f0cf4f7338fe6855d704b9c515a0',bin2hex($encrypted));
	}

	public function testAESDecrypt()
	{
		$string = '132bff3f0fc8e47035b89f246ef030ec541d3774910f183242b7fa475523b5d05ae66c78a2c26fd5bae9e7682d99b513e030f0cf4f7338fe6855d704b9c515a0';

		$decrypted = Encrypt::AES_decrypt(hex2bin($string), $this->key, $this->salt, $this->pepper);

		$this->assertEquals('Blah encrypted string.',$decrypted);
	}
}
