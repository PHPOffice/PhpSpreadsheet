<?php

error_reporting(E_ALL);
set_time_limit(0);

date_default_timezone_set('Europe/London');

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>PhpSpreadsheet Reader Example #18</title>

</head>
<body>

<h1>PhpSpreadsheet Reader Example #18</h1>
<h2>Reading list of WorkSheets without loading entire file</h2>
<?php

require_once __DIR__ . '/../../../src/Bootstrap.php';

$inputFileType = 'Xls';
//	$inputFileType = 'Xlsx';
//	$inputFileType = 'Xml';
//	$inputFileType = 'Ods';
//	$inputFileType = 'Gnumeric';
$inputFileName = './sampleData/example1.xls';

echo 'Loading file ',pathinfo($inputFileName, PATHINFO_BASENAME),' information using IOFactory with a defined reader type of ',$inputFileType,'<br />';

$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
$worksheetNames = $reader->listWorksheetNames($inputFileName);

echo '<h3>Worksheet Names</h3>';
echo '<ol>';
foreach ($worksheetNames as $worksheetName) {
    echo '<li>', $worksheetName, '</li>';
}
echo '</ol>';

?>
<body>
</html>