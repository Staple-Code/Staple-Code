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


class EncryptTest extends \PHPUnit_Framework_TestCase
{
	//@todo refactor this test
	public function testEncrypt()
	{
		$this->markTestIncomplete();

		$key = 'kASMCL^TRB8A<UQwOcgsHDKhgUs[ZtMe';
		$salt = 'askdfRIUF';
		$pepper = 'orpDjk34';
		$string = 'Blah encrypted string.';
		echo "<p>String to Encrypt: $string</p>";
		$encryped = Staple_Encrypt::AES_encrypt($string, $key, $salt, $pepper);
		echo "<p>Encrypted String: ".htmlentities($encryped)."</p>";
		$decrypted = Staple_Encrypt::AES_decrypt($encryped, $key, $salt, $pepper);
		echo "<p>Decrypted String: ".htmlentities($decrypted)."</p>";
	}
}
