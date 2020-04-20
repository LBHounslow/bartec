<?php

namespace Tests\Unit\Exception;

use Bartec\Exception\SoapException;
use Bartec\Response\Response;
use Tests\Unit\BartecTestCase;

class SoapExceptionTest extends BartecTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @param Response $response
     * @param string $expectedMessage
     * @param int $expectedCode
     * @dataProvider exceptionDataProvider
     */
    public function testItSetsTheCorrectMessageAndCode(Response $response, string $expectedMessage, int $expectedCode)
    {
        $exception = new SoapException($response);
        $this->assertEquals($expectedCode, $exception->getCode());
        $this->assertEquals($expectedMessage, $exception->getMessage());
    }

    public function exceptionDataProvider()
    {
        return [
            [(new Response())->setResult($this->getMockErrorResult()), 'error message', 0],
            [(new Response())
                ->setException(new \Exception(self::EXCEPTION_MESSAGE, self::EXCEPTION_CODE)), self::EXCEPTION_MESSAGE, self::EXCEPTION_CODE],
            [(new Response())
                ->setFault(new \SoapFault(self::SOAP_FAULT_CODE, self::SOAP_FAULT_STRING)), self::SOAP_FAULT_STRING, 0],
        ];
    }
}