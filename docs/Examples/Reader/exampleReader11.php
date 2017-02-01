<?php

error_reporting(E_ALL);
set_time_limit(0);

date_default_timezone_set('Europe/London');

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>PHPExcel Reader Example #11</title>

</head>
<body>

<h1>PHPExcel Reader Example #11</h1>
<h2>Reading a Workbook in "Chunks" Using a Configurable Read Filter (Version 1)</h2>
<?php

/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . '../../../Classes/');

/** \PhpOffice\PhpSpreadsheet\IOFactory */
include 'PHPExcel/IOFactory.php';

$inputFileType = 'Xls';
//	$inputFileType = 'Xlsx';
//	$inputFileType = 'Xml';
//	$inputFileType = 'Ods';
//	$inputFileType = 'Gnumeric';
$inputFileName = './sampleData/example2.xls';

/**  Define a Read Filter class implementing \PhpOffice\PhpSpreadsheet\Reader\IReadFilter  */
class chunkReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    private $_startRow = 0;

    private $_endRow = 0;

    /**
     * We expect a list of the rows that we want to read to be passed into the constructor.
     *
     * @param mixed $startRow
     * @param mixed $chunkSize
     */
    public function __construct($startRow, $chunkSize)
    {
        $this->_startRow = $startRow;
        $this->_endRow = $startRow + $chunkSize;
    }

    public function readCell($column, $row, $worksheetName = '')
    {
        //  Only read the heading row, and the rows that were configured in the constructor
        if (($row == 1) || ($row >= $this->_startRow && $row < $this->_endRow)) {
            return true;
        }

        return false;
    }
}

echo 'Loading file ',pathinfo($inputFileName, PATHINFO_BASENAME),' using IOFactory with a defined reader type of ',$inputFileType,'<br />';
/*  Create a new Reader of the type defined in $inputFileType  **/
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);

echo '<hr />';

/*  Define how many rows we want for each "chunk"  **/
$chunkSize = 20;

/*  Loop to read our worksheet in "chunk size" blocks  **/
for ($startRow = 2; $startRow <= 240; $startRow += $chunkSize) {
    echo 'Loading WorkSheet using configurable filter for headings row 1 and for rows ',$startRow,' to ',($startRow + $chunkSize - 1),'<br />';
    /*  Create a new Instance of our Read Filter, passing in the limits on which rows we want to read  **/
    $chunkFilter = new chunkReadFilter($startRow, $chunkSize);
    /*  Tell the Reader that we want to use the new Read Filter that we've just Instantiated  **/
    $reader->setReadFilter($chunkFilter);
    /*  Load only the rows that match our filter from $inputFileName to a PHPExcel Object  **/
    $spreadsheet = $reader->load($inputFileName);

    //	Do some processing here

    $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
    var_dump($sheetData);
    echo '<br /><br />';
}

?>
<body>
</html>