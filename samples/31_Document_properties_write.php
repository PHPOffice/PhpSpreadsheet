<?php

require __DIR__ . '/Header.php';

$inputFileType = 'Xlsx';
$inputFileName = __DIR__ . '/templates/31docproperties.xlsx';

$spreadsheetReader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
$callStartTime = microtime(true);
$spreadsheet = $spreadsheetReader->load($inputFileName);
$helper->logRead($inputFileType, $inputFileName, $callStartTime);

$helper->log('Adjust properties');
$spreadsheet->getProperties()->setTitle('Office 2007 XLSX Test Document')
        ->setSubject('Office 2007 XLSX Test Document')
        ->setDescription('Test XLSX document, generated using PhpSpreadsheet')
        ->setKeywords('office 2007 openxml php');

// Save Excel 2007 file
$filename = $helper->getFilename(__FILE__);
$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
$callStartTime = microtime(true);
$writer->save($filename);
$helper->logWrite($writer, $filename, $callStartTime);

// Echo memory peak usage
$helper->logEndingNotes();

// Reread File
$helper->log('Reread Xlsx file');
$spreadsheetRead = \PhpOffice\PhpSpreadsheet\IOFactory::load($filename);

// Set properties
$helper->log('Get properties');

$helper->log('Core Properties:');
$helper->log('    Created by - ' . $spreadsheet->getProperties()->getCreator());
$helper->log('    Created on - ' . date('d-M-Y', $spreadsheet->getProperties()->getCreated()) . ' at ' . date('H:i:s', $spreadsheet->getProperties()->getCreated()));
$helper->log('    Last Modified by - ' . $spreadsheet->getProperties()->getLastModifiedBy());
$helper->log('    Last Modified on - ' . date('d-M-Y', $spreadsheet->getProperties()->getModified()) . ' at ' . date('H:i:s', $spreadsheet->getProperties()->getModified()));
$helper->log('    Title - ' . $spreadsheet->getProperties()->getTitle());
$helper->log('    Subject - ' . $spreadsheet->getProperties()->getSubject());
$helper->log('    Description - ' . $spreadsheet->getProperties()->getDescription());
$helper->log('    Keywords: - ' . $spreadsheet->getProperties()->getKeywords());

$helper->log('Extended (Application) Properties:');
$helper->log('    Category - ' . $spreadsheet->getProperties()->getCategory());
$helper->log('    Company - ' . $spreadsheet->getProperties()->getCompany());
$helper->log('    Manager - ' . $spreadsheet->getProperties()->getManager());

$helper->log('Custom Properties:');
$customProperties = $spreadsheet->getProperties()->getCustomProperties();
foreach ($customProperties as $customProperty) {
    $propertyValue = $spreadsheet->getProperties()->getCustomPropertyValue($customProperty);
    $propertyType = $spreadsheet->getProperties()->getCustomPropertyType($customProperty);
    if ($propertyType == \PhpOffice\PhpSpreadsheet\Document\Properties::PROPERTY_TYPE_DATE) {
        $formattedValue = date('d-M-Y H:i:s', $propertyValue);
    } elseif ($propertyType == \PhpOffice\PhpSpreadsheet\Document\Properties::PROPERTY_TYPE_BOOLEAN) {
        $formattedValue = $propertyValue ? 'TRUE' : 'FALSE';
    } else {
        $formattedValue = $propertyValue;
    }
    $helper->log('    ' . $customProperty . ' - (' . $propertyType . ') - ' . $formattedValue);
}

// Echo memory peak usage
$helper->logEndingNotes();
