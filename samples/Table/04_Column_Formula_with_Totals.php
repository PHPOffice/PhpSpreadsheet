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

$columnFormula = '=SUM(Sales_Data[[#This Row],[Q1]:[Q4]])';

$dataArray = [
    ['Year', 'Country', 'Q1', 'Q2', 'Q3', 'Q4', 'Sales'],
    [2010, 'Belgium', 380, 390, 420, 460, $columnFormula],
    [2010, 'France', 510, 490, 460, 590, $columnFormula],
    [2010, 'Germany', 720, 680, 640, 660, $columnFormula],
    [2010, 'Italy', 440, 410, 420, 450, $columnFormula],
    [2010, 'Spain', 510, 490, 470, 420, $columnFormula],
    [2010, 'UK', 690, 610, 620, 600, $columnFormula],
    [2010, 'United States', 790, 730, 860, 850, $columnFormula],
    [2011, 'Belgium', 400, 350, 450, 500, $columnFormula],
    [2011, 'France', 620, 650, 415, 570, $columnFormula],
    [2011, 'Germany', 680, 620, 710, 690, $columnFormula],
    [2011, 'Italy', 430, 370, 350, 335, $columnFormula],
    [2011, 'Spain', 460, 390, 430, 415, $columnFormula],
    [2011, 'UK', 720, 650, 580, 510, $columnFormula],
    [2011, 'United States', 800, 700, 900, 950, $columnFormula],
];

$spreadsheet->getActiveSheet()->fromArray($dataArray, null, 'A1');

// Create Table
$helper->log('Create Table');
$table = new Table('A1:G16', 'Sales_Data'); // +1 row for totalsRow
$table->setShowTotalsRow(true);
$table->setRange('A1:G16'); // +1 row for totalsRow

// Set Column Formula
$table->getColumn('G')->setColumnFormula($columnFormula);

$helper->log('Add Totals Row');
// Table column label not implemented yet,
$table->getColumn('A')->setTotalsRowLabel('Total');
// So set the label directly to the cell
$spreadsheet->getActiveSheet()->getCell('A16')->setValue('Total');

// Table column function not implemented yet,
$table->getColumn('G')->setTotalsRowFunction('sum');
// So set the formula directly to the cell
$spreadsheet->getActiveSheet()->getCell('G16')->setValue('=SUBTOTAL(109,Sales_Data[Sales])');

// Table column function not implemented yet,
$table->getColumn('C')->setTotalsRowFunction('sum');
$table->getColumn('D')->setTotalsRowFunction('sum');
$table->getColumn('E')->setTotalsRowFunction('sum');
$table->getColumn('F')->setTotalsRowFunction('sum');
// So set the formulae directly to the cells
$spreadsheet->getActiveSheet()->getCell('C16')->setValue('=SUBTOTAL(109,Sales_Data[Q1])');
$spreadsheet->getActiveSheet()->getCell('D16')->setValue('=SUBTOTAL(109,Sales_Data[Q2])');
$spreadsheet->getActiveSheet()->getCell('E16')->setValue('=SUBTOTAL(109,Sales_Data[Q3])');
$spreadsheet->getActiveSheet()->getCell('F16')->setValue('=SUBTOTAL(109,Sales_Data[Q4])');

// Add Table to Worksheet
$helper->log('Add Table to Worksheet');
$spreadsheet->getActiveSheet()->addTable($table);

$helper->displayGrid($spreadsheet->getActiveSheet()->toArray(null, false, true, true));

$helper->log('Calculate Structured References');

$helper->displayGrid($spreadsheet->getActiveSheet()->toArray(null, true, true, true));

// Save
$helper->write($spreadsheet, __FILE__, ['Xlsx']);
