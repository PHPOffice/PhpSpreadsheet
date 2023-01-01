<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

require __DIR__ . '/../Header.php';

$helper->log('Start');

$inputFileType = 'Xlsx';
$inputFileName = __DIR__ . '/sampleData/formscomments.xlsx';

$helper->log('Loading file ' . $inputFileName . ' using IOFactory with a defined reader type of ' . $inputFileType);
$reader = IOFactory::createReader($inputFileType);
$helper->log('Loading all WorkSheets');
$reader->setLoadAllSheets();
$spreadsheet = $reader->load($inputFileName);

// Save
$helper->write($spreadsheet, __FILE__, ['Xlsx']);
$spreadsheet->disconnectWorksheets();

$helper->log('end');
