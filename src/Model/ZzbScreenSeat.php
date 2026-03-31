<?php

namespace ZzbSdk\Model;

/**
 * 影厅座位
 */
class ZzbScreenSeat
{
    public ?string $seatCode;
    public ?string $x;
    public ?string $y;
    public ?string $rowId;
    public ?string $columnId;
    public ?string $type;
    public ?string $status;
    public ?string $regionCode;
    public ?string $sectionCode;

    public static function fromArray(array $data): self
    {
        $instance = new self();
        foreach ($data as $key => $value) {
            if (property_exists($instance, $key)) {
                $instance->$key = $value;
            }
        }
        return $instance;
    }
}
