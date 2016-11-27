# PhpSpreadsheet Developer Documentation

## Reading and writing to file

As you already know from part  REF _Ref191885438 \w \h 3.3  REF _Ref191885438 \h Readers and writers, reading and writing to a persisted storage is not possible using the base PhpSpreadsheet classes. For this purpose, PhpSpreadsheet provides readers and writers, which are implementations of PHPExcel_Writer_IReader and \PhpOffice\PhpSpreadsheet\Writer\IWriter.

### \PhpOffice\PhpSpreadsheet\IOFactory

The PhpSpreadsheet API offers multiple methods to create a PHPExcel_Writer_IReader or \PhpOffice\PhpSpreadsheet\Writer\IWriter instance:

Direct creation via \PhpOffice\PhpSpreadsheet\IOFactory.  All examples underneath demonstrate the direct creation method. Note that you can also use the \PhpOffice\PhpSpreadsheet\IOFactory class to do this.

#### Creating \PhpOffice\PhpSpreadsheet\Reader\IReader using \PhpOffice\PhpSpreadsheet\IOFactory

There are 2 methods for reading in a file into PhpSpreadsheet: using automatic file type resolving or explicitly.

Automatic file type resolving checks the different \PhpOffice\PhpSpreadsheet\Reader\IReader distributed with PhpSpreadsheet. If one of them can load the specified file name, the file is loaded using that \PhpOffice\PhpSpreadsheet\Reader\IReader. Explicit mode requires you to specify which \PhpOffice\PhpSpreadsheet\Reader\IReader should be used.

You can create a \PhpOffice\PhpSpreadsheet\Reader\IReader instance using \PhpOffice\PhpSpreadsheet\IOFactory in automatic file type resolving mode using the following code sample:

```php
$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("05featuredemo.xlsx");
```

A typical use of this feature is when you need to read files uploaded by your users, and you don’t know whether they are uploading xls or xlsx files.

If you need to set some properties on the reader, (e.g. to only read data, see more about this later), then you may instead want to use this variant:

```php
$objReader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile("05featuredemo.xlsx");
$objReader->setReadDataOnly(true);
$objReader->load("05featuredemo.xlsx");
```

You can create a \PhpOffice\PhpSpreadsheet\Reader\IReader instance using \PhpOffice\PhpSpreadsheet\IOFactory in explicit mode using the following code sample:

```php
$objReader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
$spreadsheet = $objReader->load("05featuredemo.xlsx");
```

Note that automatic type resolving mode is slightly slower than explicit mode.

#### Creating \PhpOffice\PhpSpreadsheet\Writer\IWriter using \PhpOffice\PhpSpreadsheet\IOFactory

You can create a PHPExcel_Writer_Iwriter instance using \PhpOffice\PhpSpreadsheet\IOFactory:

```php
$objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
$objWriter->save("05featuredemo.xlsx");
```

### Excel 2007 (SpreadsheetML) file format

Xlsx file format is the main file format of PhpSpreadsheet. It allows outputting the in-memory spreadsheet to a .xlsx file.

#### \PhpOffice\PhpSpreadsheet\Reader\Xlsx

##### Reading a spreadsheet

You can read an .xlsx file using the following code:

```php
$objReader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
$spreadsheet = $objReader->load("05featuredemo.xlsx");
```

##### Read data only

You can set the option setReadDataOnly on the reader, to instruct the reader to ignore styling, data validation, … and just read cell data:

```php
$objReader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
$objReader->setReadDataOnly(true);
$spreadsheet = $objReader->load("05featuredemo.xlsx");
```

##### Read specific sheets only

You can set the option setLoadSheetsOnly on the reader, to instruct the reader to only load the sheets with a given name:

```php
$objReader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
$objReader->setLoadSheetsOnly( array("Sheet 1", "My special sheet") );
$spreadsheet = $objReader->load("05featuredemo.xlsx");
```

##### Read specific cells only

You can set the option setReadFilter on the reader, to instruct the reader to only load the cells which match a given rule. A read filter can be any class which implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter. By default, all cells are read using the \PhpOffice\PhpSpreadsheet\Reader\DefaultReadFilter.

The following code will only read row 1 and rows 20 – 30 of any sheet in the Excel file:

```php
class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter {

    public function readCell($column, $row, $worksheetName = '') {
        // Read title row and rows 20 - 30
        if ($row == 1 || ($row >= 20 && $row <= 30)) {
            return true;
        }
        return false;
    }
}

$objReader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
$objReader->setReadFilter( new MyReadFilter() );
$spreadsheet = $objReader->load("06largescale.xlsx");
```

#### \PhpOffice\PhpSpreadsheet\Writer\Xlsx

##### Writing a spreadsheet

You can write an .xlsx file using the following code:

```php
$objWriter = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
$objWriter->save("05featuredemo.xlsx");
```

##### Formula pre-calculation

By default, this writer pre-calculates all formulas in the spreadsheet. This can be slow on large spreadsheets, and maybe even unwanted. You can however disable formula pre-calculation:

```php
$objWriter = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
$objWriter->setPreCalculateFormulas(false);
$objWriter->save("05featuredemo.xlsx");
```

##### Office 2003 compatibility pack

Because of a bug in the Office2003 compatibility pack, there can be some small issues when opening Xlsx spreadsheets (mostly related to formula calculation). You can enable Office2003 compatibility with the following code:

```
$objWriter = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
$objWriter->setOffice2003Compatibility(true);
$objWriter->save("05featuredemo.xlsx");
```

__Office2003 compatibility should only be used when needed__
Office2003 compatibility option should only be used when needed. This option disables several Office2007 file format options, resulting in a lower-featured Office2007 spreadsheet when this option is used.

### Excel 5 (BIFF) file format

Xls file format is the old Excel file format, implemented in PhpSpreadsheet to provide a uniform manner to create both .xlsx and .xls files. It is basically a modified version of [PEAR Spreadsheet_Excel_Writer][21], although it has been extended and has fewer limitations and more features than the old PEAR library. This can read all BIFF versions that use OLE2: BIFF5 (introduced with office 95) through BIFF8, but cannot read earlier versions.

Xls file format will not be developed any further, it just provides an additional file format for PhpSpreadsheet.

__Excel5 (BIFF) limitations__
Please note that BIFF file format has some limits regarding to styling cells and handling large spreadsheets via PHP.

#### \PhpOffice\PhpSpreadsheet\Reader\Xls

##### Reading a spreadsheet

You can read an .xls file using the following code:

```php
$objReader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
$spreadsheet = $objReader->load("05featuredemo.xls");
```

##### Read data only

You can set the option setReadDataOnly on the reader, to instruct the reader to ignore styling, data validation, … and just read cell data:

```php
$objReader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
$objReader->setReadDataOnly(true);
$spreadsheet = $objReader->load("05featuredemo.xls");
```

##### Read specific sheets only

You can set the option setLoadSheetsOnly on the reader, to instruct the reader to only load the sheets with a given name:

```php
$objReader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
$objReader->setLoadSheetsOnly( array("Sheet 1", "My special sheet") );
$spreadsheet = $objReader->load("05featuredemo.xls");
```

##### Read specific cells only

You can set the option setReadFilter on the reader, to instruct the reader to only load the cells which match a given rule. A read filter can be any class which implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter. By default, all cells are read using the \PhpOffice\PhpSpreadsheet\Reader\DefaultReadFilter.

The following code will only read row 1 and rows 20 to 30 of any sheet in the Excel file:

```php
class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter {

    public function readCell($column, $row, $worksheetName = '') {
        // Read title row and rows 20 - 30
        if ($row == 1 || ($row >= 20 && $row <= 30)) {
            return true;
        }
        return false;
    }
}

$objReader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
$objReader->setReadFilter( new MyReadFilter() );
$spreadsheet = $objReader->load("06largescale.xls");
```

#### \PhpOffice\PhpSpreadsheet\Writer\Xls

##### Writing a spreadsheet

You can write an .xls file using the following code:

```php
$objWriter = new \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);
$objWriter->save("05featuredemo.xls");
```

### Excel 2003 XML file format

Excel 2003 XML file format is a file format which can be used in older versions of Microsoft Excel.

__Excel 2003 XML limitations__
Please note that Excel 2003 XML format has some limits regarding to styling cells and handling large spreadsheets via PHP.

#### \PhpOffice\PhpSpreadsheet\Reader\Excel2003XML

##### Reading a spreadsheet

You can read an Excel 2003 .xml file using the following code:

```php
$objReader = new \PhpOffice\PhpSpreadsheet\Reader\Excel2003XML();
$spreadsheet = $objReader->load("05featuredemo.xml");
```

##### Read specific cells only

You can set the option setReadFilter on the reader, to instruct the reader to only load the cells which match a given rule. A read filter can be any class which implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter. By default, all cells are read using the \PhpOffice\PhpSpreadsheet\Reader\DefaultReadFilter.

The following code will only read row 1 and rows 20 to 30 of any sheet in the Excel file:

```php
class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter {

    public function readCell($column, $row, $worksheetName = '') {
        // Read title row and rows 20 - 30
        if ($row == 1 || ($row >= 20 && $row <= 30)) {
            return true;
        }
        return false;
    }

}

$objReader = new \PhpOffice\PhpSpreadsheet\Reader\Excel2003XML();
$objReader->setReadFilter( new MyReadFilter() );
$spreadsheet = $objReader->load("06largescale.xml");
```

### Symbolic LinK (SYLK)

Symbolic Link (SYLK) is a Microsoft file format typically used to exchange data between applications, specifically spreadsheets. SYLK files conventionally have a .slk suffix. Composed of only displayable ANSI characters, it can be easily created and processed by other applications, such as databases.

__SYLK limitations__
Please note that SYLK file format has some limits regarding to styling cells and handling large spreadsheets via PHP.

#### \PhpOffice\PhpSpreadsheet\Reader\SYLK

##### Reading a spreadsheet

You can read an .slk file using the following code:

```php
$objReader = new \PhpOffice\PhpSpreadsheet\Reader\SYLK();
$spreadsheet = $objReader->load("05featuredemo.slk");
```

##### Read specific cells only

You can set the option setReadFilter on the reader, to instruct the reader to only load the cells which match a given rule. A read filter can be any class which implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter. By default, all cells are read using the \PhpOffice\PhpSpreadsheet\Reader\DefaultReadFilter.

The following code will only read row 1 and rows 20 to 30 of any sheet in the SYLK file:

```php
class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter {

    public function readCell($column, $row, $worksheetName = '') {
        // Read title row and rows 20 - 30
        if ($row == 1 || ($row >= 20 && $row <= 30)) {
            return true;
        }
        return false;
    }

}

$objReader = new \PhpOffice\PhpSpreadsheet\Reader\SYLK();
$objReader->setReadFilter( new MyReadFilter() );
$spreadsheet = $objReader->load("06largescale.slk");
```

### Open/Libre Office (.ods)

Open Office or Libre Office .ods files are the standard file format for Open Office or Libre Office Calc files.

#### \PhpOffice\PhpSpreadsheet\Reader\Ods

##### Reading a spreadsheet

You can read an .ods file using the following code:

```php
$objReader = new \PhpOffice\PhpSpreadsheet\Reader\Ods();
$spreadsheet = $objReader->load("05featuredemo.ods");
```

##### Read specific cells only

You can set the option setReadFilter on the reader, to instruct the reader to only load the cells which match a given rule. A read filter can be any class which implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter. By default, all cells are read using the \PhpOffice\PhpSpreadsheet\Reader\DefaultReadFilter.

The following code will only read row 1 and rows 20 to 30 of any sheet in the Calc file:

```php
class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter {

    public function readCell($column, $row, $worksheetName = '') {
        // Read title row and rows 20 - 30
        if ($row == 1 || ($row >= 20 && $row <= 30)) {
            return true;
        }
        return false;
    }

}

$objReader = new PHPExcel_Reader_OOcalc();
$objReader->setReadFilter( new MyReadFilter() );
$spreadsheet = $objReader->load("06largescale.ods");
```

### CSV (Comma Separated Values)

CSV (Comma Separated Values) are often used as an import/export file format with other systems. PhpSpreadsheet allows reading and writing to CSV files.

__CSV limitations__
Please note that CSV file format has some limits regarding to styling cells, number formatting, ...

#### \PhpOffice\PhpSpreadsheet\Reader\CSV

##### Reading a CSV file

You can read a .csv file using the following code:

```php
$objReader = new \PhpOffice\PhpSpreadsheet\Reader\CSV();
$spreadsheet = $objReader->load("sample.csv");
```

##### Setting CSV options

Often, CSV files are not really “comma separated”, or use semicolon (;) as a separator. You can instruct \PhpOffice\PhpSpreadsheet\Reader\CSV some options before reading a CSV file.

Note that \PhpOffice\PhpSpreadsheet\Reader\CSV by default assumes that the loaded CSV file is UTF-8 encoded. If you are reading CSV files that were created in Microsoft Office Excel the correct input encoding may rather be Windows-1252 (CP1252). Always make sure that the input encoding is set appropriately.

```php
$objReader = new \PhpOffice\PhpSpreadsheet\Reader\CSV();
$objReader->setInputEncoding('CP1252');
$objReader->setDelimiter(';');
$objReader->setEnclosure('');
$objReader->setLineEnding("\r\n");
$objReader->setSheetIndex(0);

$spreadsheet = $objReader->load("sample.csv");
```

##### Read a specific worksheet

CSV files can only contain one worksheet. Therefore, you can specify which sheet to read from CSV:

```php
$objReader->setSheetIndex(0);
```

##### Read into existing spreadsheet

When working with CSV files, it might occur that you want to import CSV data into an existing `Spreadsheet` object. The following code loads a CSV file into an existing $spreadsheet containing some sheets, and imports onto the 6th sheet:

```php
$objReader = new \PhpOffice\PhpSpreadsheet\Reader\CSV();
$objReader->setDelimiter(';');
$objReader->setEnclosure('');
$objReader->setLineEnding("\r\n");
$objReader->setSheetIndex(5);

$objReader->loadIntoExisting("05featuredemo.csv", $spreadsheet);
```

#### \PhpOffice\PhpSpreadsheet\Writer\CSV

##### Writing a CSV file

You can write a .csv file using the following code:

```php
$objWriter = new \PhpOffice\PhpSpreadsheet\Writer\CSV($spreadsheet);
$objWriter->save("05featuredemo.csv");
```

##### Setting CSV options

Often, CSV files are not really “comma separated”, or use semicolon (;) as a separator. You can instruct \PhpOffice\PhpSpreadsheet\Writer\CSV some options before writing a CSV file:

```php
$objWriter = new \PhpOffice\PhpSpreadsheet\Writer\CSV($spreadsheet);
$objWriter->setDelimiter(';');
$objWriter->setEnclosure('');
$objWriter->setLineEnding("\r\n");
$objWriter->setSheetIndex(0);

$objWriter->save("05featuredemo.csv");
```

##### Write a specific worksheet

CSV files can only contain one worksheet. Therefore, you can specify which sheet to write to CSV:

```php
$objWriter->setSheetIndex(0);
```

##### Formula pre-calculation

By default, this writer pre-calculates all formulas in the spreadsheet. This can be slow on large spreadsheets, and maybe even unwanted. You can however disable formula pre-calculation:

```php
$objWriter = new \PhpOffice\PhpSpreadsheet\Writer\CSV($spreadsheet);
$objWriter->setPreCalculateFormulas(false);
$objWriter->save("05featuredemo.csv");
```

##### Writing UTF-8 CSV files

A CSV file can be marked as UTF-8 by writing a BOM file header. This can be enabled by using the following code:

```php
$objWriter = new \PhpOffice\PhpSpreadsheet\Writer\CSV($spreadsheet);
$objWriter->setUseBOM(true);
$objWriter->save("05featuredemo.csv");
```

##### Decimal and thousands separators

If the worksheet you are exporting contains numbers with decimal or thousands separators then you should think about what characters you want to use for those before doing the export.

By default PhpSpreadsheet looks up in the server's locale settings to decide what characters to use. But to avoid problems it is recommended to set the characters explicitly as shown below.

English users will want to use this before doing the export:

```php
\PhpOffice\PhpSpreadsheet\Shared\StringHelper::setDecimalSeparator('.');
\PhpOffice\PhpSpreadsheet\Shared\StringHelper::setThousandsSeparator(',');
```

German users will want to use the opposite values.

```php
\PhpOffice\PhpSpreadsheet\Shared\StringHelper::setDecimalSeparator(',');
\PhpOffice\PhpSpreadsheet\Shared\StringHelper::setThousandsSeparator('.');
```

Note that the above code sets decimal and thousand separators as global options. This also affects how HTML and PDF is exported.

### HTML

PhpSpreadsheet allows you to read or write a spreadsheet as HTML format, for quick representation of the data in it to anyone who does not have a spreadsheet application on their PC, or loading files saved by other scripts that simply create HTML markup and give it a .xls file extension.

__HTML limitations__
Please note that HTML file format has some limits regarding to styling cells, number formatting, ...

#### \PhpOffice\PhpSpreadsheet\Reader\HTML

##### Reading a spreadsheet

You can read an .html or .htm file using the following code:

```php
$objReader = new \PhpOffice\PhpSpreadsheet\Reader\HTML();

$spreadsheet = $objReader->load("05featuredemo.html");
```

__HTML limitations__
Please note that HTML reader is still experimental and does not yet support merged cells or nested tables cleanly

#### \PhpOffice\PhpSpreadsheet\Writer\HTML

Please note that \PhpOffice\PhpSpreadsheet\Writer\HTML only outputs the first worksheet by default.

##### Writing a spreadsheet

You can write a .htm file using the following code:

```php
$objWriter = new \PhpOffice\PhpSpreadsheet\Writer\HTML($spreadsheet);

$objWriter->save("05featuredemo.htm");
```

##### Write all worksheets

HTML files can contain one or more worksheets. If you want to write all sheets into a single HTML file, use the following code:

```php
$objWriter->writeAllSheets();
```

##### Write a specific worksheet

HTML files can contain one or more worksheets. Therefore, you can specify which sheet to write to HTML:

```php
$objWriter->setSheetIndex(0);
```

##### Setting the images root of the HTML file

There might be situations where you want to explicitly set the included images root. For example, one might want to see
```html
<img style="position: relative; left: 0px; top: 0px; width: 140px; height: 78px;" src="http://www.domain.com/*images/logo.jpg" border="0">
```

instead of

```html
<img style="position: relative; left: 0px; top: 0px; width: 140px; height: 78px;" src="./images/logo.jpg" border="0">.
```

You can use the following code to achieve this result:

```php
$objWriter->setImagesRoot('http://www.example.com');
```

##### Formula pre-calculation

By default, this writer pre-calculates all formulas in the spreadsheet. This can be slow on large spreadsheets, and maybe even unwanted. You can however disable formula pre-calculation:

```php
$objWriter = new \PhpOffice\PhpSpreadsheet\Writer\HTML($spreadsheet);
$objWriter->setPreCalculateFormulas(false);

$objWriter->save("05featuredemo.htm");
```

##### Embedding generated HTML in a web page

There might be a situation where you want to embed the generated HTML in an existing website. \PhpOffice\PhpSpreadsheet\Writer\HTML provides support to generate only specific parts of the HTML code, which allows you to use these parts in your website.

Supported methods:

 - generateHTMLHeader()
 - generateStyles()
 - generateSheetData()
 - generateHTMLFooter()

Here's an example which retrieves all parts independently and merges them into a resulting HTML page:

```php
<?php
$objWriter = new \PhpOffice\PhpSpreadsheet\Writer\HTML($spreadsheet);
echo $objWriter->generateHTMLHeader();
?>

<style>
<!--
html {
    font-family: Times New Roman;
    font-size: 9pt;
    background-color: white;
}

<?php
echo $objWriter->generateStyles(false); // do not write <style> and </style>
?>

-->
</style>

<?php
echo $objWriter->generateSheetData();
echo $objWriter->generateHTMLFooter();
?>
```

##### Writing UTF-8 HTML files

A HTML file can be marked as UTF-8 by writing a BOM file header. This can be enabled by using the following code:

```php
$objWriter = new \PhpOffice\PhpSpreadsheet\Writer\HTML($spreadsheet);
$objWriter->setUseBOM(true);

$objWriter->save("05featuredemo.htm");
```

##### Decimal and thousands separators

See section \PhpOffice\PhpSpreadsheet\Writer\CSV how to control the appearance of these.

### PDF

PhpSpreadsheet allows you to write a spreadsheet into PDF format, for fast distribution of represented data.

__PDF limitations__
Please note that PDF file format has some limits regarding to styling cells, number formatting, ...

#### \PhpOffice\PhpSpreadsheet\Writer\PDF

PhpSpreadsheet’s PDF Writer is a wrapper for a 3rd-Party PDF Rendering library such as tcPDF, mPDF or DomPDF. You must now install a PDF Rendering library yourself; but PhpSpreadsheet will work with a number of different libraries.

Currently, the following libraries are supported:

Library | Version used for testing | Downloadable from                | PhpSpreadsheet Internal Constant
--------|--------------------------|----------------------------------|----------------------------
tcPDF   | 5.9                      | http://www.tcpdf.org/            | PDF_RENDERER_TCPDF
mPDF    | 5.4                      | http://www.mpdf1.com/mpdf/       | PDF_RENDERER_MPDF
domPDF  | 0.6.0 beta 3             | http://code.google.com/p/dompdf/ | PDF_RENDERER_DOMPDF

The different libraries have different strengths and weaknesses. Some generate better formatted output than others, some are faster or use less memory than others, while some generate smaller .pdf files. It is the developers choice which one they wish to use, appropriate to their own circumstances.

Before instantiating a Writer to generate PDF output, you will need to indicate which Rendering library you are using, and where it is located.

```php
$rendererName = \PhpOffice\PhpSpreadsheet\Settings::PDF_RENDERER_MPDF;
$rendererLibrary = 'mPDF5.4';
$rendererLibraryPath = dirname(__FILE__).'/../../../libraries/PDF/' . $rendererLibrary;

if (!\PhpOffice\PhpSpreadsheet\Settings::setPdfRenderer(
    $rendererName,
    $rendererLibraryPath
    )) {
    die(
        'Please set the $rendererName and $rendererLibraryPath values' .
        PHP_EOL .
        ' as appropriate for your directory structure'
    );
}
```

##### Writing a spreadsheet

Once you have identified the Renderer that you wish to use for PDF generation, you can write a .pdf file using the following code:

```php
$objWriter = new \PhpOffice\PhpSpreadsheet\Writer\PDF($spreadsheet);
$objWriter->save("05featuredemo.pdf");
```

Please note that \PhpOffice\PhpSpreadsheet\Writer\PDF only outputs the first worksheet by default.

##### Write all worksheets

PDF files can contain one or more worksheets. If you want to write all sheets into a single PDF file, use the following code:

```php
$objWriter->writeAllSheets();
```

##### Write a specific worksheet

PDF files can contain one or more worksheets. Therefore, you can specify which sheet to write to PDF:

```php
$objWriter->setSheetIndex(0);
```

##### Formula pre-calculation

By default, this writer pre-calculates all formulas in the spreadsheet. This can be slow on large spreadsheets, and maybe even unwanted. You can however disable formula pre-calculation:

```php
$objWriter = new \PhpOffice\PhpSpreadsheet\Writer\PDF($spreadsheet);
$objWriter->setPreCalculateFormulas(false);

$objWriter->save("05featuredemo.pdf");
```

##### Decimal and thousands separators

See section \PhpOffice\PhpSpreadsheet\Writer\CSV how to control the appearance of these.

### Generating Excel files from templates (read, modify, write)

Readers and writers are the tools that allow you to generate Excel files from templates. This requires less coding effort than generating the Excel file from scratch, especially if your template has many styles, page setup properties, headers etc.

Here is an example how to open a template file, fill in a couple of fields and save it again:

```php
$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('template.xlsx');

$objWorksheet = $spreadsheet->getActiveSheet();

$objWorksheet->getCell('A1')->setValue('John');
$objWorksheet->getCell('A2')->setValue('Smith');

$objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
$objWriter->save('write.xls');
```

Notice that it is ok to load an xlsx file and generate an xls file.

  [21]: http://pear.php.net/package/Spreadsheet_Excel_Writer
  [22]: http://www.codeplex.com/PHPExcel/Wiki/View.aspx?title=Credits&referringTitle=Home
