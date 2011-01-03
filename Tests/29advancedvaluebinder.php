<?php
/**
 * PHPExcel
 *
 * Copyright (C) 2006 - 2011 PHPExcel
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
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2011 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */

/** Error reporting */
error_reporting(E_ALL);

date_default_timezone_set('Europe/London');

/** PHPExcel */
require_once '../Classes/PHPExcel.php';


// Set timezone
echo date('H:i:s') . " Set timezone\n";
date_default_timezone_set('UTC');

// Set value binder
echo date('H:i:s') . " Set value binder\n";
PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );

// Create new PHPExcel object
echo date('H:i:s') . " Create new PHPExcel object\n";
$objPHPExcel = new PHPExcel();

// Set properties
echo date('H:i:s') . " Set properties\n";
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
							 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");

// Set default font
echo date('H:i:s') . " Set default font\n";
$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Arial');
$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10);

// Set column widths
echo date('H:i:s') . " Set column widths\n";
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(14);

// Add some data, resembling some different data types
echo date('H:i:s') . " Add some data\n";
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'String value:');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Mark Baker');

$objPHPExcel->getActiveSheet()->setCellValue('A2', 'Numeric value #1:');
$objPHPExcel->getActiveSheet()->setCellValue('B2', 12345);

$objPHPExcel->getActiveSheet()->setCellValue('A3', 'Numeric value #2:');
$objPHPExcel->getActiveSheet()->setCellValue('B3', -12.345);

$objPHPExcel->getActiveSheet()->setCellValue('A4', 'Numeric value #3:');
$objPHPExcel->getActiveSheet()->setCellValue('B4', .12345);

$objPHPExcel->getActiveSheet()->setCellValue('A5', 'Numeric value #4:');
$objPHPExcel->getActiveSheet()->setCellValue('B5', '12345');

$objPHPExcel->getActiveSheet()->setCellValue('A6', 'Numeric value #5:');
$objPHPExcel->getActiveSheet()->setCellValue('B6', '1.2345');

$objPHPExcel->getActiveSheet()->setCellValue('A7', 'Numeric value #6:');
$objPHPExcel->getActiveSheet()->setCellValue('B7', '.12345');

$objPHPExcel->getActiveSheet()->setCellValue('A8', 'Numeric value #7:');
$objPHPExcel->getActiveSheet()->setCellValue('B8', '1.234e-5');

$objPHPExcel->getActiveSheet()->setCellValue('A9', 'Numeric value #8:');
$objPHPExcel->getActiveSheet()->setCellValue('B9', '-1.234e+5');

$objPHPExcel->getActiveSheet()->setCellValue('A10', 'Boolean value:');
$objPHPExcel->getActiveSheet()->setCellValue('B10', true);

$objPHPExcel->getActiveSheet()->setCellValue('A11', 'Percentage value #1:');
$objPHPExcel->getActiveSheet()->setCellValue('B11', '10%');

$objPHPExcel->getActiveSheet()->setCellValue('A12', 'Percentage value #2:');
$objPHPExcel->getActiveSheet()->setCellValue('B12', '12.5%');

$objPHPExcel->getActiveSheet()->setCellValue('A13', 'Date value #1:');
$objPHPExcel->getActiveSheet()->setCellValue('B13', '21 December 1983');

$objPHPExcel->getActiveSheet()->setCellValue('A14', 'Date value #2:');
$objPHPExcel->getActiveSheet()->setCellValue('B14', '19-Dec-1960');

$objPHPExcel->getActiveSheet()->setCellValue('A15', 'Date value #3:');
$objPHPExcel->getActiveSheet()->setCellValue('B15', '19/12/1960');

$objPHPExcel->getActiveSheet()->setCellValue('A16', 'Date value #4:');
$objPHPExcel->getActiveSheet()->setCellValue('B16', '19-12-1960');

$objPHPExcel->getActiveSheet()->setCellValue('A17', 'Date value #5:');
$objPHPExcel->getActiveSheet()->setCellValue('B17', '1-Jan');

$objPHPExcel->getActiveSheet()->setCellValue('A18', 'Time value #1:');
$objPHPExcel->getActiveSheet()->setCellValue('B18', '01:30');

$objPHPExcel->getActiveSheet()->setCellValue('A19', 'Time value #2:');
$objPHPExcel->getActiveSheet()->setCellValue('B19', '01:30:15');

$objPHPExcel->getActiveSheet()->setCellValue('A20', 'Date/Time value:');
$objPHPExcel->getActiveSheet()->setCellValue('B20', '19-Dec-1960 01:30');

$objPHPExcel->getActiveSheet()->setCellValue('A21', 'Formula:');
$objPHPExcel->getActiveSheet()->setCellValue('B21', '=SUM(B2:B9)');

// Rename sheet
echo date('H:i:s') . " Rename sheet\n";
$objPHPExcel->getActiveSheet()->setTitle('Advanced value binder');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Save Excel 2007 file
echo date('H:i:s') . " Write to Excel5 format\n";
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save(str_replace('.php', '.xls', __FILE__));


// Echo memory peak usage
echo date('H:i:s') . " Peak memory usage: " . (memory_get_peak_usage(true) / 1024 / 1024) . " MB\r\n";

// Echo done
echo date('H:i:s') . " Done writing file.\r\n";
