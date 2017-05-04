<?php

error_reporting(E_ALL);
set_time_limit(0);

date_default_timezone_set('Europe/London');

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>PhpSpreadsheet Reading WorkBook Data Example #04</title>

</head>
<body>

<h1>PhpSpreadsheet Reading WorkBook Data Example #04</h1>
<h2>Get a List of the Worksheets in a WorkBook</h2>
<?php

require_once __DIR__ . '/../../../src/Bootstrap.php';

$inputFileType = 'Xls';
$inputFileName = './sampleData/example2.xls';

/*  Create a new Reader of the type defined in $inputFileType  **/
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
/*  Load $inputFileName to a PhpSpreadsheet Object  **/
$spreadsheet = $reader->load($inputFileName);

echo '<hr />';

echo 'Reading the number of Worksheets in the WorkBook<br />';
/*  Use the PhpSpreadsheet object's getSheetCount() method to get a count of the number of WorkSheets in the WorkBook  */
$sheetCount = $spreadsheet->getSheetCount();
echo 'There ',(($sheetCount == 1) ? 'is' : 'are'),' ',$sheetCount,' WorkSheet',(($sheetCount == 1) ? '' : 's'),' in the WorkBook<br /><br />';

echo 'Reading the names of Worksheets in the WorkBook<br />';
/*  Use the PhpSpreadsheet object's getSheetNames() method to get an array listing the names/titles of the WorkSheets in the WorkBook  */
$sheetNames = $spreadsheet->getSheetNames();
foreach ($sheetNames as $sheetIndex => $sheetName) {
    echo 'WorkSheet #',$sheetIndex,' is named "',$sheetName,'"<br />';
}

?>
<body>
</html>
