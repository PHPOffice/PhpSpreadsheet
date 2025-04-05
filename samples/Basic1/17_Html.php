<?php

require __DIR__ . '/../Header.php';
/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */
$spreadsheet = require __DIR__ . '/../templates/sampleSpreadsheet.php';
$spreadsheet->getProperties()->setTitle('Non-embedded images');

$helper->write($spreadsheet, __FILE__, ['Html']);
