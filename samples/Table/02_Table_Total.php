<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;

require __DIR__ . '/../Header.php';

// Create new Spreadsheet object
$helper->log('Create new Spreadsheet object');
$spreadsheet = new Spreadsheet();

// Set document properties
$helper->log('Set document properties');
$spreadsheet->getProperties()->setCreator('aswinkumar863')
    ->setLastModifiedBy('aswinkumar863')
    ->setTitle('PhpSpreadsheet Table Test Document')
    ->setSubject('PhpSpreadsheet Table Test Document')
    ->setDescription('Test document for PhpSpreadsheet, generated using PHP classes.')
    ->setKeywords('office PhpSpreadsheet php')
    ->setCategory('Table');

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
];

$spreadsheet->getActiveSheet()->fromArray($dataArray, null, 'A2');

// Table
$helper->log('Create Table');
$table = new Table();
$table->setName('SalesData');
$table->setShowTotalsRow(true);
$table->setRange('A1:D18'); // +1 row for totalsRow

$helper->log('Add Totals Row');
// Table column label not implemented yet,
$table->getColumn('A')->setTotalsRowLabel('Total');
// So set the label directly to the cell
$spreadsheet->getActiveSheet()->getCell('A18')->setValue('Total');

// Table column function not implemented yet,
$table->getColumn('D')->setTotalsRowFunction('sum');
// So set the formula directly to the cell
$spreadsheet->getActiveSheet()->getCell('D18')->setValue('=SUBTOTAL(109,SalesData[Sales])');

// Add Table to Worksheet
$helper->log('Add Table to Worksheet');
$spreadsheet->getActiveSheet()->addTable($table);

$helper->displayGrid($spreadsheet->getActiveSheet()->toArray(null, false, true, true));

$helper->log('Calculate Structured References');

$helper->displayGrid($spreadsheet->getActiveSheet()->toArray(null, true, true, true));

// Save
$helper->write($spreadsheet, __FILE__, ['Xlsx']);
