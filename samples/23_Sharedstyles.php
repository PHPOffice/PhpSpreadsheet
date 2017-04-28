<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

require __DIR__ . '/Header.php';

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

// Add some data
$helper->log('Add some data');
$spreadsheet->setActiveSheetIndex(0);

$sharedStyle1 = new Style();
$sharedStyle2 = new Style();

$sharedStyle1->applyFromArray(
    ['fill' => [
                'type' => Fill::FILL_SOLID,
                'color' => ['argb' => 'FFCCFFCC'],
            ],
            'borders' => [
                'bottom' => ['style' => Border::BORDER_THIN],
                'right' => ['style' => Border::BORDER_MEDIUM],
            ],
        ]
);

$sharedStyle2->applyFromArray(
    ['fill' => [
                'type' => Fill::FILL_SOLID,
                'color' => ['argb' => 'FFFFFF00'],
            ],
            'borders' => [
                'bottom' => ['style' => Border::BORDER_THIN],
                'right' => ['style' => Border::BORDER_MEDIUM],
            ],
        ]
);

$spreadsheet->getActiveSheet()->duplicateStyle($sharedStyle1, 'A1:T100');
$spreadsheet->getActiveSheet()->duplicateStyle($sharedStyle2, 'C5:R95');

// Save
$helper->write($spreadsheet, __FILE__);
