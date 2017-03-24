<?php

error_reporting(E_ALL);
set_time_limit(0);

date_default_timezone_set('Europe/London');

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>PhpSpreadsheet Reader Example #09</title>

</head>
<body>

<h1>PhpSpreadsheet Reader Example #09</h1>
<h2>Simple File Reader Using a Read Filter</h2>
<?php

require_once __DIR__ . '/../../../src/Bootstrap.php';

$inputFileType = 'Xls';
//	$inputFileType = 'Xlsx';
//	$inputFileType = 'Xml';
//	$inputFileType = 'Ods';
//	$inputFileType = 'Gnumeric';
$inputFileName = './sampleData/example1.xls';
$sheetname = 'Data Sheet #3';

class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    public function readCell($column, $row, $worksheetName = '')
    {
        // Read rows 1 to 7 and columns A to E only
        if ($row >= 1 && $row <= 7) {
            if (in_array($column, range('A', 'E'))) {
                return true;
            }
        }

        return false;
    }
}

$filterSubset = new MyReadFilter();

echo 'Loading file ',pathinfo($inputFileName, PATHINFO_BASENAME),' using IOFactory with a defined reader type of ',$inputFileType,'<br />';
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
echo 'Loading Sheet "',$sheetname,'" only<br />';
$reader->setLoadSheetsOnly($sheetname);
echo 'Loading Sheet using filter<br />';
$reader->setReadFilter($filterSubset);
$spreadsheet = $reader->load($inputFileName);

echo '<hr />';

$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
var_dump($sheetData);

?>
<body>
</html>