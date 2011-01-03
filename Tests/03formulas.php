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


// Add some data, we will use some formulas here
echo date('H:i:s') . " Add some data\n";
$objPHPExcel->getActiveSheet()->setCellValue('A5', 'Sum:');

$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Range 1');
$objPHPExcel->getActiveSheet()->setCellValue('B2', 2);
$objPHPExcel->getActiveSheet()->setCellValue('B3', 8);
$objPHPExcel->getActiveSheet()->setCellValue('B4', 10);
$objPHPExcel->getActiveSheet()->setCellValue('B5', '=SUM(B2:B4)');

$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Range 2');
$objPHPExcel->getActiveSheet()->setCellValue('C2', 3);
$objPHPExcel->getActiveSheet()->setCellValue('C3', 9);
$objPHPExcel->getActiveSheet()->setCellValue('C4', 11);
$objPHPExcel->getActiveSheet()->setCellValue('C5', '=SUM(C2:C4)');

$objPHPExcel->getActiveSheet()->setCellValue('A7', 'Total of both ranges:');
$objPHPExcel->getActiveSheet()->setCellValue('B7', '=SUM(B5:C5)');

$objPHPExcel->getActiveSheet()->setCellValue('A8', 'Minimum of both ranges:');
$objPHPExcel->getActiveSheet()->setCellValue('B8', '=MIN(B2:C5)');

// Rename sheet
echo date('H:i:s') . " Rename sheet\n";
$objPHPExcel->getActiveSheet()->setTitle('Formulas');


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
