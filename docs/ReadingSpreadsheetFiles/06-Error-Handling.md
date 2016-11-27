# PhpSpreadsheet User Documentation – Reading Spreadsheet Files

## Error Handling

Of course, you should always apply some error handling to your scripts as well. PhpSpreadsheet throws exceptions, so you can wrap all your code that accesses the library methods within Try/Catch blocks to trap for any problems that are encountered, and deal with them in an appropriate manner.

The PhpSpreadsheet Readers throw a \PhpOffice\PhpSpreadsheet\Reader\Exception.

```php
$inputFileName = './sampleData/example-1.xls';

try {
    /** Load $inputFileName to a Spreadsheet Object  **/
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
} catch(\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
    die('Error loading file: '.$e->getMessage());
}
```
 > See Examples/Reader/exampleReader16.php for a working example of this code.

