<?php

namespace ZzbSdk\Model;

/**
 * 座位图场区
 */
class ZzbSeatRegion
{
    public ?string $regionCode;
    public ?string $regionName;

    public static function fromArray(array $data): self
    {
        $instance = new self();
        if (isset($data['regionCode'])) $instance->regionCode = $data['regionCode'];
        if (isset($data['regionName'])) $instance->regionName = $data['regionName'];
        if (isset($data['regionName '])) $instance->regionName = $data['regionName '];
        return $instance;
    }
}
