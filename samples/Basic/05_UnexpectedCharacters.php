<?php

require __DIR__ . '/../Header.php';
$spreadsheet = require __DIR__ . '/../templates/sampleSpreadsheet2.php';

// Save
$helper->write($spreadsheet, __FILE__);
