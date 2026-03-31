<?php

namespace ZzbSdk\Model;

/**
 * 座位锁定请求
 */
class LockSeatsInfoRequest
{
    public string $appId;
    public string $version;
    public int $timestamp;
    public string $cinemaCode;
    public string $sessionCode;
    public string $appLockId;
    public int $count;
    public array $seatList;
    public ?string $signature;

    public static function create(
        string $appId,
        string $cinemaCode,
        string $sessionCode,
        string $appLockId,
        array $seatList,
        string $version = '1.0'
    ): self {
        $instance = new self();
        $instance->appId = $appId;
        $instance->version = $version;
        $instance->timestamp = time();
        $instance->cinemaCode = $cinemaCode;
        $instance->sessionCode = $sessionCode;
        $instance->appLockId = $appLockId;
        $instance->count = count($seatList);
        $instance->seatList = $seatList;
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
                'appLockId' => $this->appLockId,
                'count' => $this->count,
                'seatList' => array_map(
                    fn(array $seat) => ['seatCode' => $seat['seatCode'] ?? ''],
                    $this->seatList
                ),
            ],
            'signature' => $this->signature,
        ];
    }
}
