<?php

namespace ZzbSdk\Signer;

interface VstkSignerInterface
{
    /**
     * 使用 V-STK p7AttachSign 对原文签名。
     *
     * 返回值可以是 V-STK 已经返回的 Base64 签名字符串，也可以是原始二进制签名。
     */
    public function p7AttachSign(string $certId, string $plainText): string;
}
