<?php

namespace ZzbSdk\Model;

/**
 * 影厅座位信息查询请求
 */
class QueryScreenSeatInfoRequest
{
    public string $appId;
    public string $version;
    public int $timestamp;
    public string $cinemaCode;
    public string $screenCode;
    public ?string $signature;

    public static function create(string $appId, string $cinemaCode, string $screenCode, string $version = '1.0'): self
    {
        $instance = new self();
        $instance->appId = $appId;
        $instance->version = $version;
        $instance->timestamp = time();
        $instance->cinemaCode = $cinemaCode;
        $instance->screenCode = $screenCode;
        $instance->signature = null;
        return $instance;
    }

    public function toArray(): array
    {
        return [
            'appId' => $this->appId,
            'version' => $this->version,
            'timestamp' => $this->timestamp,
            'data' => [
                'cinemaCode' => $this->cinemaCode,
                'screenCode' => $this->screenCode,
            ],
            'signature' => $this->signature,
        ];
    }
}
