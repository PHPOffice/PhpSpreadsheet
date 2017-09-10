<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

error_reporting(E_ALL);
set_time_limit(0);

date_default_timezone_set('Europe/London');
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

        <title>PhpSpreadsheet Reader Example #14</title>

    </head>
    <body>

        <h1>PhpSpreadsheet Reader Example #14</h1>
        <h2>Reading a Large CSV file in "Chunks" to split across multiple Worksheets</h2>
        <?php
        require_once __DIR__ . '/../../../src/Bootstrap.php';

        $inputFileType = 'Csv';
        $inputFileName = './sampleData/example2.csv';

        /**  Define a Read Filter class implementing IReadFilter  */
        class chunkReadFilter implements IReadFilter
        {

            private $_startRow = 0;
            private $_endRow = 0;

            /**
             * Set the list of rows that we want to read.
             *
             * @param mixed $startRow
             * @param mixed $chunkSize
             */
            public function setRows($startRow, $chunkSize)
            {
                $this->_startRow = $startRow;
                $this->_endRow = $startRow + $chunkSize;
            }

            public function readCell($column, $row, $worksheetName = '')
            {
                //  Only read the heading row, and the rows that are configured in $this->_startRow and $this->_endRow
                if (($row == 1) || ($row >= $this->_startRow && $row < $this->_endRow)) {
                    return true;
                }

                return false;
            }

        }

        echo 'Loading file ', pathinfo($inputFileName, PATHINFO_BASENAME), ' using IOFactory with a defined reader type of ', $inputFileType, '<br />';
        /*  Create a new Reader of the type defined in $inputFileType  * */
        $reader = IOFactory::createReader($inputFileType);

        echo '<hr />';

        /*  Define how many rows we want to read for each "chunk"  * */
        $chunkSize = 100;
        /*  Create a new Instance of our Read Filter  * */
        $chunkFilter = new chunkReadFilter();

        /*  Tell the Reader that we want to use the Read Filter that we've Instantiated  * */
        /*    and that we want to store it in contiguous rows/columns  * */
        $reader->setReadFilter($chunkFilter)
            ->setContiguous(true);

        /*  Instantiate a new PhpSpreadsheet object manually  * */
        $spreadsheet = new Spreadsheet();

        /*  Set a sheet index  * */
        $sheet = 0;
        /*  Loop to read our worksheet in "chunk size" blocks  * */
        /**  $startRow is set to 2 initially because we always read the headings in row #1  * */
        for ($startRow = 2; $startRow <= 240; $startRow += $chunkSize) {
            echo 'Loading WorkSheet #', ($sheet + 1), ' using configurable filter for headings row 1 and for rows ', $startRow, ' to ', ($startRow + $chunkSize - 1), '<br />';
            /*  Tell the Read Filter, the limits on which rows we want to read this iteration  * */
            $chunkFilter->setRows($startRow, $chunkSize);

            /*  Increment the worksheet index pointer for the Reader  * */
            $reader->setSheetIndex($sheet);
            /*  Load only the rows that match our filter into a new worksheet in the PhpSpreadsheet Object  * */
            $reader->loadIntoExisting($inputFileName, $spreadsheet);
            /*  Set the worksheet title (to reference the "sheet" of data that we've loaded)  * */
            /*    and increment the sheet index as well  * */
            $spreadsheet->getActiveSheet()->setTitle('Country Data #' . ( ++$sheet));
        }

        echo '<hr />';

        echo $spreadsheet->getSheetCount(), ' worksheet', (($spreadsheet->getSheetCount() == 1) ? '' : 's'), ' loaded<br /><br />';
        $loadedSheetNames = $spreadsheet->getSheetNames();
        foreach ($loadedSheetNames as $sheetIndex => $loadedSheetName) {
            echo '<b>Worksheet #', $sheetIndex, ' -> ', $loadedSheetName, '</b><br />';
            $spreadsheet->setActiveSheetIndexByName($loadedSheetName);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, false, false, true);
            var_dump($sheetData);
            echo '<br />';
        }
        ?>
    <body>
</html>