<?php

namespace ZzbSdk\Model;

/**
 * 影院排片场次
 */
class ZzbSession
{
    public ?string $sessionCode;
    public ?float $lowestPrice;
    public ?float $standardPrice;
    public ?float $netServiceFee;
    public ?string $stopSellTime;
    public ?string $screenCode;
    public ?string $filmCode;
    public ?string $filmName;
    public ?string $sessionDatetime;
    public ?int $duration;
    public ?string $layoutVersion;
    /**
     * @var ZzbSessionSection[]
     */
    public array $sectionList = [];

    public static function fromArray(array $data): self
    {
        $instance = new self();
        if (isset($data['sessionCode'])) $instance->sessionCode = $data['sessionCode'];
        if (isset($data['lowestPrice'])) $instance->lowestPrice = (float) $data['lowestPrice'];
        if (isset($data['standardPrice'])) $instance->standardPrice = (float) $data['standardPrice'];
        if (isset($data['netServiceFee'])) $instance->netServiceFee = (float) $data['netServiceFee'];
        if (isset($data['stopSellTime'])) $instance->stopSellTime = $data['stopSellTime'];
        if (isset($data['screenCode'])) $instance->screenCode = $data['screenCode'];
        if (isset($data['filmCode'])) $instance->filmCode = $data['filmCode'];
        if (isset($data['filmName'])) $instance->filmName = $data['filmName'];
        if (isset($data['sessionDatetime'])) $instance->sessionDatetime = $data['sessionDatetime'];
        if (isset($data['duration'])) $instance->duration = (int) $data['duration'];
        if (isset($data['layoutVersion'])) $instance->layoutVersion = $data['layoutVersion'];
        if (isset($data['sectionList']) && is_array($data['sectionList'])) {
            foreach ($data['sectionList'] as $sectionData) {
                $instance->sectionList[] = ZzbSessionSection::fromArray($sectionData);
            }
        }
        return $instance;
    }
}
