<?php

require __DIR__ . '/../Header.php';

use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\RichText\TextElement;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

$spreadsheet = new Spreadsheet();

$helper->log('First sheet - protected, sorts not allowed');
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('sorttrue');
$sheet->getCell('A1')->setValue(10);
$sheet->getCell('A2')->setValue(5);
$sheet->getCell('B1')->setValue(15);
$protection = $sheet->getProtection();
$protection->setPassword('testpassword');
$protection->setSheet(true);
$protection->setInsertRows(true);
$protection->setFormatCells(true);
$protection->setObjects(true);
$protection->setAutoFilter(false);
$protection->setSort(true);
$comment = $sheet->getComment('A1');
$text = new RichText();
$text->addText(new TextElement('Sort options should be grayed out. Sheet password to remove protections is testpassword for all sheets.'));
$comment->setText($text)->setHeight('120pt')->setWidth('120pt');

$helper->log('Second sheet - protected, sorts allowed, but no permitted range defined');
$sheet = $spreadsheet->createSheet();
$sheet->setTitle('sortfalse');
$sheet->getCell('A1')->setValue(10);
$sheet->getCell('A2')->setValue(5);
$sheet->getCell('B1')->setValue(15);
$protection = $sheet->getProtection();
$protection->setPassword('testpassword');
$protection->setSheet(true);
$protection->setInsertRows(true);
$protection->setFormatCells(true);
$protection->setObjects(true);
$protection->setAutoFilter(false);
$protection->setSort(false);
$comment = $sheet->getComment('A1');
$text = new RichText();
$text->addText(new TextElement('Sort options not grayed out, but no permissible sort range.'));
$comment->setText($text)->setHeight('120pt')->setWidth('120pt');

$helper->log('Third sheet - protected, sorts allowed, but only on permitted range A:A, no range password needed');
$sheet = $spreadsheet->createSheet();
$sheet->setTitle('sortfalsenocolpw');
$sheet->getCell('A1')->setValue(10);
$sheet->getCell('A2')->setValue(5);
$sheet->getCell('C1')->setValue(15);
$protection = $sheet->getProtection();
$protection->setPassword('testpassword');
$protection->setSheet(true);
$protection->setInsertRows(true);
$protection->setFormatCells(true);
$protection->setObjects(true);
$protection->setAutoFilter(false);
$protection->setSort(false);
$sheet->protectCells('A:A');
$comment = $sheet->getComment('A1');
$text = new RichText();
$text->addText(new TextElement('Column A may be sorted without a password. No sort for any other column.'));
$comment->setText($text)->setHeight('120pt')->setWidth('120pt');

$helper->log('Fourth sheet - protected, sorts allowed, but only on permitted range A:A, and range password needed');
$sheet = $spreadsheet->createSheet();
$sheet->setTitle('sortfalsecolpw');
$sheet->getCell('A1')->setValue(10);
$sheet->getCell('A2')->setValue(5);
$sheet->getCell('C1')->setValue(15);
$protection = $sheet->getProtection();
$protection->setPassword('testpassword');
$protection->setSheet(true);
$protection->setInsertRows(true);
$protection->setFormatCells(true);
$protection->setObjects(true);
$protection->setAutoFilter(false);
$protection->setSort(false);
$sheet->protectCells('A:A', 'sortpw', false, 'sortrange');
$comment = $sheet->getComment('A1');
$text = new RichText();
$text->addText(new TextElement('Column A may be sorted with password sortpw. No sort for any other column.'));
$comment->setText($text)->setHeight('120pt')->setWidth('120pt');

// Save
$helper->write($spreadsheet, __FILE__, ['Xls', 'Xlsx']);
$spreadsheet->disconnectWorksheets();
