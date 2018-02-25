<?php

namespace App\Test;

use App\Utility\ValidationContext;
use PHPUnit\Framework\TestCase;

/**
 * ValidationContext tests
 *
 * @coversDefaultClass \App\Utility\ValidationContext
 */
class ValidationContextTest extends TestCase
{
    /**
     * Test instance.
     */
    public function testInstance()
    {
        $actual = new ValidationContext();
        $this->assertInstanceOf(ValidationContext::class, $actual);
    }

    /**
     * Tests getMessage and setMessage functions.
     *
     * @covers ::setMessage
     * @covers ::getMessage
     * @return void
     */
    public function testValidationContext()
    {
        $val = new ValidationContext();
        $val->setMessage("test");
        $resultText = $val->getMessage();
        $this->assertSame("test", $resultText);
    }

    /**
     * Tests addError and success functions.
     * Tests addError function with two strings.
     *
     * @covers ::addError
     * @covers ::failed
     * @return void
     */
    public function testValidationContextErrors()
    {
        $val = new ValidationContext();
        $val->addError("error1", "failed");
        $result = $val->failed();
        $this->assertTrue($result);
    }

    /**
     * Tests addError and success functions.
     * Tests addError function with an empty string for the first parameter.
     *
     * @covers ::addError
     * @covers ::failed
     * @return void
     */
    public function testValidationContextErrorsEmptyFieldOne()
    {
        $val = new ValidationContext();
        $val->addError("", "failed");
        $result = $val->failed();
        $this->assertTrue($result);
    }

    /**
     * Tests addError and success functions.
     * Tests addError function with an empty string for the second parameter.
     *
     * @covers ::addError
     * @covers ::failed
     * @return void
     */
    public function testValidationContextErrorsEmptyFieldTwo()
    {
        $val = new ValidationContext();
        $val->addError("error1", "");
        $result = $val->failed();
        $this->assertTrue($result);
    }

    /**
     * Tests addError and success functions.
     * Tests addError function with two empty strings.
     *
     * @covers ::addError
     * @covers ::failed
     * @return void
     */
    public function testValidationContextErrorsEmptyBoth()
    {
        $val = new ValidationContext();
        $val->addError("", "");
        $result = $val->failed();
        $this->assertTrue($result);
    }

    /**
     * Tests addError and success functions.
     * Tests addError function with null for the first parameter.
     *
     * @covers ::addError
     * @covers ::failed
     * @return void
     */
    public function testValidationContextErrorsNullOne()
    {
        $val = new ValidationContext();
        $val->addError(null, "failed");
        $result = $val->failed();
        $this->assertTrue($result);
    }

    /**
     * Tests addError and success functions.
     * Tests addError function with null for the second parameter.
     *
     * @covers ::addError
     * @covers ::failed
     * @return void
     */
    public function testValidationContextErrorsNullTwo()
    {
        $val = new ValidationContext();
        $val->addError("failed", null);
        $result = $val->failed();
        $this->assertTrue($result);
    }

    /**
     * Tests success function.
     * Tests for no errors.
     *
     * @covers ::failed
     * @return void
     */
    public function testValidationContextNoErrors()
    {
        $val = new ValidationContext();
        $result = $val->failed();
        $this->assertFalse($result);
    }

    /**
     * Tests __construct function.
     *
     * @covers ::__construct
     * @covers ::getMessage
     * @return void
     */
    public function testValidationContextConstructor()
    {
        $val = new ValidationContext("Error");
        $result = $val->getMessage();
        $this->assertSame($result, "Error");
    }

    /**
     * Tests clear function.
     *
     * @covers ::addError
     * @covers ::failed
     * @covers ::clear
     * @covers ::__construct
     * @return void
     */
    public function testValidationContextClear()
    {
        $val = new ValidationContext("Error");
        $val->addError("error", "error");
        $val->clear();
        $result = $val->failed();
        $this->assertFalse($result);
    }

    /**
     * Tests getErrors function.
     *
     * @covers ::getErrors
     * @return void
     */
    public function testValidationContextGetErrors()
    {
        $val = new ValidationContext();
        $errorFieldName = "ERROR";
        $errorMessage = "This is an error!";
        $val->addError($errorFieldName, $errorMessage);
        $result = $val->getErrors();
        $this->assertSame($result[0]['field'], $errorFieldName);
        $this->assertSame($result[0]['message'], $errorMessage);
    }

    /**
     * Tests toArray function.
     *
     * @covers ::toArray
     * @covers ::addError
     * @covers ::__construct
     * @return void
     */
    public function testValidationContextToArray()
    {
        $val = new ValidationContext("Errors");
        $val->addError("error1", "error");
        $val->addError("error2", "error");
        $result = $val->toArray();
        $this->assertSame($result['message'], "Errors");
        $this->assertSame($result['errors'][0]['field'], "error1");
        $this->assertSame($result['errors'][0]['message'], "error");
        $this->assertSame($result['errors'][1]['field'], "error2");
        $this->assertSame($result['errors'][1]['message'], "error");
    }
}
