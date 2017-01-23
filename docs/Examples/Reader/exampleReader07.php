<?php

error_reporting(E_ALL);
set_time_limit(0);

date_default_timezone_set('Europe/London');

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>PHPExcel Reader Example #07</title>

</head>
<body>

<h1>PHPExcel Reader Example #07</h1>
<h2>Simple File Reader Loading a Single Named WorkSheet</h2>
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
$sheetname = 'Data Sheet #2';

echo 'Loading file ',pathinfo($inputFileName, PATHINFO_BASENAME),' using IOFactory with a defined reader type of ',$inputFileType,'<br />';
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
echo 'Loading Sheet "',$sheetname,'" only<br />';
$reader->setLoadSheetsOnly($sheetname);
$spreadsheet = $reader->load($inputFileName);

echo '<hr />';

echo $spreadsheet->getSheetCount(),' worksheet',(($spreadsheet->getSheetCount() == 1) ? '' : 's'),' loaded<br /><br />';
$loadedSheetNames = $spreadsheet->getSheetNames();
foreach ($loadedSheetNames as $sheetIndex => $loadedSheetName) {
    echo $sheetIndex,' -> ',$loadedSheetName,'<br />';
}

?>
<body>
</html>