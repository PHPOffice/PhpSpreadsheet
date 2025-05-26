<?php

use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */
$helper->log('Create new Spreadsheet object');
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$rtf = new RichText();
$rtf->createText('~Cell Style~');
$rtf->createTextRun('~RTF Style~')->getFont()?->setItalic(true);
$rtf->createText('~No Style~');

$sheet->getCell('A1')->setValue($rtf);
$sheet->getStyle('A1')->getFont()->setBold(true);

// Save
$helper->write($spreadsheet, __FILE__, ['Xlsx', 'Xls', 'Html']);
