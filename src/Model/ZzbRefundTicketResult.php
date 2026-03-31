<?php

namespace ZzbSdk\Model;

/**
 * 退票结果
 */
class ZzbRefundTicketResult
{
    public ?string $cinemaCode;
    public ?string $orderId;
    public ?string $refundResult;
    public ?string $resultCode;
    public ?string $resultMsg;
    public ?string $refundTime;
    public ?int $count;
    /**
     * @var ZzbOrderTicket[]
     */
    public array $ticketList = [];

    public static function fromArray(array $data): self
    {
        $instance = new self();
        if (isset($data['cinemaCode'])) $instance->cinemaCode = $data['cinemaCode'];
        if (isset($data['orderId'])) $instance->orderId = $data['orderId'];
        if (isset($data['refundResult'])) $instance->refundResult = $data['refundResult'];
        if (isset($data['resultCode'])) $instance->resultCode = $data['resultCode'];
        if (isset($data['resultMsg'])) $instance->resultMsg = $data['resultMsg'];
        if (isset($data['refundTime'])) $instance->refundTime = $data['refundTime'];
        if (isset($data['count'])) $instance->count = (int) $data['count'];
        if (isset($data['ticketList']) && is_array($data['ticketList'])) {
            foreach ($data['ticketList'] as $ticketData) {
                $instance->ticketList[] = ZzbOrderTicket::fromArray($ticketData);
            }
        }
        return $instance;
    }
}
