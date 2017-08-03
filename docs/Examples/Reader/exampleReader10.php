<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

error_reporting(E_ALL);
set_time_limit(0);

date_default_timezone_set('Europe/London');
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

        <title>PhpSpreadsheet Reader Example #10</title>

    </head>
    <body>

        <h1>PhpSpreadsheet Reader Example #10</h1>
        <h2>Simple File Reader Using a Configurable Read Filter</h2>
        <?php
        require_once __DIR__ . '/../../../src/Bootstrap.php';

        $inputFileType = 'Xls';
        $inputFileName = './sampleData/example1.xls';
        $sheetname = 'Data Sheet #3';

        class MyReadFilter implements IReadFilter
        {

            private $_startRow = 0;
            private $_endRow = 0;
            private $_columns = [];

            public function __construct($startRow, $endRow, $columns)
            {
                $this->_startRow = $startRow;
                $this->_endRow = $endRow;
                $this->_columns = $columns;
            }

            public function readCell($column, $row, $worksheetName = '')
            {
                if ($row >= $this->_startRow && $row <= $this->_endRow) {
                    if (in_array($column, $this->_columns)) {
                        return true;
                    }
                }

                return false;
            }

        }

        $filterSubset = new MyReadFilter(9, 15, range('G', 'K'));

        echo 'Loading file ', pathinfo($inputFileName, PATHINFO_BASENAME), ' using IOFactory with a defined reader type of ', $inputFileType, '<br />';
        $reader = IOFactory::createReader($inputFileType);
        echo 'Loading Sheet "', $sheetname, '" only<br />';
        $reader->setLoadSheetsOnly($sheetname);
        echo 'Loading Sheet using configurable filter<br />';
        $reader->setReadFilter($filterSubset);
        $spreadsheet = $reader->load($inputFileName);

        echo '<hr />';

        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        var_dump($sheetData);
        ?>
    <body>
</html>