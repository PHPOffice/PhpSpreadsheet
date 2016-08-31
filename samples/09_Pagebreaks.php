<?php

require __DIR__ . '/Header.php';

// Create new Spreadsheet object
$helper->log('Create new Spreadsheet object');
$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

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
$helper->log('Add data and page breaks');
$spreadsheet->setActiveSheetIndex(0);
$spreadsheet->getActiveSheet()->setCellValue('A1', 'Firstname')
        ->setCellValue('B1', 'Lastname')
        ->setCellValue('C1', 'Phone')
        ->setCellValue('D1', 'Fax')
        ->setCellValue('E1', 'Is Client ?');

// Add data
for ($i = 2; $i <= 50; ++$i) {
    $spreadsheet->getActiveSheet()->setCellValue('A' . $i, "FName $i");
    $spreadsheet->getActiveSheet()->setCellValue('B' . $i, "LName $i");
    $spreadsheet->getActiveSheet()->setCellValue('C' . $i, "PhoneNo $i");
    $spreadsheet->getActiveSheet()->setCellValue('D' . $i, "FaxNo $i");
    $spreadsheet->getActiveSheet()->setCellValue('E' . $i, true);

    // Add page breaks every 10 rows
    if ($i % 10 == 0) {
        // Add a page break
        $spreadsheet->getActiveSheet()->setBreak('A' . $i, \PhpOffice\PhpSpreadsheet\Worksheet::BREAK_ROW);
    }
}

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);
$spreadsheet->getActiveSheet()->setTitle('Printing Options');

// Set print headers
$spreadsheet->getActiveSheet()
        ->getHeaderFooter()->setOddHeader('&C&24&K0000FF&B&U&A');
$spreadsheet->getActiveSheet()
        ->getHeaderFooter()->setEvenHeader('&C&24&K0000FF&B&U&A');

// Set print footers
$spreadsheet->getActiveSheet()
        ->getHeaderFooter()->setOddFooter('&R&D &T&C&F&LPage &P / &N');
$spreadsheet->getActiveSheet()
        ->getHeaderFooter()->setEvenFooter('&L&D &T&C&F&RPage &P / &N');

// Save
$helper->write($spreadsheet, __FILE__);
