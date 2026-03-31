<?php

namespace ZzbSdk\Model;

/**
 * 场次座位售出状态结果
 */
class ZzbSeatStatusResult
{
    public ?string $cinemaCode;
    public ?string $sessionCode;
    /**
     * @var ZzbSeat[]
     */
    public array $seatList = [];

    public static function fromArray(array $data): self
    {
        $instance = new self();
        if (isset($data['cinemaCode'])) $instance->cinemaCode = $data['cinemaCode'];
        if (isset($data['sessionCode'])) $instance->sessionCode = $data['sessionCode'];
        if (isset($data['seatList']) && is_array($data['seatList'])) {
            foreach ($data['seatList'] as $seatData) {
                $instance->seatList[] = ZzbSeat::fromArray($seatData);
            }
        }
        return $instance;
    }
}
