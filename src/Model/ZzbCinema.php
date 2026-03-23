<?php

namespace ZzbSdk\Model;

/**
 * 影院
 */
class ZzbCinema
{
    /**
     * 电影院编码 8位
     */
    public ?string $cinemaCode;

    /**
     * 电影院名称
     */
    public ?string $cinemaName;

    /**
     * 企业名称
     */
    public ?string $officialName;

    /**
     * 影院经理姓名
     */
    public ?string $manager;

    /**
     * 影院经理电话
     */
    public ?string $managerTel;

    /**
     * 传真号
     */
    public ?string $fax;

    /**
     * 所属院线
     */
    public ?string $cinemaChainName;

    /**
     * 影厅数量
     */
    public ?int $screens;

    /**
     * 营业状态
     * 11：注销
     * 12：营业
     * 13：停业
     */
    public ?string $businessStatus;

    public ?string $legalPerson;
    public ?string $legalPersonTel;
    public ?string $sales;
    public ?string $salesTel;
    public ?string $telephone;
    public ?string $cityCode;
    public ?string $cityName;

    /**
     * 影院级别
     * 1："市"
     * 2："县"
     * 3："乡
     */
    public ?int $cinemaLevel;

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
