<?php

namespace ZzbSdk\Model;

/**
 * 影票信息
 */
class ZzbTicket
{
    /**
     * 上报流⽔号
     */
    public ?int $numberByDay;

    /**
     * 渠道上游
     */
    public ?string $parentChannelCode;

    /**
     * 渠道下游
     */
    public ?string $childChannelCode;

    /**
     * 电影票编码
     */
    public ?string $ticketNo;

    /**
     * 影院编码
     */
    public ?string $cinemaCode;

    /**
     * 影厅编码
     */
    public ?string $screenCode;

    /**
     * 座位编码
     */
    public ?string $seatCode;

    /**
     * 影片编码
     */
    public ?string $filmCode;

    /**
     * 场次编码
     */
    public ?string $sessionCode;

    /**
     * 放映时间
     */
    public ?string $sessionDatetime;

    /**
     * 票价 (元) 2位小数
     */
    public ?float $ticketPrice;

    /**
     * 影厅服务费 (元) 2位小数
     */
    public ?float $screenServiceFee;

    /**
     * ⽹络代售服务费 (元) 2位小数
     */
    public ?float $netServiceFee;

    /**
     * 销售⽅编码
     */
    public ?string $saleChannelCode;

    /**
     * 操作类型 1：售票（缺省） 2：退票
     */
    public ?int $operation;

    /**
     * 操作时间
     */
    public ?string $operationDatetime;

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

    public function toArray(): array
    {
        return [
            'numberByDay' => isset($this->numberByDay) ? $this->numberByDay : null,
            'parentChannelCode' => isset($this->parentChannelCode) ? $this->parentChannelCode : null,
            'childChannelCode' => isset($this->childChannelCode) ? $this->childChannelCode : null,
            'ticketNo' => isset($this->ticketNo) ? $this->ticketNo : null,
            'cinemaCode' => isset($this->cinemaCode) ? $this->cinemaCode : null,
            'screenCode' => isset($this->screenCode) ? $this->screenCode : null,
            'seatCode' => isset($this->seatCode) ? $this->seatCode : null,
            'filmCode' => isset($this->filmCode) ? $this->filmCode : null,
            'sessionCode' => isset($this->sessionCode) ? $this->sessionCode : null,
            'sessionDatetime' => isset($this->sessionDatetime) ? $this->sessionDatetime : null,
            'ticketPrice' => isset($this->ticketPrice) ? $this->ticketPrice : null,
            'screenServiceFee' => isset($this->screenServiceFee) ? $this->screenServiceFee : null,
            'netServiceFee' => isset($this->netServiceFee) ? $this->netServiceFee : null,
            'saleChannelCode' => isset($this->saleChannelCode) ? $this->saleChannelCode : null,
            'operation' => isset($this->operation) ? $this->operation : null,
            'operationDatetime' => isset($this->operationDatetime) ? $this->operationDatetime : null,
        ];
    }
}
