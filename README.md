# ThinkPHP Symfony Console Bridge

[![Latest Stable Version](https://poser.pugx.org/zxin/think-symfony-bridge/v/stable)](https://packagist.org/packages/zxin/think-symfony-bridge)
[![License](https://poser.pugx.org/zxin/think-symfony-bridge/license)](https://packagist.org/packages/zxin/think-symfony-bridge)


## 4.0 兼容升级（候选，尚未发布）

- 最低 PHP 8.1；支持 Symfony Console 5.4、6.x、7.x，以及 ThinkPHP 6.x、8.0、8.1。
- Symfony 7 的接口要求 `mixed` / `void` 返回类型，不能继续以同一份实现支持 PHP 7.4。PHP 7.4 / 8.0 项目应保留 3.x，并使用其兼容的 Symfony 版本；3.x 的 Symfony 7 声明不代表实际接口兼容。
- 补齐 Input / Output 接口签名，并提供新版接口的 `__toString()`，直接委托 ThinkPHP 的命令行转义实现。
- `getArguments()` 保留数组参数和 `null` 默认值，修复原实现抛错。
- 输出详细程度使用 Symfony 同一量纲进行比较，修复普通输出模式误显示 debug 消息；支持 Symfony 7.2+ 的 silent 模式。

### 使用边界

本库适配已经由 ThinkPHP 绑定的输入，因此 Symfony `bind()` 仍为空操作。原始参数查询直接委托 ThinkPHP；`onlyParams` 尚不支持过滤 `--` 之后的参数。
输出格式化继续由 ThinkPHP 负责；`setFormatter()` / `setDecorated()` 保留原来的空操作，`isDecorated()` 为 false。此次升级不改变这些既有行为。

输出测试使用 ThinkPHP Buffer 驱动验证委托，并不等于完整终端输出语义：ThinkPHP Console 驱动在 quiet 模式下会无条件抑制消息，即使该消息显式使用 Symfony `VERBOSITY_QUIET`；Buffer 驱动则可保留该消息。这是底层驱动的既有差异，本次不改变。

### 验证

测试加载真实安装的 Symfony 接口及 ThinkPHP 类，不使用伪造接口；覆盖全部接口方法、输入读写和数组/null值、字符串转义、输出委托、verbosity过滤、silent、可迭代消息及输出类型映射。

```sh
composer install
composer test
# 使用临时约束切换真实依赖，不改 composer.json：
composer update --with 'symfony/console:^6.4'
composer test
composer update --with 'symfony/console:^5.4'
composer test
composer update --with 'symfony/console:^7.0'
composer test
```

CI 配置分别验证 PHP 8.1 + Symfony 5.4 / 6.4，以及 PHP 8.2 / 8.4 + Symfony 7；Symfony 7 自身要求 PHP 8.2+。
本分支不代表 4.0.0 已发布。联调可通过 Composer path 仓库临时指定版本；正式 `^4.0` 消费必须等待审核、发布相应 tag。

### 本次本地验证记录

PHP 8.4.24 + ThinkPHP 8.1.4，分别安装 Symfony Console 5.4.54、6.4.48、7.4.21，三组均通过 57 项断言；`composer validate --strict`、所有源码及测试 PHP 语法检查通过。PHP 8.1/8.2 运行时由 CI 矩阵覆盖，本地未执行，不将 CI 配置视为已通过结果。

另外尝试安装 ThinkPHP 6.x / 8.0.x 时，被 Composer 的安全公告拦截，未关闭安全防护，故这两组没有实际测试。保留原依赖范围不构成安全背书；建议使用仍可通过 Composer 安全检查的 ThinkPHP 8.1 版本。
