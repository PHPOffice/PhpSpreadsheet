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


/*
After doing some test, I've got these results benchmarked
for writing to Excel2007:

	Number of rows	Seconds to generate
	200				3
	500				4
	1000			6
	2000			12
	4000			36
	8000			64
	15000			465
*/

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


// Create a first sheet
echo date('H:i:s') . " Add data\n";
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('A1', "Firstname");
$objPHPExcel->getActiveSheet()->setCellValue('B1', "Lastname");
$objPHPExcel->getActiveSheet()->setCellValue('C1', "Phone");
$objPHPExcel->getActiveSheet()->setCellValue('D1', "Fax");
$objPHPExcel->getActiveSheet()->setCellValue('E1', "Is Client ?");


// Hide "Phone" and "fax" column
echo date('H:i:s') . " Hide \"Phone\" and \"fax\" column\n";
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setVisible(false);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setVisible(false);


// Set outline levels
echo date('H:i:s') . " Set outline levels\n";
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setOutlineLevel(1);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setVisible(false);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setCollapsed(true);

// Freeze panes
echo date('H:i:s') . " Freeze panes\n";
$objPHPExcel->getActiveSheet()->freezePane('A2');


// Rows to repeat at top
echo date('H:i:s') . " Rows to repeat at top\n";
$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 1);


// Add data
for ($i = 2; $i <= 5000; $i++) {
	$objPHPExcel->getActiveSheet()->setCellValue('A' . $i, "FName $i");
	$objPHPExcel->getActiveSheet()->setCellValue('B' . $i, "LName $i");
	$objPHPExcel->getActiveSheet()->setCellValue('C' . $i, "PhoneNo $i");
	$objPHPExcel->getActiveSheet()->setCellValue('D' . $i, "FaxNo $i");
	$objPHPExcel->getActiveSheet()->setCellValue('E' . $i, true);
}


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
