<?php

namespace ZzbSdk\Model;

/**
 * 分页数据
 */
class ZzbPageable
{
    public int $totalPages;
    public int $page;
    public int $size;

    public static function fromArray(array $data): self
    {
        $instance = new self();
        if (isset($data['totalPages'])) $instance->totalPages = $data['totalPages'];
        if (isset($data['page'])) $instance->page = $data['page'];
        if (isset($data['size'])) $instance->size = $data['size'];
        return $instance;
    }
}
