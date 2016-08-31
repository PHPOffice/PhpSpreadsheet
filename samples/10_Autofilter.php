<?php

require __DIR__ . '/Header.php';

// Create new Spreadsheet object
$helper->log('Create new Spreadsheet object');
$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

// Set document properties
$helper->log('Set document properties');
$spreadsheet->getProperties()->setCreator('Maarten Balliauw')
        ->setLastModifiedBy('Maarten Balliauw')
        ->setTitle('PhpSpreadsheet Test Document')
        ->setSubject('PhpSpreadsheet Test Document')
        ->setDescription('Test document for PhpSpreadsheet, generated using PHP classes.')
        ->setKeywords('office PhpSpreadsheet php')
        ->setCategory('Test result file');

// Create the worksheet
$helper->log('Add data');
$spreadsheet->setActiveSheetIndex(0);
$spreadsheet->getActiveSheet()->setCellValue('A1', 'Year')
        ->setCellValue('B1', 'Quarter')
        ->setCellValue('C1', 'Country')
        ->setCellValue('D1', 'Sales');

$dataArray = [
    ['2010', 'Q1', 'United States', 790],
    ['2010', 'Q2', 'United States', 730],
    ['2010', 'Q3', 'United States', 860],
    ['2010', 'Q4', 'United States', 850],
    ['2011', 'Q1', 'United States', 800],
    ['2011', 'Q2', 'United States', 700],
    ['2011', 'Q3', 'United States', 900],
    ['2011', 'Q4', 'United States', 950],
    ['2010', 'Q1', 'Belgium', 380],
    ['2010', 'Q2', 'Belgium', 390],
    ['2010', 'Q3', 'Belgium', 420],
    ['2010', 'Q4', 'Belgium', 460],
    ['2011', 'Q1', 'Belgium', 400],
    ['2011', 'Q2', 'Belgium', 350],
    ['2011', 'Q3', 'Belgium', 450],
    ['2011', 'Q4', 'Belgium', 500],
    ['2010', 'Q1', 'UK', 690],
    ['2010', 'Q2', 'UK', 610],
    ['2010', 'Q3', 'UK', 620],
    ['2010', 'Q4', 'UK', 600],
    ['2011', 'Q1', 'UK', 720],
    ['2011', 'Q2', 'UK', 650],
    ['2011', 'Q3', 'UK', 580],
    ['2011', 'Q4', 'UK', 510],
    ['2010', 'Q1', 'France', 510],
    ['2010', 'Q2', 'France', 490],
    ['2010', 'Q3', 'France', 460],
    ['2010', 'Q4', 'France', 590],
    ['2011', 'Q1', 'France', 620],
    ['2011', 'Q2', 'France', 650],
    ['2011', 'Q3', 'France', 415],
    ['2011', 'Q4', 'France', 570],
    ['2010', 'Q1', 'Germany', 720],
    ['2010', 'Q2', 'Germany', 680],
    ['2010', 'Q3', 'Germany', 640],
    ['2010', 'Q4', 'Germany', 660],
    ['2011', 'Q1', 'Germany', 680],
    ['2011', 'Q2', 'Germany', 620],
    ['2011', 'Q3', 'Germany', 710],
    ['2011', 'Q4', 'Germany', 690],
    ['2010', 'Q1', 'Spain', 510],
    ['2010', 'Q2', 'Spain', 490],
    ['2010', 'Q3', 'Spain', 470],
    ['2010', 'Q4', 'Spain', 420],
    ['2011', 'Q1', 'Spain', 460],
    ['2011', 'Q2', 'Spain', 390],
    ['2011', 'Q3', 'Spain', 430],
    ['2011', 'Q4', 'Spain', 415],
    ['2010', 'Q1', 'Italy', 440],
    ['2010', 'Q2', 'Italy', 410],
    ['2010', 'Q3', 'Italy', 420],
    ['2010', 'Q4', 'Italy', 450],
    ['2011', 'Q1', 'Italy', 430],
    ['2011', 'Q2', 'Italy', 370],
    ['2011', 'Q3', 'Italy', 350],
    ['2011', 'Q4', 'Italy', 335],
];
$spreadsheet->getActiveSheet()->fromArray($dataArray, null, 'A2');

// Set title row bold
$helper->log('Set title row bold');
$spreadsheet->getActiveSheet()->getStyle('A1:D1')->getFont()->setBold(true);

// Set autofilter
$helper->log('Set autofilter');
// Always include the complete filter range!
// Excel does support setting only the caption
// row, but that's not a best practise...
$spreadsheet->getActiveSheet()->setAutoFilter($spreadsheet->getActiveSheet()->calculateWorksheetDimension());

// Save
$helper->write($spreadsheet, __FILE__);
