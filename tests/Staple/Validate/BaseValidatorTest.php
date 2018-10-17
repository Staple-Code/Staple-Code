<?php
/**
 * Created by PhpStorm.
 * User: conta
 * Date: 10/11/2018
 * Time: 2:28 PM
 */

namespace Staple\Tests;

use Staple\Validate\BaseValidator;
use PHPUnit\Framework\TestCase;

class MyBaseValidator extends BaseValidator
{
    public function check($data): bool
    {
        return true;
    }
}

class BaseValidatorTest extends TestCase
{
    private function getBaseValidatorObject()
    {
        return new MyBaseValidator();
    }

    public function testClearErrors()
    {
        $validator = $this->getBaseValidatorObject();

        $validator->addError('Test');

        $this->assertEquals(['Test'], $validator->getErrors());

        $validator->clearErrors();

        $this->assertEquals([], $validator->getErrors());
    }

    public function testGetErrors()
    {
        $validator = $this->getBaseValidatorObject();

        $validator->addError('Test');

        $this->assertEquals(['Test'], $validator->getErrors());
    }

    public function testGetErrorsAsString()
    {
        $validator = $this->getBaseValidatorObject();

        $validator->addError('Test');

        $this->assertEquals('Test', $validator->getErrorsAsString());
    }

    public function testSetUserErrorMessage()
    {
        $validator = $this->getBaseValidatorObject();

        $validator->addError();

        $this->assertEquals([BaseValidator::DEFAULT_ERROR], $validator->getErrors());

        $validator->clearErrors();
        $validator->setUserErrorMessage('This is an error message');
        $validator->addError();

        $this->assertEquals(['This is an error message'], $validator->getErrors());
    }

    public function testGetName()
    {
        $validator = $this->getBaseValidatorObject();

        $this->assertEquals('Staple\Tests\MyBaseValidator', $validator->getName());
    }

    public function testAddError()
    {
        $validator = $this->getBaseValidatorObject();

        $validator->addError();

        $this->assertEquals([BaseValidator::DEFAULT_ERROR], $validator->getErrors());

        $validator->clearErrors();
        $validator->setUserErrorMessage('This is an error message');
        $validator->addError();

        $this->assertEquals(['This is an error message'], $validator->getErrors());

        $validator->setUserErrorMessage('This is an error message');
        $validator->addError('This is a custom error message');

        $this->assertEquals([
            'This is an error message',
            'This is a custom error message'
            ],
            $validator->getErrors());
    }
}
