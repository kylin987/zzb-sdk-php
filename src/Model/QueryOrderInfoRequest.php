<?php

namespace ZzbSdk\Model;

/**
 * 订单信息查询请求
 */
class QueryOrderInfoRequest
{
    public string $appId;
    public string $version;
    public int $timestamp;
    public string $cinemaCode;
    public string $lockOrderId;
    public ?string $signature;

    public static function create(string $appId, string $cinemaCode, string $lockOrderId, string $version = '1.0'): self
    {
        $instance = new self();
        $instance->appId = $appId;
        $instance->version = $version;
        $instance->timestamp = time();
        $instance->cinemaCode = $cinemaCode;
        $instance->lockOrderId = $lockOrderId;
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
                'lockOrderId' => $this->lockOrderId,
            ],
            'signature' => $this->signature,
        ];
    }
}
