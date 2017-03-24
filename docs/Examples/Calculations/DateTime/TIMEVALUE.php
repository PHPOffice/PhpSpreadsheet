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

<h1>TIMEVALUE</h1>
<h2>Converts a time in the form of text to a serial number.</h2>
<?php

require_once __DIR__ . '/../../../../src/Bootstrap.php';

// Create new PhpSpreadsheet object
$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$testDates = ['3:15',    '13:15',    '15:15:15',    '3:15 AM',    '3:15 PM',    '5PM',    '9:15AM',    '13:15AM',
                  ];
$testDateCount = count($testDates);

for ($row = 1; $row <= $testDateCount; ++$row) {
    $worksheet->setCellValue('A' . $row, $testDates[$row - 1]);
    $worksheet->setCellValue('B' . $row, '=TIMEVALUE(A' . $row . ')');
    $worksheet->setCellValue('C' . $row, '=B' . $row);
}

$worksheet->getStyle('C1:C' . $testDateCount)
          ->getNumberFormat()
          ->setFormatCode('hh:mm:ss');

echo '<hr />';

// Test the formulae
?>
<table border="1" cellspacing="0">
	<tr>
		<th>Time String</th>
		<th>Formula</th>
		<th>Excel TimeStamp</th>
		<th>Formatted TimeStamp</th>
	</tr>
	<?php
    for ($row = 1; $row <= $testDateCount; ++$row) {
        echo '<tr>';
        echo '<td>', $worksheet->getCell('A' . $row)->getFormattedValue(), '</td>';
        echo '<td>', $worksheet->getCell('B' . $row)->getValue(), '</td>';
        echo '<td>', $worksheet->getCell('B' . $row)->getFormattedValue(), '</td>';
        echo '<td>', $worksheet->getCell('C' . $row)->getFormattedValue(), '</td>';
        echo '</tr>';
    }
    ?>
</table>
</body>
</html>