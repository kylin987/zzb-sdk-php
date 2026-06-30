# zzb-sdk-php

> **已弃用** — 本包已拆分为两个独立包，不再维护新功能。

## 迁移说明

本包原有的双模式（legacy + netsale2025）已按业务职责拆分为两个独立 SDK：

| 旧包 | 新包 | 用途 |
|---|---|---|
| `kylin987/zzb-sdk-php` (legacy 模式) | **[`kylin987/zzb-ticket-sdk`](https://github.com/kylin987/zzb-ticket-sdk)** | 购票交易：影院 / 影厅 / 场次 / 座位查询、锁座、下单、退票 |
| `kylin987/zzb-sdk-php` (netsale2025 模式) | **[`kylin987/netsale-report-sdk`](https://github.com/kylin987/netsale-report-sdk)** | 票房上报：售票 / 退票上报、数据比对文件下载 |

### 为什么拆分

1. **业务无重合**：legacy 模式用于购票流程（查询 / 交易），netsale2025 模式用于正式票房上报，两者签名方式、证书格式、接口地址完全不同。
2. **依赖清晰**：只需上报能力的项目不必引入查询 / 交易代码和 PFX 证书解析逻辑。
3. **独立演进**：两个协议各自迭代，互不影响。

### 迁移步骤

#### 1. 替换依赖

```bash
# 移除旧包
composer remove kylin987/zzb-sdk-php

# 按需安装新包
composer require kylin987/zzb-ticket-sdk      # 购票/查询/交易
composer require kylin987/netsale-report-sdk   # 票房上报
```

#### 2. 命名空间替换

| 旧命名空间 | 新命名空间 | 所属包 |
|---|---|---|
| `ZzbSdk\ZzbService` | `ZzbTicketSdk\ZzbTicketService` | zzb-ticket-sdk |
| `ZzbSdk\Config` | `ZzbTicketSdk\Config` | zzb-ticket-sdk |
| `ZzbSdk\Exception\ZzbException` | `ZzbTicketSdk\Exception\ZzbTicketException` | zzb-ticket-sdk |
| `ZzbSdk\Model\*` | `ZzbTicketSdk\Model\*` | zzb-ticket-sdk |
| `ZzbSdk\ZzbService` (netsale2025) | `NetsaleReportSdk\NetsaleReportClient` | netsale-report-sdk |
| `ZzbSdk\Config` (netsale2025) | `NetsaleReportSdk\Config` | netsale-report-sdk |
| `ZzbSdk\Model\ZzbTicket` | `NetsaleReportSdk\Model\ReportTicket` | netsale-report-sdk |
| `ZzbSdk\Model\ZzbResult` | `NetsaleReportSdk\Model\ReportResult` | netsale-report-sdk |
| `ZzbSdk\Signer\VstkSignerInterface` | `NetsaleReportSdk\Signer\VstkSignerInterface` | netsale-report-sdk |

#### 3. 配置变更

**购票 / 交易（zzb-ticket-sdk）**：
- 移除 `mode` 配置（不再需要，纯 legacy）
- 移除 `vstkSignerUrl` 配置（V-STK 签名属于上报包）

**票房上报（netsale-report-sdk）**：
- 移除 `mode`、`appId`、`interfaceKey`、`certFilePwd`、`cssConfigFile` 等 legacy 字段
- 证书只支持 PEM 格式（`certFile` + `keyFile` 分离），不再处理 PFX/P12
- `downloadReportRecord` 需要配置 `vstkSigner`（实现 `VstkSignerInterface`）

#### 4. 方法变更

**购票 / 交易包**保留了所有查询和交易方法，以及 legacy 模式的上报方法：
- `getCinemaInfo`、`getScreenInfo`、`downloadFilmInfo`
- `querySessionInfo`、`queryScreenSeatInfo`、`querySeatStatusInfo`
- `lockSeatsInfo`、`releaseSeatsInfo`、`submitOrder`
- `queryOrderInfo`、`refundTicket`、`refundTicketByLockOrderId`
- `reportTicket`（legacy）、`reportTicketByLockOrderId`
- `downloadReportRecord`（legacy）

**上报包**只保留两个方法：
- `reportTicket` — 2025 协议售票 / 退票上报
- `downloadReportRecord` — 2025 协议比对文件下载（需 V-STK 签名）

旧包中的混合模式方法（`reportTicketByLockOrderId`、`refundReportTicketByLockOrderId` 等"先查单再上报"的方法）不再包含在上报包中，由业务层自行编排查询和上报。

### 旧版本归档

旧包的最后版本为 `v2.0.4`，已归档在此仓库中。如果需要回退，可以使用：

```bash
composer require kylin987/zzb-sdk-php:^2.0
```

但不建议长期使用旧包，新包的代码更清晰、依赖更少。

## License

MIT
