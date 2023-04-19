<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;

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
];

$spreadsheet->getActiveSheet()->fromArray($dataArray, null, 'A2');

// Create Table
$helper->log('Create Table');
$table = new Table('A1:D33', 'Sales_Data');

// Create Columns
$table->getColumn('D')->setShowFilterButton(false);
$table->getAutoFilter()->getColumn('A')
    ->setFilterType(AutoFilter\Column::AUTOFILTER_FILTERTYPE_CUSTOMFILTER)
    ->createRule()
    ->setRule(AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_GREATERTHANOREQUAL, 2011)
    ->setRuleType(AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_CUSTOMFILTER);

// Create Table Style
$helper->log('Create Table Style');
$tableStyle = new TableStyle();
$tableStyle->setTheme(TableStyle::TABLE_STYLE_MEDIUM2);
$tableStyle->setShowRowStripes(true);
$tableStyle->setShowColumnStripes(true);
$tableStyle->setShowFirstColumn(true);
$tableStyle->setShowLastColumn(true);
$table->setStyle($tableStyle);

// Add Table to Worksheet
$helper->log('Add Table to Worksheet');
$spreadsheet->getActiveSheet()->addTable($table);

$helper->displayGrid($spreadsheet->getActiveSheet()->toArray(null, true, true, true));

// Save
$helper->write($spreadsheet, __FILE__, ['Xlsx']);
