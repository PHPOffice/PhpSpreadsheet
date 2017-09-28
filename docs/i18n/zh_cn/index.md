# 欢迎阅读 PhpSpreadsheet 中文文档

![Logo](../../assets/logo.svg)

PhpSpreadsheet 是一个用纯PHP撰写的库，它提供了一系列能够支持你读写不同格式表格文件的类，对Excel、LibreOffice这些常用软件所支持的格式都能轻松应对。

## 文件格式支持

|格式                                      |读|写|
|--------------------------------------------|:-----:|:-----:|
|Open Document Format/OASIS (.ods)           |   ✓   |   ✓   |
|Office Open XML (.xlsx) Excel 2007 and above|   ✓   |   ✓   |
|BIFF 8 (.xls) Excel 97 and above            |   ✓   |   ✓   |
|BIFF 5 (.xls) Excel 95                      |   ✓   |       |
|SpreadsheetML (.xml) Excel 2003             |   ✓   |       |
|Gnumeric                                    |   ✓   |       |
|HTML                                        |   ✓   |   ✓   |
|SYLK                                        |   ✓   |       |
|CSV                                         |   ✓   |   ✓   |
|PDF (可以使用 tcPDF， DomPDF，mPDF 等库, 不管需要单独安装)|       |   ✓   |

# 开始使用

## 环境需求

使用PhpSpreadsheet需要下方列表的支持：

-   PHP 的版本需要 >= 5.6
-   安装并开启了 php\_zip 扩展
-   安装并开启了 php\_xml 扩展
-   安装并开启了 php\_gd2 扩展 (没有的话需要编译安装)

### PHP 版本支持

某个PHP版本结束支持后，最多提供六个月的维护

## 安装

使用 [composer](https://getcomposer.org/) 来把 PhpSpreadsheet 安装进你的项目：

- [Composer安装教程](http://www.phpcomposer.com/composer-the-new-age-of-dependency-manager-for-php/)
- [Composer中国镜像加速](https://pkg.phpcomposer.com/)

```sh
composer require phpoffice/phpspreadsheet
```

**备注:** 如果你要使用没有发布或者不稳定的开发版本的话请使用
`phpoffice/phpspreadsheet:dev-develop` 命来来代替。

## 示例使用

尝试简单的使用PhpSpreadsheet写一个Xlsx文件

```php
<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'Hello World !');

$writer = new Xlsx($spreadsheet);
$writer->save('hello world.xlsx');
```

## 从示例中学习使用本库

A good way to get started is to run some of the samples. Serve the samples via
PHP built-in webserver:
刚起步学习的话最好运行一下我们的示例程序。 可以使用PHP內建服务器来启动一个服务
```sh
php -S localhost:8000 -t vendor/phpoffice/phpspreadsheet/samples
```

然后在浏览器中打开

> http://localhost:8000/

示例程序也可以直接在命令行中运行，比如

```sh
php vendor/phpoffice/phpspreadsheet/samples/01_Simple.php
```

当然啦，如果你安装了lnmp, lamp, wamp 这些，也可以根据你的路径在浏览器中直接访问。

## 从文档学习

要深入阅读文档，可以参考 [结构浏览](./topics/architecture.md),
[创建文档](./topics/creating-spreadsheet.md),
[工作表](./topics/worksheets.md),
[操作单元格](./topics/accessing-cells.md) 和
[读写文件](./topics/reading-and-writing-to-file.md).

# 感谢

 请参照最新的 [贡献者列表](https://github.com/PHPOffice/PhpSpreadsheet/graphs/contributors)

# 文档翻译
- [English 英文](../../index.md)
- [Chinese 中文](./i18n/zh_cn/index.md)
