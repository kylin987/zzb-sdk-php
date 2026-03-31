<?php

namespace ZzbSdk\Model;

/**
 * 影院排片分页
 */
class ZzbSessionPage
{
    public ?ZzbPageable $pageable;
    public ?string $cinemaCode;
    /**
     * @var ZzbSession[]
     */
    public array $sessionList = [];

    public static function fromArray(array $data, ?array $pageable = null): self
    {
        $instance = new self();
        if ($pageable) {
            $instance->pageable = ZzbPageable::fromArray($pageable);
        }
        if (isset($data['cinemaCode'])) $instance->cinemaCode = $data['cinemaCode'];
        if (isset($data['sessionList']) && is_array($data['sessionList'])) {
            foreach ($data['sessionList'] as $sessionData) {
                $instance->sessionList[] = ZzbSession::fromArray($sessionData);
            }
        }
        return $instance;
    }
}
