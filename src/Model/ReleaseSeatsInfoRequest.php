<?php

namespace ZzbSdk\Model;

/**
 * 座位解锁请求
 */
class ReleaseSeatsInfoRequest
{
    public string $appId;
    public string $version;
    public int $timestamp;
    public string $cinemaCode;
    public string $lockOrderId;
    public int $count;
    public ?string $signature;

    public static function create(
        string $appId,
        string $cinemaCode,
        string $lockOrderId,
        int $count,
        string $version = '1.0'
    ): self {
        $instance = new self();
        $instance->appId = $appId;
        $instance->version = $version;
        $instance->timestamp = time();
        $instance->cinemaCode = $cinemaCode;
        $instance->lockOrderId = $lockOrderId;
        $instance->count = $count;
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
                'count' => $this->count,
            ],
            'signature' => $this->signature,
        ];
    }
}
