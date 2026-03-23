<?php

namespace ZzbSdk\Model;

/**
 * 影院影厅
 */
class ZzbCinemaScreen
{
    /**
     * 电影院编码 8位
     */
    public ?string $cinemaCode;

    /**
     * @var ZzbScreen[]
     */
    public array $screenList = [];

    public static function fromArray(array $data): self
    {
        $instance = new self();
        if (isset($data['cinemaCode'])) $instance->cinemaCode = $data['cinemaCode'];
        if (isset($data['screenList']) && is_array($data['screenList'])) {
            foreach ($data['screenList'] as $screenData) {
                $instance->screenList[] = ZzbScreen::fromArray($screenData);
            }
        }
        return $instance;
    }
}
