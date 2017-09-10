<?php

// Create new Spreadsheet object
use PhpOffice\PhpSpreadsheet\Spreadsheet;

$helper->log('Create new Spreadsheet object');
$spreadsheet = new Spreadsheet();

// Set document properties
$helper->log('Set properties');
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
$spreadsheet->getActiveSheet()->setCellValue('A1', 'Firstname');
$spreadsheet->getActiveSheet()->setCellValue('B1', 'Lastname');
$spreadsheet->getActiveSheet()->setCellValue('C1', 'Phone');
$spreadsheet->getActiveSheet()->setCellValue('D1', 'Fax');
$spreadsheet->getActiveSheet()->setCellValue('E1', 'Is Client ?');

// Hide "Phone" and "fax" column
$helper->log("Hide 'Phone' and 'fax' columns");
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setVisible(false);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setVisible(false);

// Set outline levels
$helper->log('Set outline levels');
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setOutlineLevel(1)
        ->setVisible(false)
        ->setCollapsed(true);

// Freeze panes
$helper->log('Freeze panes');
$spreadsheet->getActiveSheet()->freezePane('A2');

// Rows to repeat at top
$helper->log('Rows to repeat at top');
$spreadsheet->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 1);

// Add data
for ($i = 2; $i <= 5000; ++$i) {
    $spreadsheet->getActiveSheet()->setCellValue('A' . $i, "FName $i")
            ->setCellValue('B' . $i, "LName $i")
            ->setCellValue('C' . $i, "PhoneNo $i")
            ->setCellValue('D' . $i, "FaxNo $i")
            ->setCellValue('E' . $i, true);
}

return $spreadsheet;
