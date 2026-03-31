<?php

namespace ZzbSdk\Model;

/**
 * 订单信息查询结果
 */
class ZzbQueryOrderResult
{
    public ?string $lockOrderId;
    public ?string $orderId;
    public ?string $orderStatus;
    public ?string $cinemaCode;
    public ?string $cinemaName;
    public ?string $screenCode;
    public ?string $screenName;
    public ?string $filmCode;
    public ?string $filmName;
    public ?string $sessionCode;
    public ?string $sessionDatetime;
    public ?int $duration;
    public ?string $redeemCode;
    public ?int $count;
    public ?string $orderTime;
    /**
     * @var ZzbOrderTicket[]
     */
    public array $ticketList = [];

    public static function fromArray(array $data): self
    {
        $instance = new self();
        if (isset($data['lockOrderId'])) $instance->lockOrderId = $data['lockOrderId'];
        if (isset($data['orderId'])) $instance->orderId = $data['orderId'];
        if (isset($data['orderStatus'])) $instance->orderStatus = $data['orderStatus'];
        if (isset($data['cinemaCode'])) $instance->cinemaCode = $data['cinemaCode'];
        if (isset($data['cinemaName'])) $instance->cinemaName = $data['cinemaName'];
        if (isset($data['screenCode'])) $instance->screenCode = $data['screenCode'];
        if (isset($data['screenName'])) $instance->screenName = $data['screenName'];
        if (isset($data['filmCode'])) $instance->filmCode = $data['filmCode'];
        if (isset($data['filmName'])) $instance->filmName = $data['filmName'];
        if (isset($data['sessionCode'])) $instance->sessionCode = $data['sessionCode'];
        if (isset($data['sessionDatetime'])) $instance->sessionDatetime = $data['sessionDatetime'];
        if (isset($data['duration'])) $instance->duration = (int) $data['duration'];
        if (isset($data['redeemCode'])) $instance->redeemCode = $data['redeemCode'];
        if (isset($data['count'])) $instance->count = (int) $data['count'];
        if (isset($data['orderTime'])) $instance->orderTime = $data['orderTime'];
        if (isset($data['ticketList']) && is_array($data['ticketList'])) {
            foreach ($data['ticketList'] as $ticketData) {
                $instance->ticketList[] = ZzbOrderTicket::fromArray($ticketData);
            }
        }

        return $instance;
    }
}
