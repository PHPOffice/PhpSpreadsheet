<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

error_reporting(E_ALL);
set_time_limit(0);

date_default_timezone_set('Europe/London');
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

        <title>PhpSpreadsheet Reader Example #13</title>

    </head>
    <body>

        <h1>PhpSpreadsheet Reader Example #13</h1>
        <h2>Simple File Reader for Multiple CSV Files</h2>
        <?php
        require_once __DIR__ . '/../../../src/Bootstrap.php';

        $inputFileType = 'Csv';
        $inputFileNames = ['./sampleData/example1.csv', './sampleData/example2.csv'];

        $reader = IOFactory::createReader($inputFileType);
        $inputFileName = array_shift($inputFileNames);
        echo 'Loading file ', pathinfo($inputFileName, PATHINFO_BASENAME), ' into WorkSheet #1 using IOFactory with a defined reader type of ', $inputFileType, '<br />';
        $spreadsheet = $reader->load($inputFileName);
        $spreadsheet->getActiveSheet()->setTitle(pathinfo($inputFileName, PATHINFO_BASENAME));
        foreach ($inputFileNames as $sheet => $inputFileName) {
            echo 'Loading file ', pathinfo($inputFileName, PATHINFO_BASENAME), ' into WorkSheet #', ($sheet + 2), ' using IOFactory with a defined reader type of ', $inputFileType, '<br />';
            $reader->setSheetIndex($sheet + 1);
            $reader->loadIntoExisting($inputFileName, $spreadsheet);
            $spreadsheet->getActiveSheet()->setTitle(pathinfo($inputFileName, PATHINFO_BASENAME));
        }

        echo '<hr />';

        echo $spreadsheet->getSheetCount(), ' worksheet', (($spreadsheet->getSheetCount() == 1) ? '' : 's'), ' loaded<br /><br />';
        $loadedSheetNames = $spreadsheet->getSheetNames();
        foreach ($loadedSheetNames as $sheetIndex => $loadedSheetName) {
            echo '<b>Worksheet #', $sheetIndex, ' -> ', $loadedSheetName, '</b><br />';
            $spreadsheet->setActiveSheetIndexByName($loadedSheetName);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            var_dump($sheetData);
            echo '<br /><br />';
        }
        ?>
    <body>
</html>