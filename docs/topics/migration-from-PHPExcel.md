# Migration from PHPExcel

PhpSpreadsheet introduced many breaking changes by introducing
namespaces and renaming some classes. To help you migrate existing
project, a tool was written to replace all references to PHPExcel
classes to their new names. But there are also manual changes that
need to be done.

## Automated tool

[RectorPHP](https://github.com/rectorphp/rector) can be used to migrate
automatically your codebase. Assuming your files to be migrated lives
in `src/`, you can run the migration like so:

```sh
composer require rector/rector --dev
vendor/bin/rector process src --set phpexcel-to-phpspreadsheet
composer remove rector/rector
```

For more details, see
[RectorPHP blog post](https://getrector.org/blog/2020/04/16/how-to-migrate-from-phpexcel-to-phpspreadsheet-with-rector-in-30-minutes).
