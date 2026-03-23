<?php

namespace ZzbSdk\Model;

/**
 * 数据上报请求
 */
class ReportTicketRequest
{
    public string $appId;
    public string $version;
    public int $timestamp;
    public string $sendChannelCode;
    /**
     * @var ZzbTicket[]
     */
    public array $ticketList;
    public ?string $signature;

    public static function create(string $appId, string $channelCode, string $version, array $tickets): self
    {
        $instance = new self();
        $instance->appId = $appId;
        $instance->version = $version;
        $instance->timestamp = time();
        $instance->sendChannelCode = $channelCode;
        $instance->ticketList = $tickets;
        $instance->signature = null;
        return $instance;
    }

    public function toArray(): array
    {
        $ticketArray = [];
        foreach ($this->ticketList as $ticket) {
            $ticketArray[] = $ticket->toArray();
        }
        return [
            'appId' => $this->appId,
            'version' => $this->version,
            'timestamp' => $this->timestamp,
            'data' => [
                'sendChannelCode' => $this->sendChannelCode,
                'ticketList' => $ticketArray,
            ],
            'signature' => $this->signature,
        ];
    }
}
