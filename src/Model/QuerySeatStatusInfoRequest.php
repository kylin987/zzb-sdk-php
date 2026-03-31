<?php

namespace ZzbSdk\Model;

/**
 * 场次座位售出状态查询请求
 */
class QuerySeatStatusInfoRequest
{
    public string $appId;
    public string $version;
    public int $timestamp;
    public string $cinemaCode;
    public string $sessionCode;
    public array $seatStatus;
    public ?string $signature;

    public static function create(
        string $appId,
        string $cinemaCode,
        string $sessionCode,
        array $seatStatus = ['LOCKED', 'SOLD', 'UNAVAILABLE'],
        string $version = '1.0'
    ): self {
        $instance = new self();
        $instance->appId = $appId;
        $instance->version = $version;
        $instance->timestamp = time();
        $instance->cinemaCode = $cinemaCode;
        $instance->sessionCode = $sessionCode;
        $instance->seatStatus = $seatStatus;
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
                'sessionCode' => $this->sessionCode,
                'seatStatus' => $this->seatStatus,
            ],
            'signature' => $this->signature,
        ];
    }
}
