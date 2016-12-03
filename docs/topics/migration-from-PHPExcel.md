# Migration from PHPExcel

PhpSpreadsheet introduced many breaking changes by introducing
namespaces and renaming some classes. To help you migrate existing
project, a tool was written to replace all references to PHPExcel
classes to their new names.

The tool is included in PhpSpreadsheet. It scans recursively all files
and directories, starting from the current directory. Assuming it was
installed with composer, it can be run like so:

``` sh
cd /project/to/migrate/src
/project/to/migrate/vendor/phpoffice/phpspreadsheet/bin/migrate-from-phpexcel
```

**Important** The tool will irreversibly modify your sources, be sure to
backup everything, and double check the result before committing.
