<?php

use PhpOffice\PhpSpreadsheet\Document\Properties;
use PhpOffice\PhpSpreadsheet\IOFactory;

require __DIR__ . '/../Header.php';

$inputFileType = 'Xls';
$inputFileName = __DIR__ . '/../templates/31docproperties.xls';

$spreadsheetReader = IOFactory::createReader($inputFileType);
$callStartTime = microtime(true);
$spreadsheet = $spreadsheetReader->load($inputFileName);
$helper->logRead($inputFileType, $inputFileName, $callStartTime);

$helper->log('Adjust properties');
$spreadsheet->getProperties()->setTitle('Office 95 XLS Test Document')
    ->setSubject('Office 95 XLS Test Document')
    ->setDescription('Test XLS document, generated using PhpSpreadsheet')
    ->setKeywords('office 95 biff php');

// Save Excel 95 file
$filename = $helper->getFilename(__FILE__, 'xls');
$writer = IOFactory::createWriter($spreadsheet, 'Xls');
$callStartTime = microtime(true);
$writer->save($filename);
$helper->logWrite($writer, $filename, $callStartTime);

$helper->logEndingNotes();

// Reread File
$helper->log('Reread Xls file');
$spreadsheetRead = IOFactory::load($filename);

// Set properties
$helper->log('Get properties');

$helper->log('Core Properties:');
$helper->log('    Created by - ' . $spreadsheet->getProperties()->getCreator());
$helper->log('    Created on - ' . date('d-M-Y' . $spreadsheet->getProperties()->getCreated()) . ' at ' . date('H:i:s' . $spreadsheet->getProperties()->getCreated()));
$helper->log('    Last Modified by - ' . $spreadsheet->getProperties()->getLastModifiedBy());
$helper->log('    Last Modified on - ' . date('d-M-Y' . $spreadsheet->getProperties()->getModified()) . ' at ' . date('H:i:s' . $spreadsheet->getProperties()->getModified()));
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
    if ($propertyType == Properties::PROPERTY_TYPE_DATE) {
        $formattedValue = date('d-M-Y H:i:s', (int) $propertyValue);
    } elseif ($propertyType == Properties::PROPERTY_TYPE_BOOLEAN) {
        $formattedValue = $propertyValue ? 'TRUE' : 'FALSE';
    } else {
        $formattedValue = $propertyValue;
    }
    $helper->log('    ' . $customProperty . ' - (' . $propertyType . ') - ' . $formattedValue);
}

$helper->logEndingNotes();
