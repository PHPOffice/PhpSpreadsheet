# PhpSpreadsheet Developer Documentation

## Clearing a Workbook from memory

The PhpSpreadsheet object contains cyclic references (e.g. the workbook is linked to the worksheets, and the worksheets are linked to their parent workbook) which cause problems when PHP tries to clear the objects from memory when they are unset(), or at the end of a function when they are in local scope. The result of this is "memory leaks", which can easily use a large amount of PHP's limited memory.

This can only be resolved manually: if you need to unset a workbook, then you also need to "break" these cyclic references before doing so. PhpSpreadsheet provides the disconnectWorksheets() method for this purpose.

```php
$spreadsheet->disconnectWorksheets();

unset($spreadsheet);
```
