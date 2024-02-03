<?php

use PhpOffice\PhpSpreadsheet\Helper\Html as HtmlHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

// Create new Spreadsheet object
$helper->log('Create new Spreadsheet object');
$spreadsheet = new Spreadsheet();

// Set document properties
$helper->log('Set document properties');
$spreadsheet->getProperties()->setCreator('Maarten Balliauw')
    ->setLastModifiedBy('Maarten Balliauw')
    ->setTitle('PhpSpreadsheet Test Document')
    ->setSubject('PhpSpreadsheet Test Document')
    ->setDescription('Test document for PhpSpreadsheet, generated using PHP classes.')
    ->setKeywords('office PhpSpreadsheet php')
    ->setCategory('Test result file');

// Add some data
$helper->log('Add some data');

$html1 = '<font color="#0000ff">
<h1 align="center">My very first example of rich text<br />generated from html markup</h1>
<p>
<font size="14" COLOR="rgb(0,255,128)">
<b>This block</b> contains an <i>italicized</i> word;
while this block uses an <u>underline</u>.
</font>
</p>
<p align="right"><font size="9" color="red" face="Times New Roman, serif">
I want to eat <ins><del>healthy food</del> <strong>pizza</strong></ins>.
</font>
';

$html2 = '<p>
<font color="#ff0000">
    100&deg;C is a hot temperature
</font>
<br>
<font color="#0080ff">
    10&deg;F is cold
</font>
</p>';

$html3 = '2<sup>3</sup> equals 8';

$html4 = 'H<sub>2</sub>SO<sub>4</sub> is the chemical formula for Sulphuric acid';

$html5 = '<strong>bold</strong>, <em>italic</em>, <strong><em>bold+italic</em></strong>';

$wizard = new HtmlHelper();
$richText = $wizard->toRichTextObject($html1);

$spreadsheet->getActiveSheet()
    ->setCellValue('A1', $richText);

$spreadsheet->getActiveSheet()
    ->getColumnDimension('A')
    ->setWidth(48);
$spreadsheet->getActiveSheet()
    ->getRowDimension(1)
    ->setRowHeight(-1);
$spreadsheet->getActiveSheet()->getStyle('A1')
    ->getAlignment()
    ->setWrapText(true);

$richText = $wizard->toRichTextObject($html2);

$spreadsheet->getActiveSheet()
    ->setCellValue('A2', $richText);

$spreadsheet->getActiveSheet()
    ->getRowDimension(1)
    ->setRowHeight(-1);
$spreadsheet->getActiveSheet()
    ->getStyle('A2')
    ->getAlignment()
    ->setWrapText(true);

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A3', $wizard->toRichTextObject($html3));

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A4', $wizard->toRichTextObject($html4));

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A5', $wizard->toRichTextObject($html5));

// Rename worksheet
$helper->log('Rename worksheet');
$spreadsheet->getActiveSheet()
    ->setTitle('Rich Text Examples');

// Save
$helper->write($spreadsheet, __FILE__);
