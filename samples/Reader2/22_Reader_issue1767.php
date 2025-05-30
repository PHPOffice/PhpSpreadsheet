<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

require __DIR__ . '/../Header.php';
/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */
$helper->log('Start');

$inputFileType = 'Xlsx';
$inputFileName = __DIR__ . '/sampleData/issue.1767.xlsx';

$helper->log('Loading file ' . $inputFileName . ' using IOFactory with a defined reader type of ' . $inputFileType);
$reader = IOFactory::createReader($inputFileType);
$helper->log('Loading all WorkSheets');
$reader->setLoadAllSheets();
$spreadsheet = $reader->load($inputFileName);

// Save
$helper->write($spreadsheet, __FILE__);
$spreadsheet->disconnectWorksheets();

$helper->log('end');
