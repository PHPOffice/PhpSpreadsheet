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

<h1>TIME</h1>
<h2>Returns the serial number of a particular time.</h2>
<?php

require_once __DIR__ . '/../../../../src/Bootstrap.php';

// Create new PhpSpreadsheet object
$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$testDates = [[3, 15],        [13, 15],    [15, 15, 15],    [3, 15, 30],
                    [15, 15, 15],    [5],        [9, 15, 0],        [9, 15, -1],
                    [13, -14, -15],    [0, 0, -1],
                  ];
$testDateCount = count($testDates);

$worksheet->fromArray($testDates, null, 'A1', true);

for ($row = 1; $row <= $testDateCount; ++$row) {
    $worksheet->setCellValue('D' . $row, '=TIME(A' . $row . ',B' . $row . ',C' . $row . ')');
    $worksheet->setCellValue('E' . $row, '=D' . $row);
}
$worksheet->getStyle('E1:E' . $testDateCount)
          ->getNumberFormat()
          ->setFormatCode('hh:mm:ss');

echo '<hr />';

// Test the formulae
?>
<table border="1" cellspacing="0">
	<tr>
		<th colspan="3">Date Value</th>
		<th rowspan="2" valign="bottom">Formula</th>
		<th rowspan="2" valign="bottom">Excel TimeStamp</th>
		<th rowspan="2" valign="bottom">Formatted TimeStamp</th>
	</tr>
	<tr>
		<th>Hour</th>
		<th>Minute</th>
		<th>Second</th>
	<tr>
	<?php
    for ($row = 1; $row <= $testDateCount; ++$row) {
        echo '<tr>';
        echo '<td>', $worksheet->getCell('A' . $row)->getFormattedValue(), '</td>';
        echo '<td>', $worksheet->getCell('B' . $row)->getFormattedValue(), '</td>';
        echo '<td>', $worksheet->getCell('C' . $row)->getFormattedValue(), '</td>';
        echo '<td>', $worksheet->getCell('D' . $row)->getValue(), '</td>';
        echo '<td>', $worksheet->getCell('D' . $row)->getFormattedValue(), '</td>';
        echo '<td>', $worksheet->getCell('E' . $row)->getFormattedValue(), '</td>';
        echo '</tr>';
    }
    ?>
</table>
</body>
</html>