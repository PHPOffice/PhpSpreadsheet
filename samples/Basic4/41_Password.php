<?php

require __DIR__ . '/../Header.php';
/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */
$spreadsheet = require __DIR__ . '/../templates/sampleSpreadsheet.php';

// Set password against the spreadsheet file
$spreadsheet->getSecurity()->setLockWindows(true);
$spreadsheet->getSecurity()->setLockStructure(true);
$spreadsheet->getSecurity()->setWorkbookPassword('secret');

// Save
$helper->write($spreadsheet, __FILE__);
