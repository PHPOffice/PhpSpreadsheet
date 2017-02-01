<?php

error_reporting(E_ALL);
set_time_limit(0);

date_default_timezone_set('Europe/London');

/* Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . '../../../Classes/');

/** \PhpOffice\PhpSpreadsheet\IOFactory */
include 'PHPExcel/IOFactory.php';

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>PHPExcel Reader Example #02</title>

</head>
<body>

<h1>PHPExcel Reader Example #02</h1>
<h2>Simple File Reader using a Specified Reader</h2>
<?php

$inputFileName = './sampleData/example1.xls';

echo 'Loading file ',pathinfo($inputFileName, PATHINFO_BASENAME),' using \PhpOffice\PhpSpreadsheet\Reader\Xls<br />';
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
//	$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
//	$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xml();
//	$reader = new \PhpOffice\PhpSpreadsheet\Reader\Ods();
//	$reader = new \PhpOffice\PhpSpreadsheet\Reader\Slk();
//	$reader = new \PhpOffice\PhpSpreadsheet\Reader\Gnumeric();
//	$reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
$spreadsheet = $reader->load($inputFileName);

echo '<hr />';

$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
var_dump($sheetData);

?>
<body>
</html>