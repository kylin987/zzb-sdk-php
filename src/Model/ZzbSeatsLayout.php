<?php

namespace ZzbSdk\Model;

/**
 * 影厅座位图
 */
class ZzbSeatsLayout
{
    public ?string $layoutVersion;
    public ?string $effectiveDate;
    /**
     * @var ZzbSeatRegion[]
     */
    public array $regions = [];
    /**
     * @var ZzbSeatSection[]
     */
    public array $sections = [];
    /**
     * @var ZzbScreenSeat[]
     */
    public array $seatList = [];

    public static function fromArray(array $data): self
    {
        $instance = new self();
        if (isset($data['layoutVersion'])) $instance->layoutVersion = $data['layoutVersion'];
        if (isset($data['effectiveDate'])) $instance->effectiveDate = $data['effectiveDate'];
        if (isset($data['regions']) && is_array($data['regions'])) {
            foreach ($data['regions'] as $regionData) {
                $instance->regions[] = ZzbSeatRegion::fromArray($regionData);
            }
        }
        if (isset($data['sections']) && is_array($data['sections'])) {
            foreach ($data['sections'] as $sectionData) {
                $instance->sections[] = ZzbSeatSection::fromArray($sectionData);
            }
        }
        if (isset($data['seatList']) && is_array($data['seatList'])) {
            foreach ($data['seatList'] as $seatData) {
                $instance->seatList[] = ZzbScreenSeat::fromArray($seatData);
            }
        }
        return $instance;
    }
}
