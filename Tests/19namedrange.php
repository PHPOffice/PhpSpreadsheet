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


// Add some data
echo date('H:i:s') . " Add some data\n";
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Firstname:');
$objPHPExcel->getActiveSheet()->setCellValue('A2', 'Lastname:');
$objPHPExcel->getActiveSheet()->setCellValue('A3', 'Fullname:');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Maarten');
$objPHPExcel->getActiveSheet()->setCellValue('B2', 'Balliauw');
$objPHPExcel->getActiveSheet()->setCellValue('B3', '=B1 & " " & B2');

// Define named ranges
echo date('H:i:s') . " Define named ranges\n";
$objPHPExcel->addNamedRange( new PHPExcel_NamedRange('PersonName', $objPHPExcel->getActiveSheet(), 'B1') );
$objPHPExcel->addNamedRange( new PHPExcel_NamedRange('PersonLN', $objPHPExcel->getActiveSheet(), 'B2') );

// Rename named ranges
echo date('H:i:s') . " Rename named ranges\n";
$objPHPExcel->getNamedRange('PersonName')->setName('PersonFN');

// Rename sheet
echo date('H:i:s') . " Rename sheet\n";
$objPHPExcel->getActiveSheet()->setTitle('Person');


// Create a new worksheet, after the default sheet
echo date('H:i:s') . " Create new Worksheet object\n";
$objPHPExcel->createSheet();

// Add some data to the second sheet, resembling some different data types
echo date('H:i:s') . " Add some data\n";
$objPHPExcel->setActiveSheetIndex(1);
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Firstname:');
$objPHPExcel->getActiveSheet()->setCellValue('A2', 'Lastname:');
$objPHPExcel->getActiveSheet()->setCellValue('A3', 'Fullname:');
$objPHPExcel->getActiveSheet()->setCellValue('B1', '=PersonFN');
$objPHPExcel->getActiveSheet()->setCellValue('B2', '=PersonLN');
$objPHPExcel->getActiveSheet()->setCellValue('B3', '=PersonFN & " " & PersonLN');

// Resolve range
echo date('H:i:s') . " Resolve range\n";
echo 'Cell B1 {=PersonFN}: ' . $objPHPExcel->getActiveSheet()->getCell('B1')->getCalculatedValue() . "\n";
echo 'Cell B3 {=PersonFN & " " & PersonLN}: ' . $objPHPExcel->getActiveSheet()->getCell('B3')->getCalculatedValue() . "\n";
echo 'Cell Person!B1: ' . $objPHPExcel->getActiveSheet()->getCell('Person!B1')->getCalculatedValue() . "\n";

// Rename sheet
echo date('H:i:s') . " Rename sheet\n";
$objPHPExcel->getActiveSheet()->setTitle('Person (cloned)');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Save Excel 2007 file
echo date('H:i:s') . " Write to Excel2007 format\n";
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save(str_replace('.php', '.xlsx', __FILE__));


// Echo memory peak usage
echo date('H:i:s') . " Peak memory usage: " . (memory_get_peak_usage(true) / 1024 / 1024) . " MB\r\n";

// Echo done
echo date('H:i:s') . " Done writing file.\r\n";
