<?php

namespace ZzbSdk\Model;

/**
 * 锁座结果
 */
class ZzbLockSeatsResult
{
    public ?string $cinemaCode;
    public ?string $appLockId;
    public ?string $lockResult;
    public ?string $resultCode;
    public ?string $resultMsg;
    public ?string $lockOrderId;
    public ?int $count;
    public ?string $autoUnlockDatetime;

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
