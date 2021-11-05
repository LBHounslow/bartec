<?php

namespace Tests\Unit\Response;

use LBHounslow\Bartec\Response\SoapResponse;
use Tests\Unit\BartecTestCase;

class SoapResponseTest extends BartecTestCase
{
    /**
     * @var SoapResponse
     */
    private $soapResponse;

    public function setUp(): void
    {
        $this->soapResponse = new SoapResponse();
        parent::setUp();
    }

    public function testItSetsResult()
    {
        $this->soapResponse->setResult('result');
        $this->assertEquals('result', $this->soapResponse->getResult());
    }

    public function testItHasFailedWithNoErrors()
    {
        $this->assertEquals(false, $this->hasFailed());
    }

    public function testItHasFailedWithExceptionSet()
    {
        $this->soapResponse->setException(new \Exception(self::EXCEPTION_MESSAGE));
        $this->assertEquals(true, $this->soapResponse->hasErrors());
    }

    public function testItHasFailedWithSoapFaultSet()
    {
        $this->soapResponse->setFault(new \SoapFault(self::SOAP_FAULT_CODE, self::SOAP_FAULT_STRING));
        $this->assertEquals(true, $this->soapResponse->hasErrors());
    }
}