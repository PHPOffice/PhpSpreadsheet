<?php

use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

// Create new Spreadsheet object
$helper->log('Create new Spreadsheet object');
$spreadsheet = new Spreadsheet();

// Set document properties
$helper->log('Set document properties');
$spreadsheet->getProperties()
    ->setCreator('PHPOffice')
    ->setLastModifiedBy('PHPOffice')
    ->setTitle('PhpSpreadsheet Test Document')
    ->setSubject('PhpSpreadsheet Test Document')
    ->setDescription('Test document for PhpSpreadsheet, generated using PHP classes.')
    ->setKeywords('Office PhpSpreadsheet php')
    ->setCategory('Test result file');
function transpose($value)
{
    return [$value];
}

// Add some data
$continentColumn = 'D';
$column = 'F';

// Set data for dropdowns
$continents = glob(__DIR__ . '/data/continents/*');
foreach ($continents as $key => $filename) {
    $continent = pathinfo($filename, PATHINFO_FILENAME);
    $helper->log("Loading $continent");
    $continent = str_replace(' ', '_', $continent);
    $countries = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $countryCount = count($countries);

    // Transpose $countries from a row to a column array
    $countries = array_map('transpose', $countries);
    $spreadsheet->getActiveSheet()
        ->fromArray($countries, null, $column . '1');
    $spreadsheet->addNamedRange(
        new NamedRange(
            $continent,
            $spreadsheet->getActiveSheet(),
            '$' . $column . '$1:$' . $column . '$' . $countryCount
        )
    );
    $spreadsheet->getActiveSheet()
        ->getColumnDimension($column)
        ->setVisible(false);

    $spreadsheet->getActiveSheet()
        ->setCellValue($continentColumn . ($key + 1), $continent);

    ++$column;
}

// Hide the dropdown data
$spreadsheet->getActiveSheet()
    ->getColumnDimension($continentColumn)
    ->setVisible(false);

$spreadsheet->addNamedRange(
    new NamedRange(
        'Continents',
        $spreadsheet->getActiveSheet(),
        '$' . $continentColumn . '$1:$' . $continentColumn . '$' . count($continents)
    )
);

// Set selection cells
$spreadsheet->getActiveSheet()
    ->setCellValue('A1', 'Continent:');
$spreadsheet->getActiveSheet()
    ->setCellValue('B1', 'Select continent');
$spreadsheet->getActiveSheet()
    ->setCellValue('B3', '=' . $column . 1);
$spreadsheet->getActiveSheet()
    ->setCellValue('B3', 'Select country');
$spreadsheet->getActiveSheet()
    ->getStyle('A1:A3')
    ->getFont()->setBold(true);

// Set linked validators
$validation = $spreadsheet->getActiveSheet()
    ->getCell('B1')
    ->getDataValidation();
$validation->setType(DataValidation::TYPE_LIST)
    ->setErrorStyle(DataValidation::STYLE_INFORMATION)
    ->setAllowBlank(false)
    ->setShowInputMessage(true)
    ->setShowErrorMessage(true)
    ->setShowDropDown(true)
    ->setErrorTitle('Input error')
    ->setError('Continent is not in the list.')
    ->setPromptTitle('Pick from the list')
    ->setPrompt('Please pick a continent from the drop-down list.')
    ->setFormula1('=Continents');

$spreadsheet->getActiveSheet()
    ->setCellValue('A3', 'Country:');
$spreadsheet->getActiveSheet()
    ->getStyle('A3')
    ->getFont()->setBold(true);

$validation = $spreadsheet->getActiveSheet()
    ->getCell('B3')
    ->getDataValidation();
$validation->setType(DataValidation::TYPE_LIST)
    ->setErrorStyle(DataValidation::STYLE_INFORMATION)
    ->setAllowBlank(false)
    ->setShowInputMessage(true)
    ->setShowErrorMessage(true)
    ->setShowDropDown(true)
    ->setErrorTitle('Input error')
    ->setError('Country is not in the list.')
    ->setPromptTitle('Pick from the list')
    ->setPrompt('Please pick a country from the drop-down list.')
    ->setFormula1('=INDIRECT($B$1)');

$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(30);

// Save
$helper->log('Not writing to Xls - formulae with defined names not yet supported');
$helper->write($spreadsheet, __FILE__, ['Xlsx']);
