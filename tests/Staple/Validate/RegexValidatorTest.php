<?php
/**
 * Created by PhpStorm.
 * User: conta
 * Date: 10/11/2018
 * Time: 4:30 PM
 */

namespace Staple\Tests;

use Staple\Validate\RegexValidator;
use PHPUnit\Framework\TestCase;

class RegexValidatorTest extends TestCase
{
    public function getValidatorObject($regex = RegexValidator::PASSWORD, $userMessage = null)
    {
        return new RegexValidator($regex, $userMessage);
    }

    public function test__construct()
    {
        $validator1 = new RegexValidator(RegexValidator::PASSWORD);
        $validator2 = new RegexValidator(RegexValidator::PASSWORD, 'Bad Password');

        $this->assertInstanceOf('\Staple\Validate\RegexValidator', $validator1);
        $this->assertInstanceOf('\Staple\Validate\RegexValidator', $validator2);
    }

    public function testSetRegex()
    {
        $validator = $this->getValidatorObject();

        $this->assertEquals(RegexValidator::PASSWORD, $validator->getRegex());

        $validator->setRegex('/^\d{3,4}$/');

        $this->assertEquals('/^\d{3,4}$/', $validator->getRegex());
        $this->assertTrue($validator->check(1234));
        $this->assertFalse($validator->check(12345));
    }

    public function testGetMatches()
    {
        $validator = $this->getValidatorObject();

        $validator->check('Test1234!');
        $this->assertGreaterThanOrEqual(1, count($validator->getMatches()));
    }

    public function testCheck()
    {
        $validator = $this->getValidatorObject();

        $this->assertTrue($validator->check('Test1234!'));
        $this->assertFalse($validator->check('test'));
    }
}
