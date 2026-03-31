<?php

namespace ZzbSdk\Model;

/**
 * 订单电影票
 */
class ZzbOrderTicket
{
    public ?string $ticketNo;
    public ?string $ticketInfoCode;
    public ?string $seatCode;
    public ?string $rowId;
    public ?string $columnId;
    public ?float $ticketPrice;
    public ?float $screenServiceFee;
    public ?float $netServiceFee;
    public ?string $refundStatus;
    public ?string $refundTime;
    public ?string $redeemStatus;
    public ?string $redeemTime;
    public ?string $checkStatus;
    public ?string $checkTime;

    public static function fromArray(array $data): self
    {
        $instance = new self();
        if (isset($data['ticketNo'])) $instance->ticketNo = $data['ticketNo'];
        if (isset($data['ticketInfoCode'])) $instance->ticketInfoCode = $data['ticketInfoCode'];
        if (isset($data['seatCode'])) $instance->seatCode = $data['seatCode'];
        if (isset($data['rowId'])) $instance->rowId = (string) $data['rowId'];
        if (isset($data['columnId'])) $instance->columnId = (string) $data['columnId'];
        if (isset($data['ticketPrice'])) $instance->ticketPrice = (float) $data['ticketPrice'];
        if (isset($data['screenServiceFee'])) $instance->screenServiceFee = (float) $data['screenServiceFee'];
        if (isset($data['netServiceFee'])) $instance->netServiceFee = (float) $data['netServiceFee'];
        if (isset($data['refundStatus'])) $instance->refundStatus = $data['refundStatus'];
        if (isset($data['refundTime'])) $instance->refundTime = $data['refundTime'];
        if (isset($data['redeemStatus'])) $instance->redeemStatus = $data['redeemStatus'];
        if (isset($data['redeemTime'])) $instance->redeemTime = $data['redeemTime'];
        if (isset($data['checkStatus'])) $instance->checkStatus = $data['checkStatus'];
        if (isset($data['checkTime'])) $instance->checkTime = $data['checkTime'];
        return $instance;
    }
}
