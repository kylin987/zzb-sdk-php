<?php

namespace ZzbSdk\Model;

/**
 * 影院排片信息查询请求
 */
class QuerySessionInfoRequest
{
    public string $appId;
    public string $version;
    public int $timestamp;
    public int $page;
    public string $cinemaCode;
    public ?string $startDate;
    public ?string $endDate;
    public ?string $signature;

    public static function create(
        string $appId,
        string $cinemaCode,
        ?string $startDate = null,
        ?string $endDate = null,
        int $page = 1,
        string $version = '1.0'
    ): self {
        $instance = new self();
        $instance->appId = $appId;
        $instance->version = $version;
        $instance->timestamp = time();
        $instance->page = $page;
        $instance->cinemaCode = $cinemaCode;
        $instance->startDate = $startDate;
        $instance->endDate = $endDate;
        $instance->signature = null;
        return $instance;
    }

    public function toArray(): array
    {
        return [
            'appId' => $this->appId,
            'version' => $this->version,
            'timestamp' => $this->timestamp,
            'page' => $this->page,
            'data' => [
                'cinemaCode' => $this->cinemaCode,
                'startDate' => $this->startDate,
                'endDate' => $this->endDate,
            ],
            'signature' => $this->signature,
        ];
    }
}
