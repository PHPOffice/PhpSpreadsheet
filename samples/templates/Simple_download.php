<?php

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require_once __DIR__ . '/../Bootstrap.php';

$helper = new Sample();
if ($helper->isCli()) {
    $helper->log('This example should only be run from a Web Browser' . PHP_EOL);
    $spreadsheet = null;

    return;
}

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();

// Set document properties
$spreadsheet->getProperties()->setCreator('Maarten Balliauw')
    ->setTitle('Sample Download Test Document')
    ->setSubject('Sample Download Test Document')
    ->setDescription('Test document for PhpSpreadsheet Sample Downloads.')
    ->setKeywords('PhpSpreadsheet Download Sample')
    ->setCategory('Test result file');

// Add some data
$sheet = $spreadsheet->getActiveSheet();
$sheet
    ->setCellValue('A1', 'Hello')
    ->setCellValue('B2', 'world!')
    ->setCellValue('C1', 'Hello')
    ->setCellValue('D2', 'world!');

// Miscellaneous glyphs, UTF-8
$sheet
    ->setCellValue('A4', 'Miscellaneous glyphs')
    ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');

// Rename worksheet
$sheet->setTitle('Simple');
