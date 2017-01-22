<?php

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>PHPExcel Reader Example #15</title>

</head>
<body>

<h1>PHPExcel Reader Example #15</h1>
<h2>Simple File Reader for Tab-Separated Value File using the Advanced Value Binder</h2>
<?php

/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . '../../../Classes/');

/** \PhpOffice\PhpSpreadsheet\IOFactory */
include 'PHPExcel/IOFactory.php';

\PhpOffice\PhpSpreadsheet\Cell::setValueBinder(new \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder());

$inputFileType = 'Csv';
$inputFileName = './sampleData/example1.tsv';

$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
echo 'Loading file ',pathinfo($inputFileName, PATHINFO_BASENAME),' into WorkSheet #1 using IOFactory with a defined reader type of ',$inputFileType,'<br />';
$reader->setDelimiter("\t");
$spreadsheet = $reader->load($inputFileName);
$spreadsheet->getActiveSheet()->setTitle(pathinfo($inputFileName, PATHINFO_BASENAME));

echo '<hr />';

echo $spreadsheet->getSheetCount(),' worksheet',(($spreadsheet->getSheetCount() == 1) ? '' : 's'),' loaded<br /><br />';
$loadedSheetNames = $spreadsheet->getSheetNames();
foreach ($loadedSheetNames as $sheetIndex => $loadedSheetName) {
    echo '<b>Worksheet #',$sheetIndex,' -> ',$loadedSheetName,' (Formatted)</b><br />';
    $spreadsheet->setActiveSheetIndexByName($loadedSheetName);
    $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
    var_dump($sheetData);
    echo '<br />';
}

echo '<hr />';

foreach ($loadedSheetNames as $sheetIndex => $loadedSheetName) {
    echo '<b>Worksheet #', $sheetIndex, ' -> ', $loadedSheetName, ' (Unformatted)</b><br />';
    $spreadsheet->setActiveSheetIndexByName($loadedSheetName);
    $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, false, true);
    var_dump($sheetData);
    echo '<br />';
}

echo '<hr />';

foreach ($loadedSheetNames as $sheetIndex => $loadedSheetName) {
    echo '<b>Worksheet #', $sheetIndex, ' -> ', $loadedSheetName, ' (Raw)</b><br />';
    $spreadsheet->setActiveSheetIndexByName($loadedSheetName);
    $sheetData = $spreadsheet->getActiveSheet()->toArray(null, false, false, true);
    var_dump($sheetData);
    echo '<br />';
}

?>
<body>
</html>