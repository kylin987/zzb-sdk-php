<?php

namespace ZzbSdk\Model;

/**
 * 影厅
 */
class ZzbScreen
{
    /**
     * 影厅编码 16位
     */
    public ?string $screenCode;

    /**
     * 影厅名称
     */
    public ?string $screenName;

    /**
     * 座位数量
     */
    public ?int $seatCount;

    /**
     * 影厅类型
     * vip:VIP
     */
    public ?string $hallType;

    /**
     * 放映制式
     * 影厅放映类型：
     * N:标准
     * CG:中国巨幕
     * C:Cinity
     * IG:IMAX 巨幕
     * D:杜比
     * X:X-LAND
     * L：LED
     * 4D:4D
     * S:特种
     * O:其他
     */
    public ?string $showType;

    /**
     * 是否⼈⺠院线
     */
    public ?bool $isRed;

    /**
     * 是否艺术院线
     */
    public ?bool $isArt;

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
