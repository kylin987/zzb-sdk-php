# Changelog

## [0.1.0] - 2026-03-23

- 抽离 `zzb-sdk-php` 为独立 Composer 包目录。
- 补齐 `composer.json`、`README.md`、`LICENSE`、测试配置等发布基础文件。
- 对齐当前现网 `queryCinemaInfo`、`queryScreenInfo`、`queryFilmInfo` 的可用签名规则。
- 增加递归排序，保证嵌套 `data` 字段参与签名时顺序稳定。
- 默认禁用系统代理环境变量，避免请求被本机代理意外劫持。
