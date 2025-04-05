<?php

require __DIR__ . '/../Header.php';
/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */
$spreadsheet = require __DIR__ . '/../templates/sampleSpreadsheet.php';

// Save
$helper->write($spreadsheet, __FILE__);
