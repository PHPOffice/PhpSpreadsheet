<?php

error_reporting(E_ALL);
set_time_limit(0);

date_default_timezone_set('Europe/London');

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>PHPExcel Reader Example #19</title>

</head>
<body>

<h1>PHPExcel Reader Example #19</h1>
<h2>Reading WorkSheet information without loading entire file</h2>
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
$inputFileName = './sampleData/example1.xls';

echo 'Loading file ',pathinfo($inputFileName, PATHINFO_BASENAME),' information using IOFactory with a defined reader type of ',$inputFileType,'<br />';

$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
$worksheetData = $reader->listWorksheetInfo($inputFileName);

echo '<h3>Worksheet Information</h3>';
echo '<ol>';
foreach ($worksheetData as $worksheet) {
    echo '<li>', $worksheet['worksheetName'], '<br />';
    echo 'Rows: ', $worksheet['totalRows'], ' Columns: ', $worksheet['totalColumns'], '<br />';
    echo 'Cell Range: A1:', $worksheet['lastColumnLetter'], $worksheet['totalRows'];
    echo '</li>';
}
echo '</ol>';

?>
<body>
</html>