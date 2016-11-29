<?php

error_reporting(E_ALL);
set_time_limit(0);

date_default_timezone_set('Europe/London');

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>PHPExcel Reader Example #16</title>

</head>
<body>

<h1>PHPExcel Reader Example #16</h1>
<h2>Handling Loader Exceptions using Try/Catch</h2>
<?php

/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . '../../../Classes/');

/** \PhpOffice\PhpSpreadsheet\IOFactory */
include 'PHPExcel/IOFactory.php';

$inputFileName = './sampleData/example_1.xls';
echo 'Loading file ',pathinfo($inputFileName, PATHINFO_BASENAME),' using IOFactory to identify the format<br />';
try {
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
} catch (\PHPExcel\Reader\Exception $e) {
    die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
}

echo '<hr />';

$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
var_dump($sheetData);

?>
<body>
</html>