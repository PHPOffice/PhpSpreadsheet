<?php

error_reporting(E_ALL);
set_time_limit(0);

date_default_timezone_set('Europe/London');

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>PhpSpreadsheet Reader Example #17</title>

</head>
<body>

<h1>PhpSpreadsheet Reader Example #17</h1>
<h2>Simple File Reader Loading Several Named WorkSheets</h2>
<?php

require_once __DIR__ . '/../../../src/Bootstrap.php';

$inputFileType = 'Xls';
//	$inputFileType = 'Xlsx';
//	$inputFileType = 'Xml';
//	$inputFileType = 'Ods';
//	$inputFileType = 'Gnumeric';
$inputFileName = './sampleData/example1.xls';

echo 'Loading file ',pathinfo($inputFileName, PATHINFO_BASENAME),' using IOFactory with a defined reader type of ',$inputFileType,'<br />';
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);

/*  Read the list of Worksheet Names from the Workbook file  **/
echo 'Read the list of Worksheets in the WorkBook<br />';
$worksheetNames = $reader->listWorksheetNames($inputFileName);

echo 'There are ',count($worksheetNames),' worksheet',((count($worksheetNames) == 1) ? '' : 's'),' in the workbook<br /><br />';
foreach ($worksheetNames as $worksheetName) {
    echo $worksheetName,'<br />';
}

?>
<body>
</html>