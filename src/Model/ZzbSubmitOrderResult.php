<?php

namespace ZzbSdk\Model;

/**
 * 确认订单交易结果
 */
class ZzbSubmitOrderResult
{
    public ?string $lockOrderId;
    public ?string $orderResult;
    public ?string $resultCode;
    public ?string $resultMsg;
    public ?string $cinemaCode;
    public ?string $orderId;
    public ?string $redeemCode;
    public ?int $count;
    /**
     * @var ZzbOrderTicket[]
     */
    public array $ticketList = [];

    public static function fromArray(array $data): self
    {
        $instance = new self();
        if (isset($data['lockOrderId'])) $instance->lockOrderId = $data['lockOrderId'];
        if (isset($data['orderResult'])) $instance->orderResult = $data['orderResult'];
        if (isset($data['resultCode'])) $instance->resultCode = $data['resultCode'];
        if (isset($data['resultMsg'])) $instance->resultMsg = $data['resultMsg'];
        if (isset($data['cinemaCode'])) $instance->cinemaCode = $data['cinemaCode'];
        if (isset($data['orderId'])) $instance->orderId = $data['orderId'];
        if (isset($data['redeemCode'])) $instance->redeemCode = $data['redeemCode'];
        if (isset($data['count'])) $instance->count = (int) $data['count'];
        if (isset($data['ticketList']) && is_array($data['ticketList'])) {
            foreach ($data['ticketList'] as $ticketData) {
                $instance->ticketList[] = ZzbOrderTicket::fromArray($ticketData);
            }
        }
        return $instance;
    }
}
