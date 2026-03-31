<?php

namespace ZzbSdk\Model;

/**
 * 解锁结果
 */
class ZzbReleaseSeatsResult
{
    public ?string $cinemaCode;
    public ?string $lockOrderId;
    public ?int $count;
    public ?string $releaseResult;
    public ?string $resultCode;
    public ?string $resultMsg;

    public static function fromArray(array $data): self
    {
        $instance = new self();
        foreach ($data as $key => $value) {
            if (property_exists($instance, $key)) {
                $instance->$key = in_array($key, ['count'], true) ? (int) $value : $value;
            }
        }
        return $instance;
    }
}
