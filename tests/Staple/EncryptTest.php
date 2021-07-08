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

	public function testEncryptAndDecrypt()
	{
		$originalString = 'Blah encrypted string.';

		$iv = openssl_random_pseudo_bytes(16);

		$encrypted = Encrypt::encrypt($originalString, $this->key, Encrypt::AES256, $this->salt, $this->pepper, $iv);

		$decryptedString = Encrypt::decrypt($encrypted,$this->key, Encrypt::AES256, $this->salt, $this->pepper, $iv);

		$this->assertEquals($originalString,$decryptedString);
	}
}
