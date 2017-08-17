<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

error_reporting(E_ALL);
set_time_limit(0);

date_default_timezone_set('Europe/London');
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

        <title>PhpSpreadsheet Reader Example #04</title>

    </head>
    <body>

        <h1>PhpSpreadsheet Reader Example #04</h1>
        <h2>Simple File Reader using the IOFactory to Identify a Reader to Use</h2>
        <?php
        require_once __DIR__ . '/../../../src/Bootstrap.php';

        $inputFileName = './sampleData/example1.xls';

        $inputFileType = IOFactory::identify($inputFileName);
        echo 'File ', pathinfo($inputFileName, PATHINFO_BASENAME), ' has been identified as an ', $inputFileType, ' file<br />';

        echo 'Loading file ', pathinfo($inputFileName, PATHINFO_BASENAME), ' using IOFactory with the identified reader type<br />';
        $reader = IOFactory::createReader($inputFileType);
        $spreadsheet = $reader->load($inputFileName);

        echo '<hr />';

        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        var_dump($sheetData);
        ?>
    <body>
</html>