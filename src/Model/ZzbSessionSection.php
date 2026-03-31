<?php

namespace ZzbSdk\Model;

/**
 * 排片分区信息
 */
class ZzbSessionSection
{
    public ?string $regionCode;
    public ?string $regionName;
    public ?string $sectionCode;
    public ?string $sectionName;
    public ?float $sectionPrice;
    public ?float $screenServiceFee;

    public static function fromArray(array $data): self
    {
        $instance = new self();
        if (isset($data['regionCode'])) $instance->regionCode = $data['regionCode'];
        if (isset($data['regionName'])) $instance->regionName = $data['regionName'];
        if (isset($data['sectionCode'])) $instance->sectionCode = $data['sectionCode'];
        if (isset($data['sectionName'])) $instance->sectionName = $data['sectionName'];
        if (isset($data['sectionPrice'])) $instance->sectionPrice = (float) $data['sectionPrice'];
        if (isset($data['screenServiceFee'])) $instance->screenServiceFee = (float) $data['screenServiceFee'];
        return $instance;
    }
}
