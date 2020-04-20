<?php

namespace Tests\Unit\Client;

use Bartec\Client\SoapClient;
use Tests\Unit\BartecTestCase;

class SoapClientTest extends BartecTestCase
{
    /**
     * @var SoapClient
     */
    private $client;

    public function setUp(): void
    {
        $this->client = $this->createPartialMock(SoapClient::class, ['getInstance']);
        $this->client->setWsdl('')->setOptions([]);
        $this->client->method('getInstance')
            ->willReturn(\SoapClient::class);
        parent::setUp();
    }

    public function testItSetsAndGetsWsdl()
    {
        $this->client->setWsdl(self::TEST_WSDL);
        $this->assertEquals(self::TEST_WSDL, $this->client->getWsdl());
    }

    public function testItSetsAndGetsOptions()
    {
        $this->client->setOptions(['location' => self::TEST_WSDL]);
        $this->assertEquals(self::TEST_WSDL, $this->client->getOptions()['location']);
    }

    public function testHasOptionReturnsCorrectValue()
    {
        $this->assertFalse($this->client->hasOption('username'));
        $this->client->setOptions(['username' => 'foo']);
        $this->assertTrue($this->client->hasOption('username'));
    }

    public function testItThrowsExceptionWithNoWsdl()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('WSDL url is required');
        $this->client->connect();
    }

    public function testItSetsTraceOptionWhenDebugIsCalled()
    {
        $this->assertArrayNotHasKey('trace', $this->client->getOptions()); // not set initially
        $this->client->debug(); // set debug mode, trace = 1 must be set now
        $this->assertArrayHasKey('trace', $this->client->getOptions());
        $this->assertEquals(1, $this->client->getOptions()['trace']);
    }

    public function testItThrowsExceptionWithInvalidOption()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Option foo is not a valid SOAP option');
        $this->client->setWsdl(self::TEST_WSDL)
            ->setOptions(['foo' => 'bar']);
        $this->client->connect();
    }

    public function testConnectReturnsClientInstance()
    {
        $this->client->setWsdl(self::TEST_WSDL);
        $this->assertEquals(\SoapClient::class, $this->client->connect());
    }

    public function testItReturnsConnectedInstanceForFutureCalls()
    {
        $this->client->setWsdl(self::TEST_WSDL)->connect(); // 1st connected instance
        $this->client->setWsdl('')->setOptions(['foo' => 'bar']);
        $this->assertEquals(\SoapClient::class, $this->client->connect()); // Connect again
    }
}