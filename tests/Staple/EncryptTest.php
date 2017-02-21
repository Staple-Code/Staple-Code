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


use PHPUnit\Framework\TestCase;
use Staple\Encrypt;

class EncryptTest extends TestCase
{
	private $key = 'kASMCL^TRB8A<UQwOcgsHDKhgUs[ZtMe';
	private $salt = 'askdfRIUF';
	private $pepper = 'orpDjk34';

	public function testAESEncrypt()
	{
		$string = 'Blah encrypted string.';

		$encrypted = Encrypt::encrypt($string, $this->key, MCRYPT_RIJNDAEL_128, $this->salt, $this->pepper);

		$this->assertEquals('5be2da124b05f90210a061b7553b72c7be235ec7c6aace4c739aa0f8cb602b327d8c0104c0017b37450b8032a47da639',bin2hex($encrypted));
	}

	public function testAESDecrypt()
	{
		$string = '5be2da124b05f90210a061b7553b72c7be235ec7c6aace4c739aa0f8cb602b327d8c0104c0017b37450b8032a47da639';

		$decrypted = Encrypt::decrypt(hex2bin($string), $this->key, MCRYPT_RIJNDAEL_128, $this->salt, $this->pepper);

		$this->assertEquals('Blah encrypted string.',$decrypted);
	}
}
