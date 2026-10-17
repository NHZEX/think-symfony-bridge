# ThinkPHP Symfony Console Bridge

将 ThinkPHP 命令行输入、输出适配为 Symfony Console 接口。

[![Latest Stable Version](https://poser.pugx.org/zxin/think-symfony-bridge/v/stable)](https://packagist.org/packages/zxin/think-symfony-bridge)
[![License](https://poser.pugx.org/zxin/think-symfony-bridge/license)](https://packagist.org/packages/zxin/think-symfony-bridge)

## 版本要求

当前为 4.0 候选版，尚未发布：PHP 8.1+，Symfony Console 5.4 / 6.x / 7.x（7.x 需要 PHP 8.2+）。ThinkPHP 依赖范围为 6.x / 8.0 / 8.1，已验证 8.1；旧版验证限制见 [兼容说明](COMPATIBILITY.md)。PHP 7.4 / 8.0 项目请保留 3.x 及其兼容的 Symfony 版本。

## 安装

试用候选分支：

```sh
composer require zxin/think-symfony-bridge:dev-fix/symfony-7-compat
```

## 使用

在 ThinkPHP 命令的 `execute()` 中包装现有输入、输出，再传给需要 Symfony 接口的代码：

```php
use Zxin\Think\Symfony\Console\InputBridge;
use Zxin\Think\Symfony\Console\OutputBridge;

// $input / $output 为 ThinkPHP 传入的输入、输出对象。
$symfonyInput = new InputBridge($input);
$symfonyOutput = new OutputBridge($output);
$symfonyOutput->writeln('Hello');
```

参数绑定和输出格式化仍由 ThinkPHP 负责。接口边界、升级变化与测试方法见 [兼容说明](COMPATIBILITY.md)。
