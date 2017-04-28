<?php

use PhpOffice\PhpSpreadsheet\RichText;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

require __DIR__ . '/Header.php';

// Create new Spreadsheet object
$helper->log('Create new Spreadsheet object');
$spreadsheet = new Spreadsheet();

// Set document properties
$helper->log('Set document properties');
$spreadsheet->getProperties()
        ->setCreator('Maarten Balliauw')
        ->setLastModifiedBy('Maarten Balliauw')
        ->setTitle('Office 2007 XLSX Test Document')
        ->setSubject('Office 2007 XLSX Test Document')
        ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
        ->setKeywords('office 2007 openxml php')
        ->setCategory('Test result file');

// Set default font
$helper->log('Set default font');
$spreadsheet->getDefaultStyle()
        ->getFont()
        ->setName('Arial')
        ->setSize(10);

// Add some data, resembling some different data types
$helper->log('Add some data');
$spreadsheet->getActiveSheet()
        ->setCellValue('A1', 'String')
        ->setCellValue('B1', 'Simple')
        ->setCellValue('C1', 'PhpSpreadsheet');

$spreadsheet->getActiveSheet()
        ->setCellValue('A2', 'String')
        ->setCellValue('B2', 'Symbols')
        ->setCellValue('C2', '!+&=()~§±æþ');

$spreadsheet->getActiveSheet()
        ->setCellValue('A3', 'String')
        ->setCellValue('B3', 'UTF-8')
        ->setCellValue('C3', 'Создать MS Excel Книги из PHP скриптов');

$spreadsheet->getActiveSheet()
        ->setCellValue('A4', 'Number')
        ->setCellValue('B4', 'Integer')
        ->setCellValue('C4', 12);

$spreadsheet->getActiveSheet()
        ->setCellValue('A5', 'Number')
        ->setCellValue('B5', 'Float')
        ->setCellValue('C5', 34.56);

$spreadsheet->getActiveSheet()
        ->setCellValue('A6', 'Number')
        ->setCellValue('B6', 'Negative')
        ->setCellValue('C6', -7.89);

$spreadsheet->getActiveSheet()
        ->setCellValue('A7', 'Boolean')
        ->setCellValue('B7', 'True')
        ->setCellValue('C7', true);

$spreadsheet->getActiveSheet()
        ->setCellValue('A8', 'Boolean')
        ->setCellValue('B8', 'False')
        ->setCellValue('C8', false);

$dateTimeNow = time();
$spreadsheet->getActiveSheet()
        ->setCellValue('A9', 'Date/Time')
        ->setCellValue('B9', 'Date')
        ->setCellValue('C9', Date::PHPToExcel($dateTimeNow));
$spreadsheet->getActiveSheet()
        ->getStyle('C9')
        ->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD2);

$spreadsheet->getActiveSheet()
        ->setCellValue('A10', 'Date/Time')
        ->setCellValue('B10', 'Time')
        ->setCellValue('C10', Date::PHPToExcel($dateTimeNow));
$spreadsheet->getActiveSheet()
        ->getStyle('C10')
        ->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_DATE_TIME4);

$spreadsheet->getActiveSheet()
        ->setCellValue('A11', 'Date/Time')
        ->setCellValue('B11', 'Date and Time')
        ->setCellValue('C11', Date::PHPToExcel($dateTimeNow));
$spreadsheet->getActiveSheet()
        ->getStyle('C11')
        ->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_DATE_DATETIME);

$spreadsheet->getActiveSheet()
        ->setCellValue('A12', 'NULL')
        ->setCellValue('C12', null);

$richText = new RichText();
$richText->createText('你好 ');

$payable = $richText->createTextRun('你 好 吗？');
$payable->getFont()->setBold(true);
$payable->getFont()->setItalic(true);
$payable->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));

$richText->createText(', unless specified otherwise on the invoice.');

$spreadsheet->getActiveSheet()
        ->setCellValue('A13', 'Rich Text')
        ->setCellValue('C13', $richText);

$richText2 = new RichText();
$richText2->createText("black text\n");

$red = $richText2->createTextRun('red text');
$red->getFont()->setColor(new Color(Color::COLOR_RED));

$spreadsheet->getActiveSheet()
        ->getCell('C14')
        ->setValue($richText2);
$spreadsheet->getActiveSheet()
        ->getStyle('C14')
        ->getAlignment()->setWrapText(true);

$spreadsheet->getActiveSheet()->setCellValue('A17', 'Hyperlink');

$spreadsheet->getActiveSheet()
        ->setCellValue('C17', 'PhpSpreadsheet Web Site');
$spreadsheet->getActiveSheet()
        ->getCell('C17')
        ->getHyperlink()
        ->setUrl('https://github.com/PHPOffice/PhpSpreadsheet')
        ->setTooltip('Navigate to PhpSpreadsheet website');

$spreadsheet->getActiveSheet()
        ->setCellValue('C18', '=HYPERLINK("mailto:abc@def.com","abc@def.com")');

$spreadsheet->getActiveSheet()
        ->getColumnDimension('B')
        ->setAutoSize(true);
$spreadsheet->getActiveSheet()
        ->getColumnDimension('C')
        ->setAutoSize(true);

// Rename worksheet
$helper->log('Rename worksheet');
$spreadsheet->getActiveSheet()->setTitle('Datatypes');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Save
$helper->write($spreadsheet, __FILE__);
