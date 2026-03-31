<?php

namespace ZzbSdk\Model;

/**
 * 影厅座位信息
 */
class ZzbScreenSeatInfo
{
    public ?string $cinemaCode;
    public ?string $screenCode;
    /**
     * @var ZzbSeatsLayout[]
     */
    public array $seatsLayout = [];

    public static function fromArray(array $data): self
    {
        $instance = new self();
        if (isset($data['cinemaCode'])) $instance->cinemaCode = $data['cinemaCode'];
        if (isset($data['screenCode'])) $instance->screenCode = $data['screenCode'];
        if (isset($data['seatsLayout']) && is_array($data['seatsLayout'])) {
            foreach ($data['seatsLayout'] as $layoutData) {
                $instance->seatsLayout[] = ZzbSeatsLayout::fromArray($layoutData);
            }
        }
        return $instance;
    }
}
