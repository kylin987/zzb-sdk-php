<?php

namespace ZzbSdk\Model;

/**
 * 影片
 */
class ZzbFilm
{
    /**
     * 影片排次号
     */
    public ?string $filmCode;

    /**
     * 影片名称
     */
    public ?string $filmName;

    /**
     * 别名
     */
    public ?string $aliasName;

    /**
     * 发⾏版本
     */
    public ?string $version;

    /**
     * 影片语别
     */
    public ?string $language;

    /**
     * 片⻓
     */
    public ?int $duration;

    /**
     * 上映⽇期
     */
    public ?string $publishDate;

    /**
     * 出品单位
     */
    public ?string $producer;

    /**
     * 发⾏商
     */
    public ?string $publisher;

    /**
     * 排片开始⽇期
     */
    public ?string $keyStarttime;

    /**
     * 排片结束⽇期
     */
    public ?string $keyEndtime;

    /**
     * 是否复映
     */
    public ?string $reshowFlag;

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
