# Changelog

## [2.0.4] - 2026-06-17

- 修正 `netsale2025` 模式下 `downloadReportRecord()` 的 V-STK 签名原文，`data` 内同步包含 `sendChannelCode` 以匹配平台验签原文。
- 增加 `Config::httpLogger` 回调，`ZzbService::post()` 可记录请求 URL、HTTP 状态、请求体、响应原文和 JSON 解码结果，便于业务侧留存专资办原始响应。

## [2.0.0] - 2026-06-15

- 增加 `Config::MODE_NETSALE_2025`，显式启用 2025 网售商协议；默认仍保留 1.x legacy 行为。
- 增加 `ZzbSdk\Signer\VstkSignerInterface`，用于注入 V-STK `p7AttachSign()` 能力。
- `netsale2025` 模式下，`reportTicket()` 按 2025 文档发送根级 `sendChannelCode` 和 `ticketList`。
- `netsale2025` 模式下，`downloadReportRecord()` 使用毫秒时间戳签名原文并返回原始文件流。
- 增加 `keyFile` / `keyFilePwd` 配置，支持 PEM 证书和私钥分离的双向 HTTPS 配置。
- 增加网售商 2025 模式单元测试，确保 1.x 默认行为不被隐式切换。

## [0.1.0] - 2026-03-23

- 抽离 `zzb-sdk-php` 为独立 Composer 包目录。
- 补齐 `composer.json`、`README.md`、`LICENSE`、测试配置等发布基础文件。
- 对齐当前现网 `queryCinemaInfo`、`queryScreenInfo`、`queryFilmInfo` 的可用签名规则。
- 增加递归排序，保证嵌套 `data` 字段参与签名时顺序稳定。
- 默认禁用系统代理环境变量，避免请求被本机代理意外劫持。
