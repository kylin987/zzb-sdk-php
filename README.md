# zzb-sdk-php

用于对接专资办接口的 PHP SDK，适配当前已验证可用的现网查询接口：

- `queryCinemaInfo`
- `queryScreenInfo`
- `queryFilmInfo`
- `querySessionInfo`
- `queryScreenSeatInfo`
- `querySeatStatusInfo`
- `lockSeatsInfo`
- `releaseSeatsInfo`
- `submitOrder`
- `queryOrderInfo`
- `refundTicket`
- `reportTicketByLockOrderId`
- `refundTicketByLockOrderId`
- `refundReportTicketByLockOrderId`

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
- 影院排片信息查询
- 影厅座位信息查询
- 场次座位售出状态查询
- 座位锁定
- 座位解锁
- 确认订单交易
- 订单信息查询
- 退票
- 基于锁座订单号的售票上报
- 基于锁座订单号的退票
- 基于锁座订单号的退票上报
- 票房上报
- 比对文件下载
- 2.x: 2025 网售商 `reportTicket` 标准报文
- 2.x: 2025 网售商 `downloadReportRecord` V-STK P7 Attach 签名

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

## 2.x 网售商 2025 模式

2.x 保留 1.x 默认行为。只有显式配置 `mode => Config::MODE_NETSALE_2025` 时，才会按 2025 网售商文档发送报文。

```php
<?php

use ZzbSdk\Config;
use ZzbSdk\Signer\VstkSignerInterface;
use ZzbSdk\ZzbService;

$vstkSigner = new class implements VstkSignerInterface {
    public function p7AttachSign(string $certId, string $plainText): string
    {
        // 在这里调用 Java V-STK、Windows COM，或本地 V-STK 签名服务。
        // 返回 V-STK p7AttachSign 结果；如果已经是 Base64，SDK 会直接使用。
        return '';
    }
};

$config = new Config([
    'mode' => Config::MODE_NETSALE_2025,
    'reportUrl' => 'https://panda.zgdypw.cn:8087/report',
    'serviceUrl' => 'https://panda.zgdypw.cn:8085/service',
    'channelCode' => 'YOUR_CHANNEL_CODE',
    'certId' => 'YOUR_CERT_ID',
    'certFile' => '/path/to/client_cert.pem',
    'keyFile' => '/path/to/client_key.pem',
    'trustFile' => '/path/to/zzb_rootcert.pem',
    'vstkSigner' => $vstkSigner,
]);

$service = new ZzbService($config);
```

### 2.x reportTicket

`netsale2025` 模式下，`reportTicket()` 会发送 2025 文档要求的根级结构，不再使用 1.x 的 `X-Signature` 请求头，也不会包 `data`。

```json
{
  "sendChannelCode": "YOUR_CHANNEL_CODE",
  "ticketList": []
}
```

### 2.x downloadReportRecord

`downloadReportRecord()` 会构造如下签名原文，并调用 `VstkSignerInterface::p7AttachSign()`：

```json
{
  "data": {
    "sendChannelCode": "YOUR_CHANNEL_CODE",
    "startShowDate": "2026-06-01",
    "endShowDate": "2026-06-02"
  },
  "sendChannelCode": "YOUR_CHANNEL_CODE",
  "timestamp": 1780000000000
}
```

实际请求体为：

```json
{
  "sendChannelCode": "YOUR_CHANNEL_CODE",
  "startShowDate": "2026-06-01",
  "endShowDate": "2026-06-02",
  "signature": "P7_ATTACH_SIGNATURE"
}
```

返回值是原始文件流内容，调用方应自行保存为 zip 或按业务需要处理。

## API Examples

以下示例默认你已经准备好了同一份 `$config` 和 `$service`：

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

### getCinemaInfo

```php
<?php

$result = $service->getCinemaInfo('your-cinema-code');

if ($result->isSuccess()) {
    var_dump($result->data);
} else {
    var_dump($result->code, $result->status);
}
```

### getScreenInfo

```php
<?php

$result = $service->getScreenInfo('your-cinema-code');

if ($result->isSuccess()) {
    var_dump($result->data);
} else {
    var_dump($result->code, $result->status);
}
```

### downloadFilmInfo

```php
<?php

$result = $service->downloadFilmInfo('your-cinema-code', '2026-01-01', '2026-12-31', 1);

if ($result->isSuccess()) {
    var_dump($result->data);
} else {
    var_dump($result->code, $result->status);
}
```

### querySessionInfo

```php
<?php

$result = $service->querySessionInfo('your-cinema-code', '2026-03-31', '2026-03-31', 1);

if ($result->isSuccess()) {
    var_dump($result->data);
} else {
    var_dump($result->code, $result->status);
}
```

已验证可用参数示例：

```php
<?php

$result = $service->querySessionInfo('your-cinema-code', '2026-04-01', '2026-04-02', 1);
```

已验证返回示例：

```json
{
  "code": "200",
  "status": "success",
  "pageable": {
    "totalPages": 5,
    "page": 1,
    "size": 6
  },
  "data": {
    "cinemaCode": "your-cinema-code",
    "sessionList": [
      {
        "sessionCode": "your-session-code",
        "lowestPrice": 30.0,
        "standardPrice": 50.0,
        "netServiceFee": 2.0,
        "stopSellTime": "2026-04-02 08:00:00",
        "screenCode": "your-screen-code",
        "filmCode": "your-film-code",
        "filmName": "your-film-name",
        "sessionDatetime": "2026-04-02 08:00:00",
        "duration": 120,
        "layoutVersion": "座位图V1",
        "sectionList": [
          {
            "regionCode": "CQ1",
            "regionName": "一楼",
            "sectionCode": "FQ1",
            "sectionName": "普通区",
            "sectionPrice": 60.0,
            "screenServiceFee": 10.0
          }
        ]
      }
    ]
  }
}
```

### querySeatStatusInfo

```php
<?php

$result = $service->querySeatStatusInfo('your-cinema-code', 'your-session-code', ['LOCKED', 'SOLD']);

if ($result->isSuccess()) {
    var_dump($result->data);
} else {
    var_dump($result->code, $result->status);
}
```

已验证返回示例：

```json
{
  "code": "200",
  "status": "success",
  "data": {
    "cinemaCode": "your-cinema-code",
    "sessionCode": "your-session-code",
    "seatList": []
  }
}
```

### queryScreenSeatInfo

```php
<?php

$result = $service->queryScreenSeatInfo('your-cinema-code', 'your-screen-code');

if ($result->isSuccess()) {
    var_dump($result->data);
} else {
    var_dump($result->code, $result->status);
}
```

### lockSeatsInfo

```php
<?php

$result = $service->lockSeatsInfo(
    'your-cinema-code',
    'your-session-code',
    'your-app-lock-id',
    [
        ['seatCode' => 'your-seat-code-1'],
        ['seatCode' => 'your-seat-code-2'],
    ]
);

if ($result->isSuccess()) {
    var_dump($result->data);
} else {
    var_dump($result->code, $result->status);
}
```

### submitOrder

```php
<?php

$result = $service->submitOrder(
    'your-lock-order-id',
    'your-cinema-code',
    'your-session-code',
    [
        [
            'seatCode' => 'your-seat-code',
            'regionCode' => 'your-region-code',
            'sectionCode' => 'your-section-code',
            'ticketPrice' => 60.00,
            'screenServiceFee' => 10.00,
            'netServiceFee' => 0.00,
        ],
    ]
);

if ($result->isSuccess()) {
    var_dump($result->data);
} else {
    var_dump($result->code, $result->status);
}
```

### queryOrderInfo

```php
<?php

$result = $service->queryOrderInfo('your-cinema-code', 'your-lock-order-id');

if ($result->isSuccess()) {
    var_dump($result->data);
} else {
    var_dump($result->code, $result->status);
}
```

### refundTicket

```php
<?php

$result = $service->refundTicket(
    'your-cinema-code',
    'your-order-id',
    [
        ['ticketNo' => 'your-ticket-no-1'],
        ['ticketNo' => 'your-ticket-no-2'],
    ]
);

if ($result->isSuccess()) {
    var_dump($result->data);
} else {
    var_dump($result->code, $result->status);
}
```

### reportTicketByLockOrderId

```php
<?php

$result = $service->reportTicketByLockOrderId('your-cinema-code', 'your-lock-order-id', 10001);

if ($result->isSuccess()) {
    var_dump($result->traceId ?? null);
} else {
    var_dump($result->code, $result->status);
}
```

### refundTicketByLockOrderId

```php
<?php

$result = $service->refundTicketByLockOrderId('your-cinema-code', 'your-lock-order-id');

if ($result->isSuccess()) {
    var_dump($result->data);
} else {
    var_dump($result->code, $result->status);
}
```

### refundReportTicketByLockOrderId

```php
<?php

$result = $service->refundReportTicketByLockOrderId(
    'your-cinema-code',
    'your-lock-order-id',
    10001,
    ['your-ticket-no'],
    '2026-03-31 14:30:00'
);

if ($result->isSuccess()) {
    var_dump($result->traceId ?? null);
} else {
    var_dump($result->code, $result->status);
}
```

### releaseSeatsInfo

```php
<?php

$result = $service->releaseSeatsInfo('your-cinema-code', 'your-lock-order-id', 2);

if ($result->isSuccess()) {
    var_dump($result->data);
} else {
    var_dump($result->code, $result->status);
}
```

### downloadReportRecord

```php
<?php

$content = $service->downloadReportRecord('2026-03-01', '2026-03-31');
var_dump($content);
```

### reportTicket

```php
<?php

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
$ticket->sessionDatetime = '2026-03-31 21:20:00';
$ticket->ticketPrice = 30.00;
$ticket->screenServiceFee = 0.00;
$ticket->netServiceFee = 8.00;
$ticket->saleChannelCode = 'your-sale-channel-code';
$ticket->operation = 1;
$ticket->operationDatetime = '2026-03-31 21:20:00';

$result = $service->reportTicket([$ticket]);

if ($result->isSuccess()) {
    var_dump($result->traceId ?? null);
} else {
    var_dump($result->code, $result->status);
}
```

退票上报同样使用 `reportTicket`，只需将单票的 `operation` 改为 `2`，并传入真实退票时间作为 `operationDatetime`。

## Query API Notes

当前现网 `query*` 接口的已验证行为如下：

- 仍然要求 HTTPS 双向 TLS
- 请求体包含 `appId`、`version`、`timestamp`、`data`、`signature`
- `interfaceKey` 作为签名原文中的 `password`
- 签名前需要对对象字段递归排序
- 摘要算法为 `SM3`
- `signature = Base64(SM3(json_string))`

这套行为来自现网验证结果，可能与标准 PDF 中的路径定义不同。

## Legacy reportTicket API Notes

`reportTicket` 与当前现网 `query*` 接口的报文结构不同，不能直接复用查询接口的完整根级结构。

当前联调结果表明：

- 请求仍然走 HTTPS 双向 TLS
- 签名仍然通过 `X-Signature` 请求头传递
- 请求体不接受根级字段 `appId`、`version`、`timestamp`、`signature`
- 请求体应发送为 `data` 包裹的业务结构

当前 `reportTicket` 实际发送结构如下：

```json
{
  "data": {
    "sendChannelCode": "your-channel-code",
    "ticketList": [
      {
        "numberByDay": 1,
        "parentChannelCode": "your-parent-channel-code",
        "childChannelCode": "your-child-channel-code",
        "ticketNo": "your-ticket-no",
        "cinemaCode": "your-cinema-code",
        "screenCode": "your-screen-code",
        "seatCode": "your-seat-code",
        "filmCode": "your-film-code",
        "sessionCode": "your-session-code",
        "sessionDatetime": "2026-03-31 21:20:00",
        "ticketPrice": 30.00,
        "screenServiceFee": 0.00,
        "netServiceFee": 8.00,
        "saleChannelCode": "your-sale-channel-code",
        "operation": 1,
        "operationDatetime": "2026-03-31 21:20:00"
      }
    ]
  }
}
```

对应 PHP 调用方式保持不变：

```php
<?php

use ZzbSdk\Config;
use ZzbSdk\Model\ZzbTicket;
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
$ticket->sessionDatetime = '2026-03-31 21:20:00';
$ticket->ticketPrice = 30.00;
$ticket->screenServiceFee = 0.00;
$ticket->netServiceFee = 8.00;
$ticket->saleChannelCode = 'your-sale-channel-code';
$ticket->operation = 1;
$ticket->operationDatetime = '2026-03-31 21:20:00';

$service = new ZzbService($config);
$result = $service->reportTicket([$ticket]);
```

说明：

- `sendChannelCode` 来自 SDK 配置中的 `channelCode`
- `saleChannelCode` 由业务侧票务数据决定
- `reportTicketByLockOrderId` / `refundReportTicketByLockOrderId` 需要业务侧传入连续维护的 `numberByDay` 起始值
- `reportTicket` 默认仍为 1.x 现网兼容实现，不建议直接套用查询接口报文结构
- 2025 网售商标准报文请使用 `mode => Config::MODE_NETSALE_2025`

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
- 2025 网售商下载接口所需的 V-STK P7 Attach 签名能力
- 对接方提供的标准文档或补充说明

## Security

- 不要将真实证书、私钥、密码提交到仓库。
- 建议通过环境变量或部署系统注入敏感配置。
- 如上游接口切换到标准 PDF 路径，请显式启用对应模式，不要直接覆盖 1.x 现网配置。
