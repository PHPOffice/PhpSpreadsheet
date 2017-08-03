<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

error_reporting(E_ALL);
set_time_limit(0);

date_default_timezone_set('Europe/London');
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

        <title>PhpSpreadsheet Reading WorkBook Data Example #02</title>

    </head>
    <body>

        <h1>PhpSpreadsheet Reading WorkBook Data Example #02</h1>
        <h2>Read a list of Custom Properties for a WorkBook</h2>
        <?php
        require_once __DIR__ . '/../../../src/Bootstrap.php';

        $inputFileType = 'Xlsx';
        $inputFileName = './sampleData/example1.xlsx';

        /*  Create a new Reader of the type defined in $inputFileType  * */
        $reader = IOFactory::createReader($inputFileType);
        /*  Load $inputFileName to a PhpSpreadsheet Object  * */
        $spreadsheet = $reader->load($inputFileName);

        echo '<hr />';

        /*  Read an array list of any custom properties for this document  * */
        $customPropertyList = $spreadsheet->getProperties()->getCustomProperties();

        echo '<b>Custom Property names: </b><br />';
        foreach ($customPropertyList as $customPropertyName) {
            echo $customPropertyName, '<br />';
        }
        ?>
    <body>
</html>
