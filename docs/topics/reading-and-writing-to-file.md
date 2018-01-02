# Reading and writing to file

As you already know from the [architecture](./architecture.md#readers-and-writers),
reading and writing to a
persisted storage is not possible using the base PhpSpreadsheet classes.
For this purpose, PhpSpreadsheet provides readers and writers, which are
implementations of `\PhpOffice\PhpSpreadsheet\Reader\IReader` and
`\PhpOffice\PhpSpreadsheet\Writer\IWriter`.

## \PhpOffice\PhpSpreadsheet\IOFactory

The PhpSpreadsheet API offers multiple methods to create a
`\PhpOffice\PhpSpreadsheet\Reader\IReader` or
`\PhpOffice\PhpSpreadsheet\Writer\IWriter` instance:

Direct creation via `\PhpOffice\PhpSpreadsheet\IOFactory`. All examples
underneath demonstrate the direct creation method. Note that you can
also use the `\PhpOffice\PhpSpreadsheet\IOFactory` class to do this.

### Creating `\PhpOffice\PhpSpreadsheet\Reader\IReader` using `\PhpOffice\PhpSpreadsheet\IOFactory`

There are 2 methods for reading in a file into PhpSpreadsheet: using
automatic file type resolving or explicitly.

Automatic file type resolving checks the different
`\PhpOffice\PhpSpreadsheet\Reader\IReader` distributed with
PhpSpreadsheet. If one of them can load the specified file name, the
file is loaded using that `\PhpOffice\PhpSpreadsheet\Reader\IReader`.
Explicit mode requires you to specify which
`\PhpOffice\PhpSpreadsheet\Reader\IReader` should be used.

You can create a `\PhpOffice\PhpSpreadsheet\Reader\IReader` instance using
`\PhpOffice\PhpSpreadsheet\IOFactory` in automatic file type resolving
mode using the following code sample:

``` php
$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("05featuredemo.xlsx");
```

A typical use of this feature is when you need to read files uploaded by
your users, and you don’t know whether they are uploading xls or xlsx
files.

If you need to set some properties on the reader, (e.g. to only read
data, see more about this later), then you may instead want to use this
variant:

``` php
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile("05featuredemo.xlsx");
$reader->setReadDataOnly(true);
$reader->load("05featuredemo.xlsx");
```

You can create a `\PhpOffice\PhpSpreadsheet\Reader\IReader` instance using
`\PhpOffice\PhpSpreadsheet\IOFactory` in explicit mode using the following
code sample:

``` php
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
$spreadsheet = $reader->load("05featuredemo.xlsx");
```

Note that automatic type resolving mode is slightly slower than explicit
mode.

### Creating `\PhpOffice\PhpSpreadsheet\Writer\IWriter` using `\PhpOffice\PhpSpreadsheet\IOFactory`

You can create a `\PhpOffice\PhpSpreadsheet\Writer\IWriter` instance using
`\PhpOffice\PhpSpreadsheet\IOFactory`:

``` php
$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
$writer->save("05featuredemo.xlsx");
```

## Excel 2007 (SpreadsheetML) file format

Xlsx file format is the main file format of PhpSpreadsheet. It allows
outputting the in-memory spreadsheet to a .xlsx file.

### \PhpOffice\PhpSpreadsheet\Reader\Xlsx

#### Reading a spreadsheet

You can read an .xlsx file using the following code:

``` php
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
$spreadsheet = $reader->load("05featuredemo.xlsx");
```

#### Read data only

You can set the option setReadDataOnly on the reader, to instruct the
reader to ignore styling, data validation, … and just read cell data:

``` php
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
$reader->setReadDataOnly(true);
$spreadsheet = $reader->load("05featuredemo.xlsx");
```

#### Read specific sheets only

You can set the option setLoadSheetsOnly on the reader, to instruct the
reader to only load the sheets with a given name:

``` php
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
$reader->setLoadSheetsOnly( array("Sheet 1", "My special sheet") );
$spreadsheet = $reader->load("05featuredemo.xlsx");
```

#### Read specific cells only

You can set the option setReadFilter on the reader, to instruct the
reader to only load the cells which match a given rule. A read filter
can be any class which implements
`\PhpOffice\PhpSpreadsheet\Reader\IReadFilter`. By default, all cells are
read using the `\PhpOffice\PhpSpreadsheet\Reader\DefaultReadFilter`.

The following code will only read row 1 and rows 20 – 30 of any sheet in
the Excel file:

``` php
class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter {

    public function readCell($column, $row, $worksheetName = '') {
        // Read title row and rows 20 - 30
        if ($row == 1 || ($row >= 20 && $row <= 30)) {
            return true;
        }
        return false;
    }
}

$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
$reader->setReadFilter( new MyReadFilter() );
$spreadsheet = $reader->load("06largescale.xlsx");
```

### \PhpOffice\PhpSpreadsheet\Writer\Xlsx

#### Writing a spreadsheet

You can write an .xlsx file using the following code:

``` php
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
$writer->save("05featuredemo.xlsx");
```

#### Formula pre-calculation

By default, this writer pre-calculates all formulas in the spreadsheet.
This can be slow on large spreadsheets, and maybe even unwanted. You can
however disable formula pre-calculation:

``` php
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
$writer->setPreCalculateFormulas(false);
$writer->save("05featuredemo.xlsx");
```

#### Office 2003 compatibility pack

Because of a bug in the Office2003 compatibility pack, there can be some
small issues when opening Xlsx spreadsheets (mostly related to formula
calculation). You can enable Office2003 compatibility with the following
code:

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->setOffice2003Compatibility(true);
    $writer->save("05featuredemo.xlsx");

**Office2003 compatibility should only be used when needed** Office2003
compatibility option should only be used when needed. This option
disables several Office2007 file format options, resulting in a
lower-featured Office2007 spreadsheet when this option is used.

## Excel 5 (BIFF) file format

Xls file format is the old Excel file format, implemented in
PhpSpreadsheet to provide a uniform manner to create both .xlsx and .xls
files. It is basically a modified version of [PEAR
Spreadsheet\_Excel\_Writer](http://pear.php.net/package/Spreadsheet_Excel_Writer),
although it has been extended and has fewer limitations and more
features than the old PEAR library. This can read all BIFF versions that
use OLE2: BIFF5 (introduced with office 95) through BIFF8, but cannot
read earlier versions.

Xls file format will not be developed any further, it just provides an
additional file format for PhpSpreadsheet.

**Excel5 (BIFF) limitations** Please note that BIFF file format has some
limits regarding to styling cells and handling large spreadsheets via
PHP.

### \PhpOffice\PhpSpreadsheet\Reader\Xls

#### Reading a spreadsheet

You can read an .xls file using the following code:

``` php
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
$spreadsheet = $reader->load("05featuredemo.xls");
```

#### Read data only

You can set the option setReadDataOnly on the reader, to instruct the
reader to ignore styling, data validation, … and just read cell data:

``` php
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
$reader->setReadDataOnly(true);
$spreadsheet = $reader->load("05featuredemo.xls");
```

#### Read specific sheets only

You can set the option setLoadSheetsOnly on the reader, to instruct the
reader to only load the sheets with a given name:

``` php
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
$reader->setLoadSheetsOnly( array("Sheet 1", "My special sheet") );
$spreadsheet = $reader->load("05featuredemo.xls");
```

#### Read specific cells only

You can set the option setReadFilter on the reader, to instruct the
reader to only load the cells which match a given rule. A read filter
can be any class which implements
`\PhpOffice\PhpSpreadsheet\Reader\IReadFilter`. By default, all cells are
read using the `\PhpOffice\PhpSpreadsheet\Reader\DefaultReadFilter`.

The following code will only read row 1 and rows 20 to 30 of any sheet
in the Excel file:

``` php
class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter {

    public function readCell($column, $row, $worksheetName = '') {
        // Read title row and rows 20 - 30
        if ($row == 1 || ($row >= 20 && $row <= 30)) {
            return true;
        }
        return false;
    }
}

$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
$reader->setReadFilter( new MyReadFilter() );
$spreadsheet = $reader->load("06largescale.xls");
```

### \PhpOffice\PhpSpreadsheet\Writer\Xls

#### Writing a spreadsheet

You can write an .xls file using the following code:

``` php
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);
$writer->save("05featuredemo.xls");
```

## Excel 2003 XML file format

Excel 2003 XML file format is a file format which can be used in older
versions of Microsoft Excel.

**Excel 2003 XML limitations** Please note that Excel 2003 XML format
has some limits regarding to styling cells and handling large
spreadsheets via PHP.

### \PhpOffice\PhpSpreadsheet\Reader\Xml

#### Reading a spreadsheet

You can read an Excel 2003 .xml file using the following code:

``` php
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xml();
$spreadsheet = $reader->load("05featuredemo.xml");
```

#### Read specific cells only

You can set the option setReadFilter on the reader, to instruct the
reader to only load the cells which match a given rule. A read filter
can be any class which implements
`\PhpOffice\PhpSpreadsheet\Reader\IReadFilter`. By default, all cells are
read using the `\PhpOffice\PhpSpreadsheet\Reader\DefaultReadFilter`.

The following code will only read row 1 and rows 20 to 30 of any sheet
in the Excel file:

``` php
class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter {

    public function readCell($column, $row, $worksheetName = '') {
        // Read title row and rows 20 - 30
        if ($row == 1 || ($row >= 20 && $row <= 30)) {
            return true;
        }
        return false;
    }

}

$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xml();
$reader->setReadFilter( new MyReadFilter() );
$spreadsheet = $reader->load("06largescale.xml");
```

## Symbolic LinK (SYLK)

Symbolic Link (SYLK) is a Microsoft file format typically used to
exchange data between applications, specifically spreadsheets. SYLK
files conventionally have a .slk suffix. Composed of only displayable
ANSI characters, it can be easily created and processed by other
applications, such as databases.

**SYLK limitations** Please note that SYLK file format has some limits
regarding to styling cells and handling large spreadsheets via PHP.

### \PhpOffice\PhpSpreadsheet\Reader\Slk

#### Reading a spreadsheet

You can read an .slk file using the following code:

``` php
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Slk();
$spreadsheet = $reader->load("05featuredemo.slk");
```

#### Read specific cells only

You can set the option setReadFilter on the reader, to instruct the
reader to only load the cells which match a given rule. A read filter
can be any class which implements
`\PhpOffice\PhpSpreadsheet\Reader\IReadFilter`. By default, all cells are
read using the `\PhpOffice\PhpSpreadsheet\Reader\DefaultReadFilter`.

The following code will only read row 1 and rows 20 to 30 of any sheet
in the SYLK file:

``` php
class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter {

    public function readCell($column, $row, $worksheetName = '') {
        // Read title row and rows 20 - 30
        if ($row == 1 || ($row >= 20 && $row <= 30)) {
            return true;
        }
        return false;
    }

}

$reader = new \PhpOffice\PhpSpreadsheet\Reader\Slk();
$reader->setReadFilter( new MyReadFilter() );
$spreadsheet = $reader->load("06largescale.slk");
```

## Open/Libre Office (.ods)

Open Office or Libre Office .ods files are the standard file format for
Open Office or Libre Office Calc files.

### \PhpOffice\PhpSpreadsheet\Reader\Ods

#### Reading a spreadsheet

You can read an .ods file using the following code:

``` php
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Ods();
$spreadsheet = $reader->load("05featuredemo.ods");
```

#### Read specific cells only

You can set the option setReadFilter on the reader, to instruct the
reader to only load the cells which match a given rule. A read filter
can be any class which implements
`\PhpOffice\PhpSpreadsheet\Reader\IReadFilter`. By default, all cells are
read using the `\PhpOffice\PhpSpreadsheet\Reader\DefaultReadFilter`.

The following code will only read row 1 and rows 20 to 30 of any sheet
in the Calc file:

``` php
class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter {

    public function readCell($column, $row, $worksheetName = '') {
        // Read title row and rows 20 - 30
        if ($row == 1 || ($row >= 20 && $row <= 30)) {
            return true;
        }
        return false;
    }

}

$reader = new PhpOffice\PhpSpreadsheet\Reader\Ods();
$reader->setReadFilter( new MyReadFilter() );
$spreadsheet = $reader->load("06largescale.ods");
```

## CSV (Comma Separated Values)

CSV (Comma Separated Values) are often used as an import/export file
format with other systems. PhpSpreadsheet allows reading and writing to
CSV files.

**CSV limitations** Please note that CSV file format has some limits
regarding to styling cells, number formatting, ...

### \PhpOffice\PhpSpreadsheet\Reader\Csv

#### Reading a CSV file

You can read a .csv file using the following code:

``` php
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
$spreadsheet = $reader->load("sample.csv");
```

#### Setting CSV options

Often, CSV files are not really "comma separated", or use semicolon (`;`)
as a separator. You can instruct
`\PhpOffice\PhpSpreadsheet\Reader\Csv` some options before reading a CSV
file.

The separator will be auto-detected, so in most cases it should not be necessary
to specify it. But in cases where auto-detection does not fit the use-case, then
it can be set manually.

Note that `\PhpOffice\PhpSpreadsheet\Reader\Csv` by default assumes that
the loaded CSV file is UTF-8 encoded. If you are reading CSV files that
were created in Microsoft Office Excel the correct input encoding may
rather be Windows-1252 (CP1252). Always make sure that the input
encoding is set appropriately.

``` php
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
$reader->setInputEncoding('CP1252');
$reader->setDelimiter(';');
$reader->setEnclosure('');
$reader->setSheetIndex(0);

$spreadsheet = $reader->load("sample.csv");
```

#### Read a specific worksheet

CSV files can only contain one worksheet. Therefore, you can specify
which sheet to read from CSV:

``` php
$reader->setSheetIndex(0);
```

#### Read into existing spreadsheet

When working with CSV files, it might occur that you want to import CSV
data into an existing `Spreadsheet` object. The following code loads a
CSV file into an existing `$spreadsheet` containing some sheets, and
imports onto the 6th sheet:

``` php
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
$reader->setDelimiter(';');
$reader->setEnclosure('');
$reader->setSheetIndex(5);

$reader->loadIntoExisting("05featuredemo.csv", $spreadsheet);
```

### \PhpOffice\PhpSpreadsheet\Writer\Csv

#### Writing a CSV file

You can write a .csv file using the following code:

``` php
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
$writer->save("05featuredemo.csv");
```

#### Setting CSV options

Often, CSV files are not really "comma separated", or use semicolon (`;`)
as a separator. You can instruct
`\PhpOffice\PhpSpreadsheet\Writer\Csv` some options before writing a CSV
file:

``` php
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
$writer->setDelimiter(';');
$writer->setEnclosure('');
$writer->setLineEnding("\r\n");
$writer->setSheetIndex(0);

$writer->save("05featuredemo.csv");
```

#### Write a specific worksheet

CSV files can only contain one worksheet. Therefore, you can specify
which sheet to write to CSV:

``` php
$writer->setSheetIndex(0);
```

#### Formula pre-calculation

By default, this writer pre-calculates all formulas in the spreadsheet.
This can be slow on large spreadsheets, and maybe even unwanted. You can
however disable formula pre-calculation:

``` php
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
$writer->setPreCalculateFormulas(false);
$writer->save("05featuredemo.csv");
```

#### Writing UTF-8 CSV files

A CSV file can be marked as UTF-8 by writing a BOM file header. This can
be enabled by using the following code:

``` php
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
$writer->setUseBOM(true);
$writer->save("05featuredemo.csv");
```

#### Decimal and thousands separators

If the worksheet you are exporting contains numbers with decimal or
thousands separators then you should think about what characters you
want to use for those before doing the export.

By default PhpSpreadsheet looks up in the server's locale settings to
decide what characters to use. But to avoid problems it is recommended
to set the characters explicitly as shown below.

English users will want to use this before doing the export:

``` php
\PhpOffice\PhpSpreadsheet\Shared\StringHelper::setDecimalSeparator('.');
\PhpOffice\PhpSpreadsheet\Shared\StringHelper::setThousandsSeparator(',');
```

German users will want to use the opposite values.

``` php
\PhpOffice\PhpSpreadsheet\Shared\StringHelper::setDecimalSeparator(',');
\PhpOffice\PhpSpreadsheet\Shared\StringHelper::setThousandsSeparator('.');
```

Note that the above code sets decimal and thousand separators as global
options. This also affects how HTML and PDF is exported.

## HTML

PhpSpreadsheet allows you to read or write a spreadsheet as HTML format,
for quick representation of the data in it to anyone who does not have a
spreadsheet application on their PC, or loading files saved by other
scripts that simply create HTML markup and give it a .xls file
extension.

**HTML limitations** Please note that HTML file format has some limits
regarding to styling cells, number formatting, ...

### \PhpOffice\PhpSpreadsheet\Reader\Html

#### Reading a spreadsheet

You can read an .html or .htm file using the following code:

``` php
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();

$spreadsheet = $reader->load("05featuredemo.html");
```

**HTML limitations** Please note that HTML reader is still experimental
and does not yet support merged cells or nested tables cleanly

### \PhpOffice\PhpSpreadsheet\Writer\Html

Please note that `\PhpOffice\PhpSpreadsheet\Writer\Html` only outputs the
first worksheet by default.

#### Writing a spreadsheet

You can write a .htm file using the following code:

``` php
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($spreadsheet);

$writer->save("05featuredemo.htm");
```

#### Write all worksheets

HTML files can contain one or more worksheets. If you want to write all
sheets into a single HTML file, use the following code:

``` php
$writer->writeAllSheets();
```

#### Write a specific worksheet

HTML files can contain one or more worksheets. Therefore, you can
specify which sheet to write to HTML:

``` php
$writer->setSheetIndex(0);
```

#### Setting the images root of the HTML file

There might be situations where you want to explicitly set the included
images root. For example, one might want to see

``` html
<img style="position: relative; left: 0px; top: 0px; width: 140px; height: 78px;" src="http://www.domain.com/*images/logo.jpg" border="0">
```

instead of

``` html
<img style="position: relative; left: 0px; top: 0px; width: 140px; height: 78px;" src="./images/logo.jpg" border="0">.
```

You can use the following code to achieve this result:

``` php
$writer->setImagesRoot('http://www.example.com');
```

#### Formula pre-calculation

By default, this writer pre-calculates all formulas in the spreadsheet.
This can be slow on large spreadsheets, and maybe even unwanted. You can
however disable formula pre-calculation:

``` php
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($spreadsheet);
$writer->setPreCalculateFormulas(false);

$writer->save("05featuredemo.htm");
```

#### Embedding generated HTML in a web page

There might be a situation where you want to embed the generated HTML in
an existing website. \PhpOffice\PhpSpreadsheet\Writer\Html provides
support to generate only specific parts of the HTML code, which allows
you to use these parts in your website.

Supported methods:

-   `generateHTMLHeader()`
-   `generateStyles()`
-   `generateSheetData()`
-   `generateHTMLFooter()`

Here's an example which retrieves all parts independently and merges
them into a resulting HTML page:

``` php
<?php
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($spreadsheet);
echo $writer->generateHTMLHeader();
?>

<style>
<!--
html {
    font-family: Times New Roman;
    font-size: 9pt;
    background-color: white;
}

<?php
echo $writer->generateStyles(false); // do not write <style> and </style>
?>

-->
</style>

<?php
echo $writer->generateSheetData();
echo $writer->generateHTMLFooter();
?>
```

#### Writing UTF-8 HTML files

A HTML file can be marked as UTF-8 by writing a BOM file header. This
can be enabled by using the following code:

``` php
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($spreadsheet);
$writer->setUseBOM(true);

$writer->save("05featuredemo.htm");
```

#### Decimal and thousands separators

See section `\PhpOffice\PhpSpreadsheet\Writer\Csv` how to control the
appearance of these.

## PDF

PhpSpreadsheet allows you to write a spreadsheet into PDF format, for
fast distribution of represented data.

**PDF limitations** Please note that PDF file format has some limits
regarding to styling cells, number formatting, ...

### \PhpOffice\PhpSpreadsheet\Writer\Pdf

PhpSpreadsheet’s PDF Writer is a wrapper for a 3rd-Party PDF Rendering
library such as TCPDF, mPDF or Dompdf. You must now install a PDF
rendering library yourself; but PhpSpreadsheet will work with a number
of different libraries.

Currently, the following libraries are supported:

Library | Downloadable from                   | PhpSpreadsheet writer
--------|-------------------------------------|----------------------
TCPDF   | https://github.com/tecnickcom/tcpdf | Tcpdf
mPDF    | https://github.com/mpdf/mpdf        | Mpdf
Dompdf  | https://github.com/dompdf/dompdf    | Dompdf

The different libraries have different strengths and weaknesses. Some
generate better formatted output than others, some are faster or use
less memory than others, while some generate smaller .pdf files. It is
the developers choice which one they wish to use, appropriate to their
own circumstances.

You can instantiate a writer with its specific name, like so:

``` php
$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Mpdf');
```

Or you can register which writer you are using with a more generic name,
so you don't need to remember which library you chose, only that you want
to write PDF files:

``` php
$class = \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf::class;
\PhpOffice\PhpSpreadsheet\IOFactory::registerWriter('Pdf', $class);
$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Pdf');
```

Or you can instantiate directly the writer of your choice like so:

``` php
$writer = \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
```

#### Custom implementation or configuration

If you need a custom implementation, or custom configuration, of a supported
PDF library. You can extends the PDF library, and the PDF writer like so:

``` php
class My_Custom_TCPDF extends TCPDF
{
	// ...
}

class My_Custom_TCPDF_Writer extends \PhpOffice\PhpSpreadsheet\Writer\Pdf\Tcpdf
{
	protected function createExternalWriterInstance($orientation, $unit, $paperSize)
	{
		$instance = new My_Custom_TCPDF($orientation, $unit, $paperSize);

		// more configuration of $instance

		return $instance;
	}
}

\PhpOffice\PhpSpreadsheet\IOFactory::registerWriter('Pdf', MY_TCPDF_WRITER::class);
```

#### Writing a spreadsheet

Once you have identified the Renderer that you wish to use for PDF
generation, you can write a .pdf file using the following code:

``` php
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
$writer->save("05featuredemo.pdf");
```

Please note that `\PhpOffice\PhpSpreadsheet\Writer\Pdf` only outputs the
first worksheet by default.

#### Write all worksheets

PDF files can contain one or more worksheets. If you want to write all
sheets into a single PDF file, use the following code:

``` php
$writer->writeAllSheets();
```

#### Write a specific worksheet

PDF files can contain one or more worksheets. Therefore, you can specify
which sheet to write to PDF:

``` php
$writer->setSheetIndex(0);
```

#### Formula pre-calculation

By default, this writer pre-calculates all formulas in the spreadsheet.
This can be slow on large spreadsheets, and maybe even unwanted. You can
however disable formula pre-calculation:

``` php
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
$writer->setPreCalculateFormulas(false);

$writer->save("05featuredemo.pdf");
```

#### Decimal and thousands separators

See section `\PhpOffice\PhpSpreadsheet\Writer\Csv` how to control the
appearance of these.

## Generating Excel files from templates (read, modify, write)

Readers and writers are the tools that allow you to generate Excel files
from templates. This requires less coding effort than generating the
Excel file from scratch, especially if your template has many styles,
page setup properties, headers etc.

Here is an example how to open a template file, fill in a couple of
fields and save it again:

``` php
$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('template.xlsx');

$worksheet = $spreadsheet->getActiveSheet();

$worksheet->getCell('A1')->setValue('John');
$worksheet->getCell('A2')->setValue('Smith');

$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
$writer->save('write.xls');
```

Notice that it is ok to load an xlsx file and generate an xls file.
