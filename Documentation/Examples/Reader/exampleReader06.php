<?php

error_reporting(E_ALL);
set_time_limit(0);

date_default_timezone_set('Europe/London');

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>PHPExcel Reader Example #06</title>

</head>
<body>

<h1>PHPExcel Reader Example #06</h1>
<h2>Simple File Reader Loading All WorkSheets</h2>
<?php

/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . '../../../Classes/');

/** PHPExcel_IOFactory */
include 'PHPExcel/IOFactory.php';

$inputFileType = 'Xls';
//	$inputFileType = 'Xlsx';
//	$inputFileType = 'Excel2003XML';
//	$inputFileType = 'Ods';
//	$inputFileType = 'Gnumeric';
$inputFileName = './sampleData/example1.xls';

echo 'Loading file ',pathinfo($inputFileName, PATHINFO_BASENAME),' using IOFactory with a defined reader type of ',$inputFileType,'<br />';
$objReader = PHPExcel_IOFactory::createReader($inputFileType);
echo 'Loading all WorkSheets<br />';
$objReader->setLoadAllSheets();
$objPHPExcel = $objReader->load($inputFileName);

echo '<hr />';

echo $objPHPExcel->getSheetCount(),' worksheet',(($objPHPExcel->getSheetCount() == 1) ? '' : 's'),' loaded<br /><br />';
$loadedSheetNames = $objPHPExcel->getSheetNames();
foreach ($loadedSheetNames as $sheetIndex => $loadedSheetName) {
    echo $sheetIndex,' -> ',$loadedSheetName,'<br />';
}

?>
<body>
</html>