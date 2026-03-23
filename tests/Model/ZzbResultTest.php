<?php

namespace ZzbSdkTests\Model;

use PHPUnit\Framework\TestCase;
use ZzbSdk\Model\ZzbResult;

class ZzbResultTest extends TestCase
{
    public function testIsSuccess()
    {
        $result = new ZzbResult();
        $result->code = '200';
        $this->assertTrue($result->isSuccess());

        $result->code = '500';
        $this->assertFalse($result->isSuccess());
    }

    public function testFromArray()
    {
        $data = [
            'code' => '200',
            'status' => 'success',
            'data' => ['key' => 'value'],
            'traceId' => '123456',
        ];

        $result = ZzbResult::fromArray($data);

        $this->assertEquals('200', $result->code);
        $this->assertEquals('success', $result->status);
        $this->assertEquals(['key' => 'value'], $result->data);
        $this->assertEquals('123456', $result->traceId);
    }
}
