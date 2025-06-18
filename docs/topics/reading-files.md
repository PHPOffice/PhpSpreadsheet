# Reading Files

## Security

XML-based formats such as OfficeOpen XML, Excel2003 XML, OASIS and
Gnumeric are susceptible to XML External Entity Processing (XXE)
injection attacks when reading spreadsheet files. This can lead to:

-   Disclosure whether a file is existent
-   Server Side Request Forgery
-   Command Execution (depending on the installed PHP wrappers)

To prevent this, by default every XML-based Reader looks for XML
entities declared inside the DOCTYPE and if any is found an exception
is raised.

Read more [about of XXE injection](https://websec.io/2012/08/27/Preventing-XXE-in-PHP.html).

## Loading a Spreadsheet File

The simplest way to load a workbook file is to let PhpSpreadsheet's IO
Factory identify the file type and load it, calling the static `load()`
method of the `\PhpOffice\PhpSpreadsheet\IOFactory` class.

``` php
$inputFileName = './sampleData/example1.xls';

/** Load $inputFileName to a Spreadsheet Object  **/
$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
```

See `samples/Reader/01_Simple_file_reader_using_IOFactory.php` for a working
example of this code.

The `load()` method will attempt to identify the file type, and
instantiate a loader for that file type; using it to load the file and
store the data and any formatting in a `Spreadsheet` object.

The method makes an initial guess at the loader to instantiate based on
the file extension; but will test the file before actually executing the
load: so if (for example) the file is actually a CSV file or contains
HTML markup, but that has been given a .xls extension (quite a common
practise), it will reject the Xls loader that it would normally use for
a .xls file; and test the file using the other loaders until it finds
the appropriate loader, and then use that to read the file.

While easy to implement in your code, and you don't need to worry about
the file type; this isn't the most efficient method to load a file; and
it lacks the flexibility to configure the loader in any way before
actually reading the file into a `Spreadsheet` object.

## Creating a Reader and Loading a Spreadsheet File

If you know the file type of the spreadsheet file that you need to load,
you can instantiate a new reader object for that file type, then use the
reader's `load()` method to read the file to a `Spreadsheet` object. It is
possible to instantiate the reader objects for each of the different
supported filetype by name. However, you may get unpredictable results
if the file isn't of the right type (e.g. it is a CSV with an extension
of .xls), although this type of exception should normally be trapped.

``` php
$inputFileName = './sampleData/example1.xls';

/** Create a new Xls Reader  **/
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
//    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
//    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xml();
//    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Ods();
//    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Slk();
//    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Gnumeric();
//    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
/** Load $inputFileName to a Spreadsheet Object  **/
$spreadsheet = $reader->load($inputFileName);
```

See `samples/Reader/02_Simple_file_reader_using_a_specified_reader.php`
for a working example of this code.

Alternatively, you can use the IO Factory's `createReader()` method to
instantiate the reader object for you, simply telling it the file type
of the reader that you want instantiating.

``` php
$inputFileType = 'Xls';
//    $inputFileType = 'Xlsx';
//    $inputFileType = 'Xml';
//    $inputFileType = 'Ods';
//    $inputFileType = 'Slk';
//    $inputFileType = 'Gnumeric';
//    $inputFileType = 'Csv';
$inputFileName = './sampleData/example1.xls';

/**  Create a new Reader of the type defined in $inputFileType  **/
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
/**  Load $inputFileName to a Spreadsheet Object  **/
$spreadsheet = $reader->load($inputFileName);
```

See `samples/Reader/03_Simple_file_reader_using_the_IOFactory_to_return_a_reader.php`
for a working example of this code.

If you're uncertain of the filetype, you can use the `IOFactory::identify()`
method to identify the reader that you need, before using the
`createReader()` method to instantiate the reader object.

``` php
$inputFileName = './sampleData/example1.xls';

/**  Identify the type of $inputFileName  **/
$inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($inputFileName);
/**  Create a new Reader of the type that has been identified  **/
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
/**  Load $inputFileName to a Spreadsheet Object  **/
$spreadsheet = $reader->load($inputFileName);
```

See `samples/Reader/04_Simple_file_reader_using_the_IOFactory_to_identify_a_reader_to_use.php`
for a working example of this code.

## Spreadsheet Reader Options

Once you have created a reader object for the workbook that you want to
load, you have the opportunity to set additional options before
executing the `load()` method.

### Reading Only Data from a Spreadsheet File

If you're only interested in the cell values in a workbook, but don't
need any of the cell formatting information, then you can set the reader
to read only the data values and any formulae from each cell using the
`setReadDataOnly()` method.

``` php
$inputFileType = 'Xls';
$inputFileName = './sampleData/example1.xls';

/**  Create a new Reader of the type defined in $inputFileType  **/
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
/**  Advise the Reader that we only want to load cell data  **/
$reader->setReadDataOnly(true);
/**  Load $inputFileName to a Spreadsheet Object  **/
$spreadsheet = $reader->load($inputFileName);
```

See `samples/Reader/05_Simple_file_reader_using_the_read_data_only_option.php`
for a working example of this code.

It is important to note that Workbooks (and PhpSpreadsheet) store dates
and times as simple numeric values: they can only be distinguished from
other numeric values by the format mask that is applied to that cell.
When setting read data only to true, PhpSpreadsheet doesn't read the
cell format masks, so it is not possible to differentiate between
dates/times and numbers.

The Gnumeric loader has been written to read the format masks for date
values even when read data only has been set to true, so it can
differentiate between dates/times and numbers; but this change hasn't
yet been implemented for the other readers.

Reading Only Data from a Spreadsheet File applies to Readers:

Reader    | Y/N |Reader  | Y/N |Reader        | Y/N |
----------|:---:|--------|:---:|--------------|:---:|
Xlsx      | YES | Xls | YES | Xml | YES |
Ods    | YES | SYLK   | NO  | Gnumeric     | YES |
CSV       | NO  | HTML   | NO

### Reading Only Named WorkSheets from a File

If your workbook contains a number of worksheets, but you are only
interested in reading some of those, then you can use the
`setLoadSheetsOnly()` method to identify those sheets you are interested
in reading.

To read a single sheet, you can pass that sheet name as a parameter to
the `setLoadSheetsOnly()` method.

``` php
$inputFileType = 'Xls';
$inputFileName = './sampleData/example1.xls';
$sheetname = 'Data Sheet #2';

/**  Create a new Reader of the type defined in $inputFileType  **/
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
/**  Advise the Reader of which WorkSheets we want to load  **/
$reader->setLoadSheetsOnly($sheetname);
/**  Load $inputFileName to a Spreadsheet Object  **/
$spreadsheet = $reader->load($inputFileName);
```

See `samples/Reader/07_Simple_file_reader_loading_a_single_named_worksheet.php`
for a working example of this code.

If you want to read more than just a single sheet, you can pass a list
of sheet names as an array parameter to the `setLoadSheetsOnly()` method.

``` php
$inputFileType = 'Xls';
$inputFileName = './sampleData/example1.xls';
$sheetnames = ['Data Sheet #1','Data Sheet #3'];

/**  Create a new Reader of the type defined in $inputFileType  **/
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
/**  Advise the Reader of which WorkSheets we want to load  **/
$reader->setLoadSheetsOnly($sheetnames);
/**  Load $inputFileName to a Spreadsheet Object  **/
$spreadsheet = $reader->load($inputFileName);
```

See `samples/Reader/08_Simple_file_reader_loading_several_named_worksheets.php`
for a working example of this code.

To reset this option to the default, you can call the `setLoadAllSheets()`
method.

``` php
$inputFileType = 'Xls';
$inputFileName = './sampleData/example1.xls';

/**  Create a new Reader of the type defined in $inputFileType  **/
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
/**  Advise the Reader to load all Worksheets  **/
$reader->setLoadAllSheets();
/**  Load $inputFileName to a Spreadsheet Object  **/
$spreadsheet = $reader->load($inputFileName);
```

See `samples/Reader/06_Simple_file_reader_loading_all_worksheets.php` for a
working example of this code.

Reading Only Named WorkSheets from a File applies to Readers:

Reader    | Y/N |Reader  | Y/N |Reader        | Y/N |
----------|:---:|--------|:---:|--------------|:---:|
Xlsx      | YES | Xls | YES | Xml | YES |
Ods    | YES | SYLK   | NO  | Gnumeric     | YES |
CSV       | NO  | HTML   | NO

### Reading Only Specific Columns and Rows from a File (Read Filters)

If you are only interested in reading part of a worksheet, then you can
write a filter class that identifies whether or not individual cells
should be read by the loader. A read filter must implement the
`\PhpOffice\PhpSpreadsheet\Reader\IReadFilter` interface, and contain a
`readCell()` method that accepts arguments of `$column`, `$row` and
`$worksheetName`, and return a boolean true or false that indicates
whether a workbook cell identified by those arguments should be read or
not.

``` php
$inputFileType = 'Xls';
$inputFileName = './sampleData/example1.xls';
$sheetname = 'Data Sheet #3';

/**  Define a Read Filter class implementing \PhpOffice\PhpSpreadsheet\Reader\IReadFilter  */
class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    public function readCell($column, $row, $worksheetName = '') {
        //  Read rows 1 to 7 and columns A to E only
        if ($row >= 1 && $row <= 7) {
            if (in_array($column,range('A','E'))) {
                return true;
            }
        }
        return false;
    }
}

/**  Create an Instance of our Read Filter  **/
$filterSubset = new MyReadFilter();

/**  Create a new Reader of the type defined in $inputFileType  **/
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
/**  Tell the Reader that we want to use the Read Filter  **/
$reader->setReadFilter($filterSubset);
/**  Load only the rows and columns that match our filter to Spreadsheet  **/
$spreadsheet = $reader->load($inputFileName);
```

See `samples/Reader/09_Simple_file_reader_using_a_read_filter.php` for a
working example of this code.

This example is not particularly useful, because it can only be used in
a very specific circumstance (when you only want cells in the range
A1:E7 from your worksheet. A generic Read Filter would probably be more
useful:

``` php
/**  Define a Read Filter class implementing \PhpOffice\PhpSpreadsheet\Reader\IReadFilter  */
class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    private $startRow = 0;
    private $endRow   = 0;
    private $columns  = [];

    /**  Get the list of rows and columns to read  */
    public function __construct($startRow, $endRow, $columns) {
        $this->startRow = $startRow;
        $this->endRow   = $endRow;
        $this->columns  = $columns;
    }

    public function readCell($column, $row, $worksheetName = '') {
        //  Only read the rows and columns that were configured
        if ($row >= $this->startRow && $row <= $this->endRow) {
            if (in_array($column,$this->columns)) {
                return true;
            }
        }
        return false;
    }
}

/**  Create an Instance of our Read Filter, passing in the cell range  **/
$filterSubset = new MyReadFilter(9,15,range('G','K'));
```

See `samples/Reader/10_Simple_file_reader_using_a_configurable_read_filter.php`
for a working example of this code.

This can be particularly useful for conserving memory, by allowing you
to read and process a large workbook in "chunks": an example of this
usage might be when transferring data from an Excel worksheet to a
database.

``` php
$inputFileType = 'Xls';
$inputFileName = './sampleData/example2.xls';

/**  Define a Read Filter class implementing \PhpOffice\PhpSpreadsheet\Reader\IReadFilter  */
class ChunkReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    private $startRow = 0;
    private $endRow   = 0;

    /**  Set the list of rows that we want to read  */
    public function setRows($startRow, $chunkSize) {
        $this->startRow = $startRow;
        $this->endRow   = $startRow + $chunkSize;
    }

    public function readCell($column, $row, $worksheetName = '') {
        //  Only read the heading row, and the configured rows
        if (($row == 1) || ($row >= $this->startRow && $row < $this->endRow)) {
            return true;
        }
        return false;
    }
}

/**  Create a new Reader of the type defined in $inputFileType  **/
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);

/**  Define how many rows we want to read for each "chunk"  **/
$chunkSize = 2048;
/**  Create a new Instance of our Read Filter  **/
$chunkFilter = new ChunkReadFilter();

/**  Tell the Reader that we want to use the Read Filter  **/
$reader->setReadFilter($chunkFilter);

/**  Loop to read our worksheet in "chunk size" blocks  **/
for ($startRow = 2; $startRow <= 65536; $startRow += $chunkSize) {
    /**  Tell the Read Filter which rows we want this iteration  **/
    $chunkFilter->setRows($startRow,$chunkSize);
    /**  Load only the rows that match our filter  **/
    $spreadsheet = $reader->load($inputFileName);
    //    Do some processing here
}
```

See `samples/Reader/12_Reading_a_workbook_in_chunks_using_a_configurable_read_filter_`
for a working example of this code.

Using Read Filters applies to:

Reader    | Y/N |Reader  | Y/N |Reader        | Y/N |
----------|:---:|--------|:---:|--------------|:---:|
Xlsx      | YES | Xls    | YES | Xml | YES |
Ods       | YES | SYLK   | NO  | Gnumeric     | YES |
CSV       | YES | HTML   | NO  |              |     |

### Combining Multiple Files into a Single Spreadsheet Object

While you can limit the number of worksheets that are read from a
workbook file using the `setLoadSheetsOnly()` method, certain readers also
allow you to combine several individual "sheets" from different files
into a single `Spreadsheet` object, where each individual file is a
single worksheet within that workbook. For each file that you read, you
need to indicate which worksheet index it should be loaded into using
the `setSheetIndex()` method of the `$reader`, then use the
`loadIntoExisting()` method rather than the `load()` method to actually read
the file into that worksheet.

``` php
$inputFileType = 'Csv';
$inputFileNames = [
    './sampleData/example1.csv',
    './sampleData/example2.csv'
    './sampleData/example3.csv'
];

/**  Create a new Reader of the type defined in $inputFileType  **/
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);

/**  Extract the first named file from the array list  **/
$inputFileName = array_shift($inputFileNames);
/**  Load the initial file to the first worksheet in a `Spreadsheet` Object  **/
$spreadsheet = $reader->load($inputFileName);
/**  Set the worksheet title (to the filename that we've loaded)  **/
$spreadsheet->getActiveSheet()
    ->setTitle(pathinfo($inputFileName,PATHINFO_BASENAME));

/**  Loop through all the remaining files in the list  **/
foreach($inputFileNames as $sheet => $inputFileName) {
    /**  Increment the worksheet index pointer for the Reader  **/
    $reader->setSheetIndex($sheet+1);
    /**  Load the current file into a new worksheet in Spreadsheet  **/
    $reader->loadIntoExisting($inputFileName,$spreadsheet);
    /**  Set the worksheet title (to the filename that we've loaded)  **/
    $spreadsheet->getActiveSheet()
        ->setTitle(pathinfo($inputFileName,PATHINFO_BASENAME));
}
```

See `samples/Reader/13_Simple_file_reader_for_multiple_CSV_files.php` for a
working example of this code.

Note that using the same sheet index for multiple sheets won't append
files into the same sheet, but overwrite the results of the previous
load. You cannot load multiple CSV files into the same worksheet.

Combining Multiple Files into a Single Spreadsheet Object applies to:

Reader    | Y/N |Reader  | Y/N |Reader        | Y/N |
----------|:---:|--------|:---:|--------------|:---:|
Xlsx      | NO  | Xls    | NO  | Xml | NO  |
Ods       | NO  | SYLK   | YES | Gnumeric     | NO  |
CSV       | YES | HTML   | NO

### Combining Read Filters with the `setSheetIndex()` method to split a large CSV file across multiple Worksheets

An Xls BIFF .xls file is limited to 65536 rows in a worksheet, while the
Xlsx Microsoft Office Open XML SpreadsheetML .xlsx file is limited to
1,048,576 rows in a worksheet; but a CSV file is not limited other than
by available disk space. This means that we wouldn’t ordinarily be able
to read all the rows from a very large CSV file that exceeded those
limits, and save it as an Xls or Xlsx file. However, by using Read
Filters to read the CSV file in "chunks" (using the ChunkReadFilter
Class that we defined in [the above section](#reading-only-specific-columns-and-rows-from-a-file-read-filters),
and the `setSheetIndex()` method of the `$reader`, we can split the CSV
file across several individual worksheets.

``` php
$inputFileType = 'Csv';
$inputFileName = './sampleData/example2.csv';

echo 'Loading file ',pathinfo($inputFileName,PATHINFO_BASENAME),' using IOFactory with a defined reader type of ',$inputFileType,'<br />';
/**  Create a new Reader of the type defined in $inputFileType  **/
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);

/**  Define how many rows we want to read for each "chunk"  **/
$chunkSize = 65530;
/**  Create a new Instance of our Read Filter  **/
$chunkFilter = new ChunkReadFilter();

/**  Tell the Reader that we want to use the Read Filter  **/
/**    and that we want to store it in contiguous rows/columns  **/

$reader->setReadFilter($chunkFilter)
    ->setContiguous(true);

/**  Instantiate a new Spreadsheet object manually  **/
$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

/**  Set a sheet index  **/
$sheet = 0;
/**  Loop to read our worksheet in "chunk size" blocks  **/
/**  $startRow is set to 2 initially because we always read the headings in row #1  **/
for ($startRow = 2; $startRow <= 1000000; $startRow += $chunkSize) {
    /**  Tell the Read Filter which rows we want to read this loop  **/
    $chunkFilter->setRows($startRow,$chunkSize);

    /**  Increment the worksheet index pointer for the Reader  **/
    $reader->setSheetIndex($sheet);
    /**  Load only the rows that match our filter into a new worksheet  **/
    $reader->loadIntoExisting($inputFileName,$spreadsheet);
    /**  Set the worksheet title for the sheet that we've justloaded)  **/
    /**    and increment the sheet index as well  **/
    $spreadsheet->getActiveSheet()->setTitle('Country Data #'.(++$sheet));
}
```

See `samples/Reader/14_Reading_a_large_CSV_file_in_chunks_to_split_across_multiple_worksheets.php`
for a working example of this code.

This code will read 65,530 rows at a time from the CSV file that we’re
loading, and store each "chunk" in a new worksheet.

The `setContiguous()` method for the Reader is important here. It is
applicable only when working with a Read Filter, and identifies whether
or not the cells should be stored by their position within the CSV file,
or their position relative to the filter.

For example, if the filter returned true for cells in the range B2:C3,
then with setContiguous set to false (the default) these would be loaded
as B2:C3 in the `Spreadsheet` object; but with setContiguous set to
true, they would be loaded as A1:B2.

Splitting a single loaded file across multiple worksheets applies to:

Reader    | Y/N |Reader  | Y/N |Reader        | Y/N |
----------|:---:|--------|:---:|--------------|:---:|
Xlsx      | NO  | Xls    | NO  | Xml | NO  |
Ods       | NO  | SYLK   | NO  | Gnumeric     | NO  |
CSV       | YES | HTML   | NO

### Pipe or Tab Separated Value Files

The CSV loader will attempt to auto-detect the separator used in the file. If it
cannot auto-detect, it will default to the comma. If this does not fit your
use-case, you can manually specify a separator by using the `setDelimiter()`
method.

``` php
$inputFileType = 'Csv';
$inputFileName = './sampleData/example1.tsv';

/**  Create a new Reader of the type defined in $inputFileType  **/
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
/**  Set the delimiter to a TAB character  **/
$reader->setDelimiter("\t");
//    $reader->setDelimiter('|');

/**  Load the file to a Spreadsheet Object  **/
$spreadsheet = $reader->load($inputFileName);
```

See `samples/Reader/15_Simple_file_reader_for_tab_separated_value_file_using_the_Advanced_Value_Binder.php`
for a working example of this code.

In addition to the delimiter, you can also use the following methods to
set other attributes for the data load:

Method             | Default
-------------------|----------
setEnclosure()     | `"`
setInputEncoding() | `UTF-8`

Setting CSV delimiter applies to:

Reader    | Y/N |Reader  | Y/N |Reader        | Y/N |
----------|:---:|--------|:---:|--------------|:---:|
Xlsx      | NO  | Xls    | NO  | Xml | NO  |
Ods       | NO  | SYLK   | NO  | Gnumeric     | NO  |
CSV       | YES | HTML   | NO

### A Brief Word about the Advanced Value Binder

When loading data from a file that contains no formatting information,
such as a CSV file, then data is read either as strings or numbers
(float or integer). This means that PhpSpreadsheet does not
automatically recognise dates/times (such as `16-Apr-2009` or `13:30`),
booleans (`true` or `false`), percentages (`75%`), hyperlinks
(`https://www.example.com`), etc as anything other than simple strings.
However, you can apply additional processing that is executed against
these values during the load process within a Value Binder.

A Value Binder is a class that implement the
`\PhpOffice\PhpSpreadsheet\Cell\IValueBinder` interface. It must contain a
`bindValue()` method that accepts a `\PhpOffice\PhpSpreadsheet\Cell\Cell` and a
value as arguments, and return a boolean `true` or `false` that indicates
whether the workbook cell has been populated with the value or not. The
Advanced Value Binder implements such a class: amongst other tests, it
identifies a string comprising "TRUE" or "FALSE" (based on locale
settings) and sets it to a boolean; or a number in scientific format
(e.g. "1.234e-5") and converts it to a float; or dates and times,
converting them to their Excel timestamp value – before storing the
value in the cell object. It also sets formatting for strings that are
identified as dates, times or percentages. It could easily be extended
to provide additional handling (including text or cell formatting) when
it encountered a hyperlink, or HTML markup within a CSV file.

So using a Value Binder allows a great deal more flexibility in the
loader logic when reading unformatted text files.

``` php
/**  Tell PhpSpreadsheet that we want to use the Advanced Value Binder  **/
\PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder( new \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder() );

$inputFileType = 'Csv';
$inputFileName = './sampleData/example1.tsv';

$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
$reader->setDelimiter("\t");
$spreadsheet = $reader->load($inputFileName);
```

See `samples/Reader/15_Simple_file_reader_for_tab_separated_value_file_using_the_Advanced_Value_Binder.php`
for a working example of this code.

Loading using a Value Binder applies to:

Reader    | Y/N |Reader  | Y/N |Reader        | Y/N
----------|:---:|--------|:---:|--------------|:---:
Xlsx      | NO  | Xls    | NO  | Xml | NO
Ods       | NO  | SYLK   | NO  | Gnumeric     | NO
CSV       | YES | HTML   | YES

## Error Handling

Of course, you should always apply some error handling to your scripts
as well. PhpSpreadsheet throws exceptions, so you can wrap all your code
that accesses the library methods within Try/Catch blocks to trap for
any problems that are encountered, and deal with them in an appropriate
manner.

The PhpSpreadsheet Readers throw a
`\PhpOffice\PhpSpreadsheet\Reader\Exception`.

``` php
$inputFileName = './sampleData/example-1.xls';

try {
    /** Load $inputFileName to a Spreadsheet Object  **/
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
} catch(\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
    die('Error loading file: '.$e->getMessage());
}
```

See `samples/Reader/16_Handling_loader_exceptions_using_TryCatch.php` for a
working example of this code.

## Helper Methods

You can retrieve a list of worksheet names contained in a file without
loading the whole file by using the Reader’s `listWorksheetNames()`
method; similarly, a `listWorksheetInfo()` method will retrieve the
dimensions of worksheet in a file without needing to load and parse the
whole file.

### listWorksheetNames

The `listWorksheetNames()` method returns a simple array listing each
worksheet name within the workbook:

``` php
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);

$worksheetNames = $reader->listWorksheetNames($inputFileName);

echo '<h3>Worksheet Names</h3>';
echo '<ol>';
foreach ($worksheetNames as $worksheetName) {
    echo '<li>', $worksheetName, '</li>';
}
echo '</ol>';
```

See `samples/Reader/18_Reading_list_of_worksheets_without_loading_entire_file.php`
for a working example of this code.

### listWorksheetInfo

The `listWorksheetInfo()` method returns a nested array, with each entry
listing the name and dimensions for a worksheet:

``` php
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);

$worksheetData = $reader->listWorksheetInfo($inputFileName);

echo '<h3>Worksheet Information</h3>';
echo '<ol>';
foreach ($worksheetData as $worksheet) {
    echo '<li>', $worksheet['worksheetName'], '<br />';
    echo 'Rows: ', $worksheet['totalRows'],
         ' Columns: ', $worksheet['totalColumns'], '<br />';
    echo 'Cell Range: A1:',
    $worksheet['lastColumnLetter'], $worksheet['totalRows'];
    echo '</li>';
}
echo '</ol>';
```

See `samples/Reader/19_Reading_worksheet_information_without_loading_entire_file.php`
for a working example of this code.
