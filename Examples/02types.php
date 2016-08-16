<?php
/**
 * PhpSpreadsheet
 *
 * Copyright (c) 2006 - 2016 PhpSpreadsheet
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PhpSpreadsheet
 * @copyright  Copyright (c) 2006 - 2016 PhpSpreadsheet (https://github.com/PHPOffice/PhpSpreadsheet)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE); 
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

/** Include PhpSpreadsheet */
require_once dirname(__FILE__) . '/../src/Bootstrap.php';


// Create new PhpSpreadsheet object
echo date('H:i:s') , " Create new PhpSpreadsheet object" , EOL;
$objPhpSpreadsheet = new \PhpSpreadsheet\Spreadsheet();

// Set document properties
echo date('H:i:s') , " Set document properties" , EOL;
$objPhpSpreadsheet->getProperties()
    ->setCreator("Maarten Balliauw")
	->setLastModifiedBy("Maarten Balliauw")
	->setTitle("Office 2007 XLSX Test Document")
	->setSubject("Office 2007 XLSX Test Document")
	->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
	->setKeywords("office 2007 openxml php")
	->setCategory("Test result file");

// Set default font
echo date('H:i:s') , " Set default font" , EOL;
$objPhpSpreadsheet->getDefaultStyle()
    ->getFont()
    ->setName('Arial')
    ->setSize(10);

// Add some data, resembling some different data types
echo date('H:i:s') , " Add some data" , EOL;
$objPhpSpreadsheet->getActiveSheet()
    ->setCellValue('A1', 'String')
    ->setCellValue('B1', 'Simple')
    ->setCellValue('C1', 'PhpSpreadsheet');

$objPhpSpreadsheet->getActiveSheet()
    ->setCellValue('A2', 'String')
    ->setCellValue('B2', 'Symbols')
    ->setCellValue('C2', '!+&=()~§±æþ');

$objPhpSpreadsheet->getActiveSheet()
    ->setCellValue('A3', 'String')
    ->setCellValue('B3', 'UTF-8')
    ->setCellValue('C3', 'Создать MS Excel Книги из PHP скриптов');

$objPhpSpreadsheet->getActiveSheet()
    ->setCellValue('A4', 'Number')
    ->setCellValue('B4', 'Integer')
    ->setCellValue('C4', 12);

$objPhpSpreadsheet->getActiveSheet()
    ->setCellValue('A5', 'Number')
    ->setCellValue('B5', 'Float')
    ->setCellValue('C5', 34.56);

$objPhpSpreadsheet->getActiveSheet()
    ->setCellValue('A6', 'Number')
    ->setCellValue('B6', 'Negative')
    ->setCellValue('C6', -7.89);

$objPhpSpreadsheet->getActiveSheet()
    ->setCellValue('A7', 'Boolean')
    ->setCellValue('B7', 'True')
    ->setCellValue('C7', true);

$objPhpSpreadsheet->getActiveSheet()
    ->setCellValue('A8', 'Boolean')
    ->setCellValue('B8', 'False')
    ->setCellValue('C8', false);

$dateTimeNow = time();
$objPhpSpreadsheet->getActiveSheet()
    ->setCellValue('A9', 'Date/Time')
    ->setCellValue('B9', 'Date')
    ->setCellValue('C9', \PhpSpreadsheet\Shared\Date::PHPToExcel( $dateTimeNow ));
$objPhpSpreadsheet->getActiveSheet()
    ->getStyle('C9')
    ->getNumberFormat()
    ->setFormatCode(\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDD2);

$objPhpSpreadsheet->getActiveSheet()
    ->setCellValue('A10', 'Date/Time')
    ->setCellValue('B10', 'Time')
    ->setCellValue('C10', \PhpSpreadsheet\Shared\Date::PHPToExcel( $dateTimeNow ));
$objPhpSpreadsheet->getActiveSheet()
    ->getStyle('C10')
    ->getNumberFormat()
    ->setFormatCode(\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME4);

$objPhpSpreadsheet->getActiveSheet()
    ->setCellValue('A11', 'Date/Time')
    ->setCellValue('B11', 'Date and Time')
    ->setCellValue('C11', \PhpSpreadsheet\Shared\Date::PHPToExcel( $dateTimeNow ));
$objPhpSpreadsheet->getActiveSheet()
    ->getStyle('C11')
    ->getNumberFormat()
    ->setFormatCode(\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DATETIME);

$objPhpSpreadsheet->getActiveSheet()
    ->setCellValue('A12', 'NULL')
    ->setCellValue('C12', NULL);

$objRichText = new \PhpSpreadsheet\RichText();
$objRichText->createText('你好 ');

$objPayable = $objRichText->createTextRun('你 好 吗？');
$objPayable->getFont()->setBold(true);
$objPayable->getFont()->setItalic(true);
$objPayable->getFont()->setColor( new \PhpSpreadsheet\Style\Color( \PhpSpreadsheet\Style\Color::COLOR_DARKGREEN ) );

$objRichText->createText(', unless specified otherwise on the invoice.');

$objPhpSpreadsheet->getActiveSheet()
    ->setCellValue('A13', 'Rich Text')
    ->setCellValue('C13', $objRichText);


$objRichText2 = new \PhpSpreadsheet\RichText();
$objRichText2->createText("black text\n");

$objRed = $objRichText2->createTextRun("red text");
$objRed->getFont()->setColor( new \PhpSpreadsheet\Style\Color(\PhpSpreadsheet\Style\Color::COLOR_RED ) );

$objPhpSpreadsheet->getActiveSheet()
    ->getCell("C14")
    ->setValue($objRichText2);
$objPhpSpreadsheet->getActiveSheet()
    ->getStyle("C14")
    ->getAlignment()->setWrapText(true);


$objPhpSpreadsheet->getActiveSheet()->setCellValue('A17', 'Hyperlink');

$objPhpSpreadsheet->getActiveSheet()
    ->setCellValue('C17', 'PhpSpreadsheet Web Site');
$objPhpSpreadsheet->getActiveSheet()
    ->getCell('C17')
    ->getHyperlink()
    ->setUrl('https://github.com/PHPOffice/PhpSpreadsheet')
    ->setTooltip('Navigate to PhpSpreadsheet website');

$objPhpSpreadsheet->getActiveSheet()
    ->setCellValue('C18', '=HYPERLINK("mailto:abc@def.com","abc@def.com")');


$objPhpSpreadsheet->getActiveSheet()
    ->getColumnDimension('B')
    ->setAutoSize(true);
$objPhpSpreadsheet->getActiveSheet()
    ->getColumnDimension('C')
    ->setAutoSize(true);

// Rename worksheet
echo date('H:i:s') , " Rename worksheet" , EOL;
$objPhpSpreadsheet->getActiveSheet()->setTitle('Datatypes');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPhpSpreadsheet->setActiveSheetIndex(0);


// Save Excel 2007 file
echo date('H:i:s') , " Write to Excel2007 format" , EOL;
$callStartTime = microtime(true);

$objWriter = \PhpSpreadsheet\IOFactory::createWriter($objPhpSpreadsheet, 'Excel2007');
$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
$callEndTime = microtime(true);
$callTime = $callEndTime - $callStartTime;

echo date('H:i:s') , " File written to " , str_replace('.php', '.xlsx', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;
echo 'Call time to write Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;
// Echo memory usage
echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;


// Save Excel 5 file
echo date('H:i:s') , " Write to Excel5 format" , EOL;
$callStartTime = microtime(true);

$objWriter = \PhpSpreadsheet\IOFactory::createWriter($objPhpSpreadsheet, 'Excel5');
$objWriter->save(str_replace('.php', '.xls', __FILE__));
$callEndTime = microtime(true);
$callTime = $callEndTime - $callStartTime;

echo date('H:i:s') , " File written to " , str_replace('.php', '.xls', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;
echo 'Call time to write Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;
// Echo memory usage
echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;


// Echo memory peak usage
echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , EOL;

// Echo done
echo date('H:i:s') , " Done testing file" , EOL;
echo 'Files have been created in ' , getcwd() , EOL;
