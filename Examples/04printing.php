<?php
/**
 * PhpSpreadsheet
 *
 * Copyright (c) 2006 - 2015 PhpSpreadsheet
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
 * @package    PhpSpreadsheet
 * @copyright  Copyright (c) 2006 - 2015 PhpSpreadsheet (https://github.com/PHPOffice/PhpSpreadsheet)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

date_default_timezone_set('Europe/London');

/** Include PhpSpreadsheet */
require_once dirname(__FILE__) . '/../src/Bootstrap.php';


// Create new PhpSpreadsheet object
echo date('H:i:s') , " Create new PhpSpreadsheet object" , EOL;
$objPhpSpreadsheet = new \PhpSpreadsheet\Spreadsheet();

// Set document properties
echo date('H:i:s') , " Set document properties" , EOL;
$objPhpSpreadsheet->getProperties()->setCreator("Maarten Balliauw")
    ->setLastModifiedBy("Maarten Balliauw")
	->setTitle("Office 2007 XLSX Test Document")
	->setSubject("Office 2007 XLSX Test Document")
	->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
	->setKeywords("office 2007 openxml php")
	->setCategory("Test result file");


// Add some data, we will use printing features
echo date('H:i:s') , " Add some data" , EOL;
for ($i = 1; $i < 200; $i++) {
	$objPhpSpreadsheet->getActiveSheet()->setCellValue('A' . $i, $i);
	$objPhpSpreadsheet->getActiveSheet()->setCellValue('B' . $i, 'Test value');
}

// Set header and footer. When no different headers for odd/even are used, odd header is assumed.
echo date('H:i:s') , " Set header/footer" , EOL;
$objPhpSpreadsheet->getActiveSheet()
    ->getHeaderFooter()
    ->setOddHeader('&L&G&C&HPlease treat this document as confidential!');
$objPhpSpreadsheet->getActiveSheet()
    ->getHeaderFooter()
    ->setOddFooter('&L&B' . $objPhpSpreadsheet->getProperties()->getTitle() . '&RPage &P of &N');

// Add a drawing to the header
echo date('H:i:s') , " Add a drawing to the header" , EOL;
$objDrawing = new \PhpSpreadsheet\Worksheet\HeaderFooterDrawing();
$objDrawing->setName('PhpSpreadsheet logo');
$objDrawing->setPath('./images/PhpSpreadsheet_logo.gif');
$objDrawing->setHeight(36);
$objPhpSpreadsheet->getActiveSheet()
    ->getHeaderFooter()
    ->addImage($objDrawing, \PhpSpreadsheet\Worksheet\HeaderFooter::IMAGE_HEADER_LEFT);

// Set page orientation and size
echo date('H:i:s') , " Set page orientation and size" , EOL;
$objPhpSpreadsheet->getActiveSheet()
    ->getPageSetup()
    ->setOrientation(\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
$objPhpSpreadsheet->getActiveSheet()
    ->getPageSetup()
    ->setPaperSize(\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

// Rename worksheet
echo date('H:i:s') , " Rename worksheet" , EOL;
$objPhpSpreadsheet->getActiveSheet()->setTitle('Printing');


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


// Save Excel 95 file
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
echo date('H:i:s') , " Done writing files" , EOL;
echo 'Files have been created in ' , getcwd() , EOL;
