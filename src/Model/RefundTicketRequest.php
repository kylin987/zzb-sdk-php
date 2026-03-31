<?php

namespace ZzbSdk\Model;

/**
 * 退票请求
 */
class RefundTicketRequest
{
    public string $appId;
    public string $version;
    public int $timestamp;
    public string $cinemaCode;
    public string $orderId;
    public int $count;
    public array $ticketList;
    public ?string $signature;

    public static function create(string $appId, string $cinemaCode, string $orderId, array $ticketList, string $version = '1.0'): self
    {
        $instance = new self();
        $instance->appId = $appId;
        $instance->version = $version;
        $instance->timestamp = time();
        $instance->cinemaCode = $cinemaCode;
        $instance->orderId = $orderId;
        $instance->count = count($ticketList);
        $instance->ticketList = $ticketList;
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
                'orderId' => $this->orderId,
                'count' => $this->count,
                'ticketList' => array_map(
                    fn(array $ticket) => ['ticketNo' => $ticket['ticketNo'] ?? ''],
                    $this->ticketList
                ),
            ],
            'signature' => $this->signature,
        ];
    }
}
