<?php

namespace ZzbSdk\Model;

/**
 * 结果
 */
class ZzbResult
{
    public ?string $code;
    public ?string $status;
    public $data; // 可以是任何类型
    public ?string $traceId;

    public static function fromArray(array $data): self
    {
        $instance = new self();
        if (isset($data['code'])) $instance->code = $data['code'];
        if (isset($data['status'])) $instance->status = $data['status'];
        if (isset($data['data'])) $instance->data = $data['data'];
        if (isset($data['traceId'])) $instance->traceId = $data['traceId'];
        return $instance;
    }

    public function isSuccess(): bool
    {
        return $this->code === '200';
    }
}
