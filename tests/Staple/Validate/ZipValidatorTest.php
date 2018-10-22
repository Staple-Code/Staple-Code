<?php
/**
 * Created by PhpStorm.
 * User: conta
 * Date: 10/11/2018
 * Time: 4:31 PM
 */

namespace Staple\Tests;

use Staple\Validate\ZipValidator;
use PHPUnit\Framework\TestCase;

class ZipValidatorTest extends TestCase
{

    public function testCheck()
    {
        $validator = new ZipValidator();

        $this->assertTrue($validator->check('03834'));
        $this->assertTrue($validator->check('93844-2314'));
        $this->assertFalse($validator->check('93844-23145'));
    }
}
