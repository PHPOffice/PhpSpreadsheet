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

// Read from Excel2007 (.xlsx) template
echo date('H:i:s') . " Load Excel2007 template file\n";
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("templates/26template.xlsx");

/** at this point, we could do some manipulations with the template, but we skip this step */

// Export to Excel2007 (.xlsx)
echo date('H:i:s') . " Write to Excel5 format\n";
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save(str_replace('.php', '.xlsx', __FILE__));

// Export to Excel5 (.xls)
echo date('H:i:s') . " Write to Excel5 format\n";
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save(str_replace('.php', '.xls', __FILE__));

// Export to HTML (.html)
echo date('H:i:s') . " Write to HTML format\n";
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'HTML');
$objWriter->save(str_replace('.php', '.htm', __FILE__));

// Export to PDF (.pdf)
echo date('H:i:s') . " Write to PDF format\n";
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
$objWriter->save(str_replace('.php', '.pdf', __FILE__));

// Remove first two rows with field headers before exporting to CSV
echo date('H:i:s') . " Removing first two rows\n";
$objWorksheet = $objPHPExcel->getActiveSheet();
$objWorksheet->removeRow(1, 2);

// Export to CSV (.csv)
echo date('H:i:s') . " Write to CSV format\n";
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
$objWriter->save(str_replace('.php', '.csv', __FILE__));

// Export to CSV with BOM (.csv)
echo date('H:i:s') . " Write to CSV format (with BOM)\n";
$objWriter->setUseBOM(true);
$objWriter->save(str_replace('.php', '-bom.csv', __FILE__));


// Echo memory peak usage
echo date('H:i:s') . " Peak memory usage: " . (memory_get_peak_usage(true) / 1024 / 1024) . " MB\r\n";

// Echo done
echo date('H:i:s') . " Done writing files.\r\n";
