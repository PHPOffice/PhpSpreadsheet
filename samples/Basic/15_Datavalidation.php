<?php

use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

// Create new Spreadsheet object
$helper->log('Create new Spreadsheet object');
$spreadsheet = new Spreadsheet();

// Set document properties
$helper->log('Set document properties');
$spreadsheet->getProperties()->setCreator('Maarten Balliauw')
        ->setLastModifiedBy('Maarten Balliauw')
        ->setTitle('Office 2007 XLSX Test Document')
        ->setSubject('Office 2007 XLSX Test Document')
        ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
        ->setKeywords('office 2007 openxml php')
        ->setCategory('Test result file');

// Create a first sheet
$helper->log('Add data');
$spreadsheet->setActiveSheetIndex(0);
$spreadsheet->getActiveSheet()->setCellValue('A1', 'Cell B3 and B5 contain data validation...')
        ->setCellValue('A3', 'Number:')
        ->setCellValue('B3', '10')
        ->setCellValue('A5', 'List:')
        ->setCellValue('B5', 'Item A')
        ->setCellValue('A7', 'List #2:')
        ->setCellValue('B7', 'Item #2')
        ->setCellValue('D2', 'Item #1')
        ->setCellValue('D3', 'Item #2')
        ->setCellValue('D4', 'Item #3')
        ->setCellValue('D5', 'Item #4')
        ->setCellValue('D6', 'Item #5');

// Set data validation
$helper->log('Set data validation');
$validation = $spreadsheet->getActiveSheet()->getCell('B3')->getDataValidation();
$validation->setType(DataValidation::TYPE_WHOLE);
$validation->setErrorStyle(DataValidation::STYLE_STOP);
$validation->setAllowBlank(true);
$validation->setShowInputMessage(true);
$validation->setShowErrorMessage(true);
$validation->setErrorTitle('Input error');
$validation->setError('Only numbers between 10 and 20 are allowed!');
$validation->setPromptTitle('Allowed input');
$validation->setPrompt('Only numbers between 10 and 20 are allowed.');
$validation->setFormula1(10);
$validation->setFormula2(20);

$validation = $spreadsheet->getActiveSheet()->getCell('B5')->getDataValidation();
$validation->setType(DataValidation::TYPE_LIST);
$validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
$validation->setAllowBlank(false);
$validation->setShowInputMessage(true);
$validation->setShowErrorMessage(true);
$validation->setShowDropDown(true);
$validation->setErrorTitle('Input error');
$validation->setError('Value is not in list.');
$validation->setPromptTitle('Pick from list');
$validation->setPrompt('Please pick a value from the drop-down list.');
$validation->setFormula1('"Item A,Item B,Item C"'); // Make sure to put the list items between " and " if your list is simply a comma-separated list of values !!!

$validation = $spreadsheet->getActiveSheet()->getCell('B7')->getDataValidation();
$validation->setType(DataValidation::TYPE_LIST);
$validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
$validation->setAllowBlank(false);
$validation->setShowInputMessage(true);
$validation->setShowErrorMessage(true);
$validation->setShowDropDown(true);
$validation->setErrorTitle('Input error');
$validation->setError('Value is not in list.');
$validation->setPromptTitle('Pick from list');
$validation->setPrompt('Please pick a value from the drop-down list.');
$validation->setFormula1('$D$2:$D$6'); // Make sure NOT to put a range of cells or a formula between " and "

// Save
$helper->write($spreadsheet, __FILE__);
