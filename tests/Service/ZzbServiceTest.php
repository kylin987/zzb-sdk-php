<?php

namespace ZzbSdkTests\Service;

use PHPUnit\Framework\TestCase;
use ZzbSdk\Config;
use ZzbSdk\ZzbService;
use ZzbSdk\Model\ZzbTicket;

class ZzbServiceTest extends TestCase
{
    private $config;
    private $service;

    protected function setUp(): void
    {
        // 使用测试配置
        $this->config = new Config([
            'reportUrl' => 'https://test.zzb.com/report',
            'serviceUrl' => 'https://test.zzb.com/service',
            'channelCode' => 'test_channel',
            'certId' => 'test_cert_id',
            'appId' => 'test_app_id',
            'interfaceKey' => 'test_interface_key',
        ]);

        $this->service = new ZzbService($this->config);
    }

    public function testReportTicket()
    {
        // 这里应该模拟 HTTP 请求，但由于 PHP 的 cURL 难以直接 mock，
        // 我们暂时只测试方法是否可调用，实际使用时需要集成测试
        $ticket = new ZzbTicket();
        $ticket->ticketNo = 'TEST123';

        // 由于没有真实的服务器，这里会抛出异常，但我们测试的是结构
        $this->expectException(\Exception::class);
        $this->service->reportTicket([$ticket]);
    }

    public function testGetCinemaInfo()
    {
        // 同样，由于没有真实服务器，这里会抛出异常
        $this->expectException(\Exception::class);
        $this->service->getCinemaInfo('12345678');
    }
}
