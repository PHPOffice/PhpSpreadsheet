<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooter;
use PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooterDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

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

// Add some data, we will use printing features
$helper->log('Add some data');
for ($i = 1; $i < 200; ++$i) {
    $spreadsheet->getActiveSheet()->setCellValue('A' . $i, $i);
    $spreadsheet->getActiveSheet()->setCellValue('B' . $i, 'Test value');
}

// Set header and footer. When no different headers for odd/even are used, odd header is assumed.
$helper->log('Set header/footer');
$spreadsheet->getActiveSheet()
        ->getHeaderFooter()
        ->setOddHeader('&L&G&C&HPlease treat this document as confidential!');
$spreadsheet->getActiveSheet()
        ->getHeaderFooter()
        ->setOddFooter('&L&B' . $spreadsheet->getProperties()->getTitle() . '&RPage &P of &N');

// Add a drawing to the header
$helper->log('Add a drawing to the header');
$drawing = new HeaderFooterDrawing();
$drawing->setName('PhpSpreadsheet logo');
$drawing->setPath(__DIR__ . '/../images/PhpSpreadsheet_logo.png');
$drawing->setHeight(36);
$spreadsheet->getActiveSheet()
        ->getHeaderFooter()
        ->addImage($drawing, HeaderFooter::IMAGE_HEADER_LEFT);

// Set page orientation and size
$helper->log('Set page orientation and size');
$spreadsheet->getActiveSheet()
        ->getPageSetup()
        ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
$spreadsheet->getActiveSheet()
        ->getPageSetup()
        ->setPaperSize(PageSetup::PAPERSIZE_A4);

// Rename worksheet
$helper->log('Rename worksheet');
$spreadsheet->getActiveSheet()->setTitle('Printing');

// Save
$helper->write($spreadsheet, __FILE__);
