<?php

namespace ZzbSdk\Model;

/**
 * 座位状态
 */
class ZzbSeat
{
    public ?string $seatCode;
    public ?string $seatStatus;

    public static function fromArray(array $data): self
    {
        $instance = new self();
        if (isset($data['seatCode'])) $instance->seatCode = $data['seatCode'];
        if (isset($data['seatStatus'])) $instance->seatStatus = $data['seatStatus'];
        return $instance;
    }
}
