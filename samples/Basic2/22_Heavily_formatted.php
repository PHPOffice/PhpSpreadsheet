<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

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

// Add some data
$helper->log('Add some data');
$spreadsheet->setActiveSheetIndex(0);

$spreadsheet->getActiveSheet()->getStyle('A1:T100')->applyFromArray(
    ['fill' => [
        'fillType' => Fill::FILL_SOLID,
        'color' => ['argb' => 'FFCCFFCC'],
    ],
        'borders' => [
            'bottom' => ['borderStyle' => Border::BORDER_THIN],
            'right' => ['borderStyle' => Border::BORDER_MEDIUM],
        ],
    ]
);

$spreadsheet->getActiveSheet()->getStyle('C5:R95')->applyFromArray(
    ['fill' => [
        'fillType' => Fill::FILL_SOLID,
        'color' => ['argb' => 'FFFFFF00'],
    ],
    ]
);

// Save
$helper->write($spreadsheet, __FILE__);
