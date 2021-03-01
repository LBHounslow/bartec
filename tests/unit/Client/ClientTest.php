<?php

namespace Tests\Unit\Client;

use Bartec\Client\Client as BartecClient;
use Bartec\Client\SoapClient;
use Bartec\Exception\SoapException;
use Bartec\Response\Response;
use Bartec\Response\SoapResponse;
use Tests\Unit\BartecTestCase;

class ClientTest extends BartecTestCase
{
    /**
     * @var BartecClient
     */
    private $bartecClient;

    /**
     * @var SoapClient|\PHPUnit\Framework\MockObject\MockObject
     */
    private $mockSoapClient;

    /**
     * @var SoapResponse
     */
    private $soapResponse;

    /**
     * @var \stdClass
     */
    private $mockSoapResult;

    public function setUp(): void
    {
        $this->mockSoapClient = $this->createMock(SoapClient::class);
        $this->mockSoapClient
            ->method('setOptions')
            ->willReturn($this->mockSoapClient);
        $this->bartecClient = new BartecClient($this->mockSoapClient, $this->mockSoapClient, 'u', 'p');
        $this->soapResponse = new SoapResponse();
        $this->mockSoapResult = new \stdClass();
        $this->mockSoapResult->Token = new \stdClass();
        parent::setUp();
    }

    public function testItHasSetTheConstructorArguments()
    {
        $this->assertInstanceOf(SoapClient::class, $this->bartecClient->getAuthSoapClient());
        $this->assertInstanceOf(SoapClient::class, $this->bartecClient->getCollectiveSoapClient());
    }

    public function testThatGetAuthTokenThrowsAnExceptionForResponseWithErrors()
    {
        $this->expectException(SoapException::class);
        $this->expectExceptionMessage('auth failed');
        $this->soapResponse->setException(new \Exception('auth failed'));
        $this->mockSoapClient
            ->method('callOperation')
            ->willReturn($this->soapResponse);
        $bartecClient = new BartecClient($this->mockSoapClient, $this->mockSoapClient, 'u', 'p');
        $bartecClient->getAuthToken();
    }

    public function testThatGetAuthTokenReturnsTokenForValidResponse()
    {
        $this->mockSoapResult->Token->TokenString = 'token';
        $this->mockSoapClient
            ->method('callOperation')
            ->willReturn($this->soapResponse->setResult($this->mockSoapResult));
        $bartecClient = new BartecClient($this->mockSoapClient, $this->mockSoapClient, 'u', 'p');
        $this->assertEquals('token', $bartecClient->getAuthToken());
    }

    public function testThatGetIFrameAuthTokenThrowsAnExceptionForResponseWithErrors()
    {
        $this->expectException(SoapException::class);
        $this->expectExceptionMessage('auth iframe failed');
        $this->soapResponse->setException(new \Exception('auth iframe failed'));
        $this->mockSoapClient
            ->method('callOperation')
            ->willReturn($this->soapResponse);
        $bartecClient = new BartecClient($this->mockSoapClient, $this->mockSoapClient, self::USERNAME, self::PASSWORD);
        $bartecClient->getIFrameAuthToken();
    }

    public function testThatGetIFrameAuthTokenReturnsTokenForValidResponse()
    {
        $this->mockSoapResult->Token->TokenString = 'token';
        $this->mockSoapClient
            ->method('callOperation')
            ->willReturn($this->soapResponse->setResult($this->mockSoapResult));
        $bartecClient = new BartecClient($this->mockSoapClient, $this->mockSoapClient, self::USERNAME, self::PASSWORD);
        $this->assertEquals('token', $bartecClient->getIFrameAuthToken());
    }

    public function testThatCallThrowsExceptionForResponseWithErrors()
    {
        $this->expectException(SoapException::class);
        $this->expectExceptionMessage(self::SOAP_FAULT_STRING);
        $this->soapResponse->setException(new \SoapFault(self::SOAP_FAULT_CODE, self::SOAP_FAULT_STRING));
        $this->mockSoapClient
            ->method('callOperation')
            ->willReturn($this->soapResponse->setResult($this->mockSoapResult));
        $bartecClient = new BartecClient($this->mockSoapClient, $this->mockSoapClient, self::USERNAME, self::PASSWORD);
        $bartecClient->call('FooBar');
    }

    public function testThatCallReturnsResponseForSuccessfulAuth()
    {
        $this->mockSoapResult->Token->TokenString = 'token';
        $this->mockSoapClient
            ->method('callOperation')
            ->willReturn($this->soapResponse->setResult($this->mockSoapResult));
        $bartecClient = new BartecClient($this->mockSoapClient, $this->mockSoapClient, self::USERNAME, self::PASSWORD);
        $response = $bartecClient->call('FooBar');
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals('token', $response->getToken());
    }
}
