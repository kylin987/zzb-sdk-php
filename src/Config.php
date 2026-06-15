<?php

namespace ZzbSdk;

/**
 * 配置信息
 */
class Config
{
    public const MODE_LEGACY = 'legacy';
    public const MODE_NETSALE_2025 = 'netsale2025';

    /**
     * SDK 协议模式。默认保留 1.x 现网兼容行为。
     */
    public string $mode;

    /**
     * 数据上报接口地址
     */
    public ?string $reportUrl;

    /**
     * 信息下载接口地址
     */
    public ?string $serviceUrl;

    /**
     * 网售编码
     */
    public ?string $channelCode;

    /**
     * 证书ID
     */
    public ?string $certId;

    /**
     * 证书文件路径
     */
    public ?string $certFile;

    /**
     * 证书文件密码
     */
    public ?string $certFilePwd;

    /**
     * 独立私钥文件路径。2.x 网售商模式推荐使用 PEM 证书 + PEM 私钥。
     */
    public ?string $keyFile;

    /**
     * 独立私钥文件密码。
     */
    public ?string $keyFilePwd;

    /**
     * 信任文件路径
     */
    public ?string $trustFile;

    /**
     * 信任文件密码
     */
    public ?string $trustFilePwd;

    /**
     * cssconfig.properties文件路径
     */
    public ?string $cssConfigFile;

    /**
     * API版本号
     */
    public ?string $version;

    /**
     * AppID
     */
    public ?string $appId;

    /**
     * 接口密钥 (用于 HMAC 签名)
     */
    public ?string $interfaceKey;

    /**
     * 可选代理地址；不配置时默认禁用系统代理环境变量
     */
    public ?string $proxy;

    /**
     * V-STK P7 Attach 签名器，可传入 ZzbSdk\Signer\VstkSignerInterface 实例。
     */
    public mixed $vstkSigner;

    public function __construct(array $config = [])
    {
        // 显式初始化所有属性为 null
        $this->mode = self::MODE_LEGACY;
        $this->reportUrl = null;
        $this->serviceUrl = null;
        $this->channelCode = null;
        $this->certId = null;
        $this->certFile = null;
        $this->certFilePwd = null;
        $this->keyFile = null;
        $this->keyFilePwd = null;
        $this->trustFile = null;
        $this->trustFilePwd = null;
        $this->cssConfigFile = null;
        $this->version = '1.0'; // Default version
        $this->appId = null;
        $this->interfaceKey = null;
        $this->proxy = null;
        $this->vstkSigner = null;

        // 从配置数组中赋值
        if (isset($config['mode'])) $this->mode = $config['mode'];
        if (isset($config['reportUrl'])) $this->reportUrl = $config['reportUrl'];
        if (isset($config['serviceUrl'])) $this->serviceUrl = $config['serviceUrl'];
        if (isset($config['channelCode'])) $this->channelCode = $config['channelCode'];
        if (isset($config['certId'])) $this->certId = $config['certId'];
        if (isset($config['certFile'])) $this->certFile = $config['certFile'];
        if (isset($config['certFilePwd'])) $this->certFilePwd = $config['certFilePwd'];
        if (isset($config['keyFile'])) $this->keyFile = $config['keyFile'];
        if (isset($config['keyFilePwd'])) $this->keyFilePwd = $config['keyFilePwd'];
        if (isset($config['trustFile'])) $this->trustFile = $config['trustFile'];
        if (isset($config['trustFilePwd'])) $this->trustFilePwd = $config['trustFilePwd'];
        if (isset($config['cssConfigFile'])) $this->cssConfigFile = $config['cssConfigFile'];
        if (isset($config['version'])) $this->version = $config['version'];
        if (isset($config['appId'])) $this->appId = $config['appId'];
        if (isset($config['interfaceKey'])) $this->interfaceKey = $config['interfaceKey'];
        if (isset($config['proxy'])) $this->proxy = $config['proxy'];
        if (isset($config['vstkSigner'])) $this->vstkSigner = $config['vstkSigner'];
    }
}
