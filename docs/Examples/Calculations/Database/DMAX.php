<?php

error_reporting(E_ALL);
set_time_limit(0);

date_default_timezone_set('Europe/London');

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>PhpSpreadsheet Calculation Examples</title>

</head>
<body>

<h1>DMAX</h1>
<h2>Returns the maximum value from selected database entries.</h2>
<?php

/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . '../../../../Classes/');

/** Include PhpSpreadsheet */
include 'PHPExcel.php';

// Create new PhpSpreadsheet object
$spreadsheet = new PHPExcel();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$database = [['Tree',  'Height', 'Age', 'Yield', 'Profit'],
                   ['Apple',  18,       20,    14,      105.00],
                   ['Pear',   12,       12,    10,       96.00],
                   ['Cherry', 13,       14,     9,      105.00],
                   ['Apple',  14,       15,    10,       75.00],
                   ['Pear',    9,        8,     8,       76.80],
                   ['Apple',   8,        9,     6,       45.00],
                 ];
$criteria = [['Tree',      'Height', 'Age', 'Yield', 'Profit', 'Height'],
                   ['="=Apple"', '>10',    null,  null,    null,     '<16'],
                   ['="=Pear"',  null,     null,  null,    null,     null],
                 ];

$worksheet->fromArray($criteria, null, 'A1');
$worksheet->fromArray($database, null, 'A4');

$worksheet->setCellValue('A12', 'The tallest tree in the orchard');
$worksheet->setCellValue('B12', '=DMAX(A4:E10,"Height",A4:E10)');

$worksheet->setCellValue('A13', 'The Oldest apple tree in the orchard');
$worksheet->setCellValue('B13', '=DMAX(A4:E10,3,A1:A2)');

echo '<hr />';

echo '<h4>Database</h4>';

$databaseData = $worksheet->rangeToArray('A4:E10', null, true, true, true);
var_dump($databaseData);

echo '<hr />';

// Test the formulae
echo '<h4>Criteria</h4>';

echo 'ALL' . '<br /><br />';

echo $worksheet->getCell('A12')->getValue() . '<br />';
echo 'DMAX() Result is ' . $worksheet->getCell('B12')->getCalculatedValue() . '<br /><br />';

echo '<h4>Criteria</h4>';

$criteriaData = $worksheet->rangeToArray('A1:A2', null, true, true, true);
var_dump($criteriaData);

echo $worksheet->getCell('A13')->getValue() . '<br />';
echo 'DMAX() Result is ' . $worksheet->getCell('B13')->getCalculatedValue();

?>
<body>
</html>