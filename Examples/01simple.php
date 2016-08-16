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


// Create new Spreadsheet object
echo date('H:i:s') , " Create new Spreadsheet object" , EOL;
$objPhpSpreadsheet = new \PhpSpreadsheet\Spreadsheet();

// Set document properties
echo date('H:i:s') , " Set document properties" , EOL;
$objPhpSpreadsheet->getProperties()
    ->setCreator("Maarten Balliauw")
	->setLastModifiedBy("Maarten Balliauw")
	->setTitle("PhpSpreadsheet Test Document")
	->setSubject("PhpSpreadsheet Test Document")
	->setDescription("Test document for PhpSpreadsheet, generated using PHP classes.")
	->setKeywords("office PhpSpreadsheet php")
	->setCategory("Test result file");


// Add some data
echo date('H:i:s') , " Add some data" , EOL;
$objPhpSpreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', 'Hello')
    ->setCellValue('B2', 'world!')
    ->setCellValue('C1', 'Hello')
    ->setCellValue('D2', 'world!');

// Miscellaneous glyphs, UTF-8
$objPhpSpreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A4', 'Miscellaneous glyphs')
    ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');


$objPhpSpreadsheet->getActiveSheet()
    ->setCellValue('A8',"Hello\nWorld");
$objPhpSpreadsheet->getActiveSheet()
    ->getRowDimension(8)
    ->setRowHeight(-1);
$objPhpSpreadsheet->getActiveSheet()
    ->getStyle('A8')
    ->getAlignment()
    ->setWrapText(true);


$value = "-ValueA\n-Value B\n-Value C";
$objPhpSpreadsheet->getActiveSheet()
    ->setCellValue('A10', $value);
$objPhpSpreadsheet->getActiveSheet()
    ->getRowDimension(10)
    ->setRowHeight(-1);
$objPhpSpreadsheet->getActiveSheet()
    ->getStyle('A10')
    ->getAlignment()
    ->setWrapText(true);
$objPhpSpreadsheet->getActiveSheet()
    ->getStyle('A10')
    ->setQuotePrefix(true);


// Rename worksheet
echo date('H:i:s') , " Rename worksheet" , EOL;
$objPhpSpreadsheet->getActiveSheet()
    ->setTitle('Simple');


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
