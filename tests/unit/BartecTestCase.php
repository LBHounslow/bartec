<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

abstract class BartecTestCase extends TestCase
{
    const TEST_WSDL = 'https://wsdl.url';
    const SOAP_FAULT_CODE = 'SOAP-ENV:Client';
    const SOAP_FAULT_STRING = 'SOAP Fault';
    const EXCEPTION_MESSAGE = 'Exception';
    const EXCEPTION_CODE = 123;
    const USERNAME = 'u';
    const PASSWORD = 'p';

    /**
     * @return \stdClass
     */
    public function getMockErrorResult()
    {
        $mockBartecSoapErrorResult = new \stdClass();
        $mockBartecSoapErrorResult->Errors = new \stdClass();
        $mockBartecSoapErrorResult->Errors->Result = 1;
        $mockBartecSoapErrorResult->Errors->Message = 'error message';
        return $mockBartecSoapErrorResult;
    }
}