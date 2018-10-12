<?php
/**
 * Created by PhpStorm.
 * User: conta
 * Date: 10/11/2018
 * Time: 4:30 PM
 */

namespace Staple\Tests;

use Staple\Validate\UploadedFileValidator;
use PHPUnit\Framework\TestCase;

class UploadedFileValidatorTest extends TestCase
{

    private function getValidationObject($mimeType = null, $userMessage = null)
    {
        return new UploadedFileValidator($mimeType, $userMessage);
    }

    public function test__construct()
    {
        $validator1 = $this->getValidationObject();
        $validator2 = $this->getValidationObject(['text/json']);
        $validator3 = $this->getValidationObject(['text/json'], 'Test Message');

        $this->assertInstanceOf('\Staple\Validate\UploadedFileValidator', $validator1);
        $this->assertInstanceOf('\Staple\Validate\UploadedFileValidator', $validator2);
        $this->assertInstanceOf('\Staple\Validate\UploadedFileValidator', $validator3);
    }

    public function testSetMimeCheck()
    {
        $validator = $this->getValidationObject();
        $validator->setMimeCheck(['text/javascript']);

        $this->assertEquals(['text/javascript'], $validator->getMimeCheck());
    }

    public function testCheck()
    {
        $this->markTestIncomplete();
    }
}
