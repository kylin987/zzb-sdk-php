<?php

namespace ZzbSdk\Model;

/**
 * 确认订单交易请求
 */
class SubmitOrderRequest
{
    public string $appId;
    public string $version;
    public int $timestamp;
    public string $lockOrderId;
    public string $cinemaCode;
    public string $sessionCode;
    public int $count;
    public array $seatList;
    public ?string $signature;

    public static function create(
        string $appId,
        string $lockOrderId,
        string $cinemaCode,
        string $sessionCode,
        array $seatList,
        string $version = '1.0'
    ): self {
        $instance = new self();
        $instance->appId = $appId;
        $instance->version = $version;
        $instance->timestamp = time();
        $instance->lockOrderId = $lockOrderId;
        $instance->cinemaCode = $cinemaCode;
        $instance->sessionCode = $sessionCode;
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
                'lockOrderId' => $this->lockOrderId,
                'cinemaCode' => $this->cinemaCode,
                'sessionCode' => $this->sessionCode,
                'count' => $this->count,
                'seatList' => array_map(
                    fn(array $seat) => [
                        'seatCode' => $seat['seatCode'] ?? '',
                        'regionCode' => $seat['regionCode'] ?? '',
                        'sectionCode' => $seat['sectionCode'] ?? '',
                        'ticketPrice' => isset($seat['ticketPrice']) ? round((float) $seat['ticketPrice'], 2) : 0.0,
                        'screenServiceFee' => isset($seat['screenServiceFee']) ? round((float) $seat['screenServiceFee'], 2) : 0.0,
                        'netServiceFee' => isset($seat['netServiceFee']) ? round((float) $seat['netServiceFee'], 2) : 0.0,
                    ],
                    $this->seatList
                ),
            ],
            'signature' => $this->signature,
        ];
    }
}
