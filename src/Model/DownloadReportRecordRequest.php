<?php

namespace ZzbSdk\Model;

/**
 * 数据比对文件下载请求
 */
class DownloadReportRecordRequest
{
    public string $appId;
    public string $version;
    public int $timestamp;
    public string $sendChannelCode;
    public string $startShowDate;
    public string $endShowDate;
    public ?string $signature;

    public static function create(string $appId, string $channelCode, string $startDate, string $endDate, string $version = '1.0'): self
    {
        $instance = new self();
        $instance->appId = $appId;
        $instance->version = $version;
        $instance->timestamp = time();
        $instance->sendChannelCode = $channelCode;
        $instance->startShowDate = $startDate;
        $instance->endShowDate = $endDate;
        $instance->signature = null;
        return $instance;
    }

    public function toArray(): array
    {
        return [
            'appId' => $this->appId,
            'version' => $this->version,
            'timestamp' => $this->timestamp,
            'sendChannelCode' => $this->sendChannelCode,
            'startShowDate' => $this->startShowDate,
            'endShowDate' => $this->endShowDate,
            'signature' => $this->signature,
        ];
    }
}
