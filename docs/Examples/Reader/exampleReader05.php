<?php

error_reporting(E_ALL);
set_time_limit(0);

date_default_timezone_set('Europe/London');

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>PHPExcel Reader Example #05</title>

</head>
<body>

<h1>PHPExcel Reader Example #05</h1>
<h2>Simple File Reader using the "Read Data Only" Option</h2>
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

echo 'Loading file ',pathinfo($inputFileName, PATHINFO_BASENAME),' using IOFactory with a defined reader type of ',$inputFileType,'<br />';
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
echo 'Turning Formatting off for Load<br />';
$reader->setReadDataOnly(true);
$spreadsheet = $reader->load($inputFileName);

echo '<hr />';

$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
var_dump($sheetData);

?>
<body>
</html>