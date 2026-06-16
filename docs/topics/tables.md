# Tables

## Introduction

To make managing and analyzing a group of related data easier, you can turn a range of cells into an Excel table (previously known as an Excel list).

## Support

Currently tables are supported in Xlsx reader and Html Writer

To enable table formatting for Html writer, use:

```php
        $writer = new HtmlWriter($spreadsheet);
        $writer->setConditionalFormatting(true);
```