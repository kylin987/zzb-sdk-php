<?php

namespace ZzbSdk\Model;

/**
 * 座位图分区
 */
class ZzbSeatSection
{
    public ?string $sectionCode;
    public ?string $sectionName;

    public static function fromArray(array $data): self
    {
        $instance = new self();
        if (isset($data['sectionCode'])) $instance->sectionCode = $data['sectionCode'];
        if (isset($data['sectionName'])) $instance->sectionName = $data['sectionName'];
        return $instance;
    }
}
