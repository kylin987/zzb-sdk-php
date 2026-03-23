# zzb-sdk-php

用于对接专资办接口的 PHP SDK，适配当前已验证可用的现网查询接口：

- `queryCinemaInfo`
- `queryScreenInfo`
- `queryFilmInfo`

同时保留票房上报与数据比对文件下载能力。

## Requirements

- PHP `>=8.1`
- `ext-curl`
- `ext-openssl`

## Installation

```bash
composer require kylin987/zzb-sdk-php
```

## Supported Capabilities

- 影院信息查询
- 影厅信息查询
- 影片信息查询
- 票房上报
- 比对文件下载

## Quick Start

```php
<?php

use ZzbSdk\Config;
use ZzbSdk\ZzbService;

$config = new Config([
    'serviceUrl' => 'https://218.241.227.141:8000/serverapp',
    'reportUrl' => 'https://218.241.227.141:8000/serverapp',
    'channelCode' => '98982402',
    'certId' => '370100',
    'appId' => '370100',
    'interfaceKey' => '00000000',
    'certFile' => '/path/to/private_key.pem',
    'certFilePwd' => 'your-cert-password',
    'trustFile' => '/path/to/rootcert.pem',
    'version' => '1.0',
]);

$service = new ZzbService($config);
$result = $service->getCinemaInfo('96900105');

if ($result->isSuccess()) {
    var_dump($result->data);
}
```

## Query API Notes

当前现网 `query*` 接口的已验证行为如下：

- 仍然要求 HTTPS 双向 TLS
- 请求体包含 `appId`、`version`、`timestamp`、`data`、`signature`
- `interfaceKey` 作为签名原文中的 `password`
- 签名前需要对对象字段递归排序
- 摘要算法为 `SM3`
- `signature = Base64(SM3(json_string))`

这套行为来自现网验证结果，可能与标准 PDF 中的路径定义不同。

## Running Tests

```bash
composer install
vendor/bin/phpunit
```

## External Requirements

本包不包含以下外部材料，需要接入方自行准备：

- 客户端证书与私钥
- 根证书或信任证书
- `appId`、`channelCode`、`interfaceKey` 等接口参数
- 对接方提供的标准文档或补充说明

## Security

- 不要将真实证书、私钥、密码提交到仓库。
- 建议通过环境变量或部署系统注入敏感配置。
- 如上游接口切换到标准 PDF 路径，建议新增兼容模式，不要直接覆盖现网实现。
