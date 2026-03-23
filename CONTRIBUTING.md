# Contributing

## Development

```bash
composer install
vendor/bin/phpunit
```

## Guidelines

- 保持 `src/` 下命名空间为 `ZzbSdk\\`。
- 新增模型放在 `src/Model/`，异常放在 `src/Exception/`。
- 修改签名逻辑时，优先补测试，再改实现。
- 不要提交真实证书、密码、接口账号或对接资料原件。

## Pull Requests

- 说明变更目的和影响范围。
- 如涉及签名、证书、接口路径调整，写清验证方式。
- 如变更现网兼容行为，附上最小请求示例或响应对比。
