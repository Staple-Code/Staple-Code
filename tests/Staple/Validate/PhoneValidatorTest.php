<?php
/**
 * Created by PhpStorm.
 * User: conta
 * Date: 10/11/2018
 * Time: 4:29 PM
 */

namespace Staple\Tests;

use Staple\Validate\PhoneValidator;
use PHPUnit\Framework\TestCase;

class PhoneValidatorTest extends TestCase
{
    public function testCheck()
    {
        $validator = new PhoneValidator();

        $this->assertTrue($validator->check("123-456-7890"));
        $this->assertTrue($validator->check("(800) 555-9999"));
        $this->assertTrue($validator->check("1 (800) 555-9999"));
        $this->assertTrue($validator->check("23-800-555-9999"));
        $this->assertTrue($validator->check("800.555.9999"));
        $this->assertFalse($validator->check("[800]555.9999"));
    }
}
