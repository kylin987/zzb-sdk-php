<?php

namespace ZzbSdkTests\Service;

use PHPUnit\Framework\TestCase;
use ZzbSdk\Config;
use ZzbSdk\Model\ZzbTicket;
use ZzbSdk\Signer\VstkSignerInterface;
use ZzbSdk\ZzbService;

class Netsale2025ServiceTest extends TestCase
{
    public function testReportTicketUsesNetsale2025RootPayload(): void
    {
        $service = new CapturingZzbService(new Config([
            'mode' => Config::MODE_NETSALE_2025,
            'reportUrl' => 'https://panda.zgdypw.cn:8087/report',
            'serviceUrl' => 'https://panda.zgdypw.cn:8085/service',
            'channelCode' => '98265004',
            'certId' => '98265004',
        ]));

        $ticket = new ZzbTicket();
        $ticket->numberByDay = 1;
        $ticket->parentChannelCode = '00000000';
        $ticket->childChannelCode = '00000000';
        $ticket->ticketNo = '130904010Ba0102';
        $ticket->cinemaCode = '13090401';
        $ticket->saleChannelCode = '98265004';
        $ticket->operation = 1;

        $result = $service->reportTicket([$ticket]);

        $this->assertTrue($result->isSuccess());
        $this->assertSame('https://panda.zgdypw.cn:8087/report/reportTicket', $service->lastPost['url']);
        $this->assertSame([], $service->lastPost['headers']);
        $this->assertSame('98265004', $service->lastPost['data']['sendChannelCode']);
        $this->assertArrayHasKey('ticketList', $service->lastPost['data']);
        $this->assertArrayNotHasKey('data', $service->lastPost['data']);
        $this->assertSame('130904010Ba0102', $service->lastPost['data']['ticketList'][0]['ticketNo']);
    }

    public function testDownloadReportRecordUsesVstkP7AttachSignature(): void
    {
        $signer = new CapturingSigner('U0lHTkVE');
        $service = new CapturingZzbService(new Config([
            'mode' => Config::MODE_NETSALE_2025,
            'reportUrl' => 'https://panda.zgdypw.cn:8087/report',
            'serviceUrl' => 'https://panda.zgdypw.cn:8085/service',
            'channelCode' => '98265004',
            'certId' => '98265004',
            'vstkSigner' => $signer,
        ]));
        $service->rawResponse = "zip-bytes";

        $content = $service->downloadReportRecord('2026-06-01', '2026-06-02');

        $this->assertSame('zip-bytes', $content);
        $this->assertSame('98265004', $signer->certId);

        $plain = json_decode($signer->plainText, true);
        $this->assertSame([
            'startShowDate' => '2026-06-01',
            'endShowDate' => '2026-06-02',
        ], $plain['data']);
        $this->assertSame('98265004', $plain['sendChannelCode']);
        $this->assertIsInt($plain['timestamp']);
        $this->assertGreaterThan(1000000000000, $plain['timestamp']);

        $this->assertSame('https://panda.zgdypw.cn:8085/service/data/downloadReportRecord', $service->lastPost['url']);
        $this->assertFalse($service->lastPost['decodeJson']);
        $this->assertSame([
            'sendChannelCode' => '98265004',
            'startShowDate' => '2026-06-01',
            'endShowDate' => '2026-06-02',
            'signature' => 'U0lHTkVE',
        ], $service->lastPost['data']);
    }
}

class CapturingZzbService extends ZzbService
{
    public array $lastPost = [];
    public string $rawResponse = '';

    protected function post(string $url, array $data, bool $decodeJson = true, array $headers = [])
    {
        $this->lastPost = [
            'url' => $url,
            'data' => $data,
            'decodeJson' => $decodeJson,
            'headers' => $headers,
        ];

        if (!$decodeJson) {
            return $this->rawResponse;
        }

        return [
            'code' => '200',
            'status' => 'success',
            'data' => [],
            'traceId' => 'test-trace',
        ];
    }
}

class CapturingSigner implements VstkSignerInterface
{
    public ?string $certId = null;
    public string $plainText = '';

    public function __construct(private string $signature)
    {
    }

    public function p7AttachSign(string $certId, string $plainText): string
    {
        $this->certId = $certId;
        $this->plainText = $plainText;

        return $this->signature;
    }
}
