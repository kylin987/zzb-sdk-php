<?php

namespace ZzbSdk\Model;

/**
 * 影片信息下载请求
 */
class DownloadFilmInfoRequest
{
    public string $appId;
    public string $version;
    public int $timestamp;
    public string $cinemaCode;
    public string $startPublishDate;
    public string $endPublishDate;
    public int $page;
    public ?string $signature;

    public static function create(string $appId, string $cinemaCode, string $startDate, string $endDate, int $page, string $version = '1.0'): self
    {
        $instance = new self();
        $instance->appId = $appId;
        $instance->version = $version;
        $instance->timestamp = time();
        $instance->cinemaCode = $cinemaCode;
        $instance->startPublishDate = $startDate;
        $instance->endPublishDate = $endDate;
        $instance->page = $page;
        $instance->signature = null;
        return $instance;
    }

    public function toArray(): array
    {
        return [
            'appId' => $this->appId,
            'version' => $this->version,
            'timestamp' => $this->timestamp,
            'page' => $this->page, // Move page to root
            'data' => [
                'cinemaCode' => $this->cinemaCode,
                'startPublishDate' => $this->startPublishDate,
                'endPublishDate' => $this->endPublishDate,
            ],
            'signature' => $this->signature,
        ];
    }
}
