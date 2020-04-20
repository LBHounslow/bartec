<?php

namespace Tests\Unit\Soap\Response;

use Bartec\Response\Response;
use Bartec\Response\SoapResponse;
use Tests\Unit\BartecTestCase;

class ResponseTest extends BartecTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testItHydratesCorrectly()
    {
        $soapResponse = (new SoapResponse())
            ->setFault(new \SoapFault(self::SOAP_FAULT_CODE, self::SOAP_FAULT_STRING))
            ->setException(new \Exception(self::EXCEPTION_MESSAGE, self::EXCEPTION_CODE))
            ->setResult(new \stdClass());
        $result = (new Response())->hydrate($soapResponse);
        $this->assertInstanceOf(\SoapFault::class, $result->getFault());
        $this->assertInstanceOf(\Exception::class, $result->getException());
        $this->assertInstanceOf(\stdClass::class, $result->getResult());
    }

    /**
     * @param \stdClass $result
     * @param int $expected
     * @dataProvider getErrorResultDataProvider
     */
    public function testGetErrorResult(\stdClass $result, int $expected)
    {
        $bartecResponse = (new Response())->setResult($result);
        $this->assertEquals($expected, $bartecResponse->getErrorResult());
    }

    public function getErrorResultDataProvider()
    {
        return [
            [new \stdClass(),               0],
            [$this->getMockErrorResult(),   1]
        ];
    }
}