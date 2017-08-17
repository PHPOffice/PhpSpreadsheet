<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

error_reporting(E_ALL);
set_time_limit(0);

date_default_timezone_set('Europe/London');
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

        <title>PhpSpreadsheet Reader Example #01</title>

    </head>
    <body>

        <h1>PhpSpreadsheet Reader Example #01</h1>
        <h2>Simple File Reader using IOFactory::load()</h2>
        <?php
        require_once __DIR__ . '/../../../src/Bootstrap.php';

        $inputFileName = './sampleData/example1.xls';
        echo 'Loading file ', pathinfo($inputFileName, PATHINFO_BASENAME), ' using IOFactory to identify the format<br />';
        $spreadsheet = IOFactory::load($inputFileName);

        echo '<hr />';

        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        var_dump($sheetData);
        ?>
    <body>
</html>