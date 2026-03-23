<?php

namespace ZzbSdk\Model;

/**
 * 影厅信息下载请求
 */
class GetScreenInfoRequest
{
    public string $appId;
    public string $version;
    public int $timestamp;
    public string $cinemaCode;
    public ?string $signature;

    public static function create(string $appId, string $cinemaCode, string $version = '1.0'): self
    {
        $instance = new self();
        $instance->appId = $appId;
        $instance->cinemaCode = $cinemaCode;
        $instance->version = $version;
        $instance->timestamp = time();
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
            ],
            'signature' => $this->signature,
        ];
    }
}
