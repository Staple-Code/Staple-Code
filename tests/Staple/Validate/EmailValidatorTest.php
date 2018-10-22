<?php
/**
 * Created by PhpStorm.
 * User: conta
 * Date: 10/11/2018
 * Time: 3:08 PM
 */

namespace Staple\Tests;

use Staple\Validate\EmailValidator;
use PHPUnit\Framework\TestCase;

class EmailValidatorTest extends TestCase
{

    public function testCheck()
    {
        $validator = new EmailValidator();

        $this->assertTrue($validator->check('test@test.com'));
        $this->assertTrue($validator->check('test@test.co.uk'));
        $this->assertTrue($validator->check('some.data@test.com'));
        $this->assertTrue($validator->check('test@test.net'));
        $this->assertFalse($validator->check('test@testcom'));
        $this->assertFalse($validator->check('te@st@test.com'));
        $this->assertFalse($validator->check('testtest.com'));
    }
}
