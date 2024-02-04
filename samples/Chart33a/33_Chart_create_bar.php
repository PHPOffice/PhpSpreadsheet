<?php

require __DIR__ . '/../Header.php';

$spreadsheet = require __DIR__ . '/../templates/chartSpreadsheet.php';

// Save Excel 2007 file
$helper->write($spreadsheet, __FILE__, ['Xlsx'], true);
