<?php

namespace ZzbSdk;

/**
 * 配置信息
 */
class Config
{
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

    public function __construct(array $config = [])
    {
        // 显式初始化所有属性为 null
        $this->reportUrl = null;
        $this->serviceUrl = null;
        $this->channelCode = null;
        $this->certId = null;
        $this->certFile = null;
        $this->certFilePwd = null;
        $this->trustFile = null;
        $this->trustFilePwd = null;
        $this->cssConfigFile = null;
        $this->version = '1.0'; // Default version
        $this->appId = null;
        $this->interfaceKey = null;
        $this->proxy = null;

        // 从配置数组中赋值
        if (isset($config['reportUrl'])) $this->reportUrl = $config['reportUrl'];
        if (isset($config['serviceUrl'])) $this->serviceUrl = $config['serviceUrl'];
        if (isset($config['channelCode'])) $this->channelCode = $config['channelCode'];
        if (isset($config['certId'])) $this->certId = $config['certId'];
        if (isset($config['certFile'])) $this->certFile = $config['certFile'];
        if (isset($config['certFilePwd'])) $this->certFilePwd = $config['certFilePwd'];
        if (isset($config['trustFile'])) $this->trustFile = $config['trustFile'];
        if (isset($config['trustFilePwd'])) $this->trustFilePwd = $config['trustFilePwd'];
        if (isset($config['cssConfigFile'])) $this->cssConfigFile = $config['cssConfigFile'];
        if (isset($config['version'])) $this->version = $config['version'];
        if (isset($config['appId'])) $this->appId = $config['appId'];
        if (isset($config['interfaceKey'])) $this->interfaceKey = $config['interfaceKey'];
        if (isset($config['proxy'])) $this->proxy = $config['proxy'];
    }
}
