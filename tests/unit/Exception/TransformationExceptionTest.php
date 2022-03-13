<?php

namespace Tests\Unit\Exception;

use LBHounslow\Bartec\Exception\TransformationException;
use Tests\Unit\BartecTestCase;

class TransformationExceptionTest extends BartecTestCase
{
    public function testItConstructs()
    {
        $result = new TransformationException();
        $this->assertInstanceOf(TransformationException::class, $result);
    }

    public function testItSetsTheCorrectMessageAndCode()
    {
        $exception = new TransformationException('error message', 123);
        $this->assertEquals(123, $exception->getCode());
        $this->assertEquals('error message', $exception->getMessage());
    }
}