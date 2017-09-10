<?php

use PhpOffice\PhpSpreadsheet\Reader\Xls;

error_reporting(E_ALL);
set_time_limit(0);

date_default_timezone_set('Europe/London');

require_once __DIR__ . '/../../../src/Bootstrap.php';
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

        <title>PhpSpreadsheet Reader Example #02</title>

    </head>
    <body>

        <h1>PhpSpreadsheet Reader Example #02</h1>
        <h2>Simple File Reader using a Specified Reader</h2>
        <?php
        $inputFileName = './sampleData/example1.xls';

        echo 'Loading file ', pathinfo($inputFileName, PATHINFO_BASENAME), ' using \PhpOffice\PhpSpreadsheet\Reader\Xls<br />';
        $reader = new Xls();
        $spreadsheet = $reader->load($inputFileName);

        echo '<hr />';

        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        var_dump($sheetData);
        ?>
    <body>
</html>