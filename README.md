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
    'serviceUrl' => 'https://your-host/serverapp',
    'reportUrl' => 'https://your-host/serverapp',
    'channelCode' => 'your-channel-code',
    'certId' => 'your-cert-id',
    'appId' => 'your-app-id',
    'interfaceKey' => 'your-interface-key',
    'certFile' => '/path/to/private_key.pem',
    'certFilePwd' => 'your-cert-password',
    'trustFile' => '/path/to/rootcert.pem',
    'version' => '1.0',
]);

$service = new ZzbService($config);
$result = $service->getCinemaInfo('your-cinema-code');

if ($result->isSuccess()) {
    var_dump($result->data);
}
```

## API Usage

以下示例均基于同一组初始化配置：

```php
<?php

use ZzbSdk\Config;
use ZzbSdk\ZzbService;

$config = new Config([
    'serviceUrl' => 'https://your-host/serverapp',
    'reportUrl' => 'https://your-host/serverapp',
    'channelCode' => 'your-channel-code',
    'certId' => 'your-cert-id',
    'appId' => 'your-app-id',
    'interfaceKey' => 'your-interface-key',
    'certFile' => '/path/to/private_key.pem',
    'certFilePwd' => 'your-cert-password',
    'trustFile' => '/path/to/rootcert.pem',
    'version' => '1.0',
]);

$service = new ZzbService($config);
```

### Config 字段说明

- `serviceUrl`：信息下载类接口地址，例如影院、影厅、影片查询。
- `reportUrl`：数据上报类接口地址，例如票房上报。
- `channelCode`：渠道编码，用于构造业务请求。
- `certId`：证书标识，用于证书签名场景。
- `appId`：应用标识，当前查询接口通常会使用。
- `interfaceKey`：查询接口使用的摘要签名密钥。
- `certFile`：客户端证书或私钥文件路径。
- `certFilePwd`：证书或私钥密码。
- `trustFile`：服务端 CA 或信任证书路径。
- `version`：接口版本号，默认 `1.0`。
- `proxy`：可选代理地址；未配置时默认禁用系统代理环境变量。

建议通过环境变量或部署配置注入敏感信息，不要将真实地址、证书、密码和接口参数直接写入仓库。

### `getCinemaInfo`

用途：查询单个影院信息。  
所需数据：`cinemaCode`。

```php
$result = $service->getCinemaInfo('your-cinema-code');

if ($result->isSuccess()) {
    var_dump($result->data); // ZzbCinema
}
```

### `getScreenInfo`

用途：查询指定影院下的影厅信息。  
所需数据：`cinemaCode`。

```php
$result = $service->getScreenInfo('your-cinema-code');

if ($result->isSuccess()) {
    var_dump($result->data); // ZzbCinemaScreen
}
```

### `downloadFilmInfo`

用途：按影院和上映日期范围查询影片信息。  
所需数据：`cinemaCode`、`startPublishDate`、`endPublishDate`、`page`。

```php
$result = $service->downloadFilmInfo('your-cinema-code', '2026-03-01', '2026-03-31', 1);

if ($result->isSuccess()) {
    var_dump($result->data); // ZzbFilmPage
}
```

### `reportTicket`

用途：上报票房数据。  
所需数据：一个或多个 `ZzbTicket` 对象，每个对象通常需要以下字段：

- `numberByDay`
- `parentChannelCode`
- `childChannelCode`
- `ticketNo`
- `cinemaCode`
- `screenCode`
- `seatCode`
- `filmCode`
- `sessionCode`
- `sessionDatetime`
- `ticketPrice`
- `screenServiceFee`
- `netServiceFee`
- `saleChannelCode`
- `operation`
- `operationDatetime`

```php
use ZzbSdk\Model\ZzbTicket;

$ticket = new ZzbTicket();
$ticket->numberByDay = 1;
$ticket->parentChannelCode = 'your-parent-channel-code';
$ticket->childChannelCode = 'your-child-channel-code';
$ticket->ticketNo = 'your-ticket-no';
$ticket->cinemaCode = 'your-cinema-code';
$ticket->screenCode = 'your-screen-code';
$ticket->seatCode = 'your-seat-code';
$ticket->filmCode = 'your-film-code';
$ticket->sessionCode = 'your-session-code';
$ticket->sessionDatetime = '2026-03-24 19:00:00';
$ticket->ticketPrice = 50.99;
$ticket->screenServiceFee = 3.99;
$ticket->netServiceFee = 5.99;
$ticket->saleChannelCode = 'your-sale-channel-code';
$ticket->operation = 1;
$ticket->operationDatetime = '2026-03-24 18:30:00';

$result = $service->reportTicket([$ticket]);
```

说明：该接口属于写操作，请仅在已确认测试环境、测试证书和测试数据可用时调用。

### `downloadReportRecord`

用途：下载指定放映日期范围的数据比对文件。  
所需数据：`startShowDate`、`endShowDate`。

```php
$content = $service->downloadReportRecord('2026-03-01', '2026-03-02');
```

说明：当前仓库默认配置对应的现网地址上，`/data/downloadReportRecord` 路径未验证可用，使用前请先确认上游实际接口路径。

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
