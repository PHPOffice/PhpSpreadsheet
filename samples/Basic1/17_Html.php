<?php

require __DIR__ . '/../Header.php';
$spreadsheet = require __DIR__ . '/../templates/sampleSpreadsheet.php';
$spreadsheet->getProperties()->setTitle('Non-embedded images');

$helper->write($spreadsheet, __FILE__, ['Html']);
