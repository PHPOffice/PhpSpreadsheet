<?php

require __DIR__ . '/../Header.php';
/** @var PhpOffice\PhpSpreadsheet\Spreadsheet */
$spreadsheet = require __DIR__ . '/../templates/chartSpreadsheet.php';

// Save Excel 2007 file
/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */
$helper->write($spreadsheet, __FILE__, ['Xlsx'], true);
