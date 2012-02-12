<?php
/**
 * PHPExcel
 *
 * Copyright (C) 2006 - 2012 PHPExcel
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
 * @copyright  Copyright (c) 2006 - 2012 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */

/** Error reporting */
error_reporting(E_ALL);

date_default_timezone_set('Europe/London');

/** Include PHPExcel */
require_once '../Classes/PHPExcel.php';


// List functions
echo date('H:i:s') , " List implemented functions" , PHP_EOL;
$objCalc = PHPExcel_Calculation::getInstance();
print_r($objCalc->listFunctionNames());

// Create new PHPExcel object
echo date('H:i:s') , " Create new PHPExcel object" , PHP_EOL;
$objPHPExcel = new PHPExcel();

// Add some data, we will use some formulas here
echo date('H:i:s') , " Add some data and formulas" , PHP_EOL;
$objPHPExcel->getActiveSheet()->setCellValue('A14', 'Count:');
$objPHPExcel->getActiveSheet()->setCellValue('A15', 'Sum:');
$objPHPExcel->getActiveSheet()->setCellValue('A16', 'Max:');
$objPHPExcel->getActiveSheet()->setCellValue('A17', 'Min:');
$objPHPExcel->getActiveSheet()->setCellValue('A18', 'Average:');
$objPHPExcel->getActiveSheet()->setCellValue('A19', 'Median:');
$objPHPExcel->getActiveSheet()->setCellValue('A20', 'Mode:');

$objPHPExcel->getActiveSheet()->setCellValue('A22', 'CountA:');
$objPHPExcel->getActiveSheet()->setCellValue('A23', 'MaxA:');
$objPHPExcel->getActiveSheet()->setCellValue('A24', 'MinA:');

$objPHPExcel->getActiveSheet()->setCellValue('A26', 'StDev:');
$objPHPExcel->getActiveSheet()->setCellValue('A27', 'StDevA:');
$objPHPExcel->getActiveSheet()->setCellValue('A28', 'StDevP:');
$objPHPExcel->getActiveSheet()->setCellValue('A29', 'StDevPA:');

$objPHPExcel->getActiveSheet()->setCellValue('A31', 'DevSq:');
$objPHPExcel->getActiveSheet()->setCellValue('A32', 'Var:');
$objPHPExcel->getActiveSheet()->setCellValue('A33', 'VarA:');
$objPHPExcel->getActiveSheet()->setCellValue('A34', 'VarP:');
$objPHPExcel->getActiveSheet()->setCellValue('A35', 'VarPA:');

$objPHPExcel->getActiveSheet()->setCellValue('A37', 'Date:');


$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Range 1');
$objPHPExcel->getActiveSheet()->setCellValue('B2', 2);
$objPHPExcel->getActiveSheet()->setCellValue('B3', 8);
$objPHPExcel->getActiveSheet()->setCellValue('B4', 10);
$objPHPExcel->getActiveSheet()->setCellValue('B5', True);
$objPHPExcel->getActiveSheet()->setCellValue('B6', False);
$objPHPExcel->getActiveSheet()->setCellValue('B7', 'Text String');
$objPHPExcel->getActiveSheet()->setCellValue('B9', '22');
$objPHPExcel->getActiveSheet()->setCellValue('B10', 4);
$objPHPExcel->getActiveSheet()->setCellValue('B11', 6);
$objPHPExcel->getActiveSheet()->setCellValue('B12', 12);

$objPHPExcel->getActiveSheet()->setCellValue('B14', '=COUNT(B2:B12)');
$objPHPExcel->getActiveSheet()->setCellValue('B15', '=SUM(B2:B12)');
$objPHPExcel->getActiveSheet()->setCellValue('B16', '=MAX(B2:B12)');
$objPHPExcel->getActiveSheet()->setCellValue('B17', '=MIN(B2:B12)');
$objPHPExcel->getActiveSheet()->setCellValue('B18', '=AVERAGE(B2:B12)');
$objPHPExcel->getActiveSheet()->setCellValue('B19', '=MEDIAN(B2:B12)');
$objPHPExcel->getActiveSheet()->setCellValue('B20', '=MODE(B2:B12)');

$objPHPExcel->getActiveSheet()->setCellValue('B22', '=COUNTA(B2:B12)');
$objPHPExcel->getActiveSheet()->setCellValue('B23', '=MAXA(B2:B12)');
$objPHPExcel->getActiveSheet()->setCellValue('B24', '=MINA(B2:B12)');

$objPHPExcel->getActiveSheet()->setCellValue('B26', '=STDEV(B2:B12)');
$objPHPExcel->getActiveSheet()->setCellValue('B27', '=STDEVA(B2:B12)');
$objPHPExcel->getActiveSheet()->setCellValue('B28', '=STDEVP(B2:B12)');
$objPHPExcel->getActiveSheet()->setCellValue('B29', '=STDEVPA(B2:B12)');

$objPHPExcel->getActiveSheet()->setCellValue('B31', '=DEVSQ(B2:B12)');
$objPHPExcel->getActiveSheet()->setCellValue('B32', '=VAR(B2:B12)');
$objPHPExcel->getActiveSheet()->setCellValue('B33', '=VARA(B2:B12)');
$objPHPExcel->getActiveSheet()->setCellValue('B34', '=VARP(B2:B12)');
$objPHPExcel->getActiveSheet()->setCellValue('B35', '=VARPA(B2:B12)');

$objPHPExcel->getActiveSheet()->setCellValue('B37', '=DATE(2007, 12, 21)');
$objPHPExcel->getActiveSheet()->setCellValue('B38', '=DATEDIF( DATE(2007, 12, 21), DATE(2007, 12, 22), "D" )');
$objPHPExcel->getActiveSheet()->setCellValue('B39', '=DATEVALUE("01-Feb-2006 10:06 AM")');
$objPHPExcel->getActiveSheet()->setCellValue('B40', '=DAY( DATE(2006, 1, 2) )');
$objPHPExcel->getActiveSheet()->setCellValue('B41', '=DAYS360( DATE(2002, 2, 3), DATE(2005, 5, 31) )');


$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Range 2');
$objPHPExcel->getActiveSheet()->setCellValue('C2', 1);
$objPHPExcel->getActiveSheet()->setCellValue('C3', 2);
$objPHPExcel->getActiveSheet()->setCellValue('C4', 2);
$objPHPExcel->getActiveSheet()->setCellValue('C5', 3);
$objPHPExcel->getActiveSheet()->setCellValue('C6', 3);
$objPHPExcel->getActiveSheet()->setCellValue('C7', 3);
$objPHPExcel->getActiveSheet()->setCellValue('C8', '0');
$objPHPExcel->getActiveSheet()->setCellValue('C9', 4);
$objPHPExcel->getActiveSheet()->setCellValue('C10', 4);
$objPHPExcel->getActiveSheet()->setCellValue('C11', 4);
$objPHPExcel->getActiveSheet()->setCellValue('C12', 4);

$objPHPExcel->getActiveSheet()->setCellValue('C14', '=COUNT(C2:C12)');
$objPHPExcel->getActiveSheet()->setCellValue('C15', '=SUM(C2:C12)');
$objPHPExcel->getActiveSheet()->setCellValue('C16', '=MAX(C2:C12)');
$objPHPExcel->getActiveSheet()->setCellValue('C17', '=MIN(C2:C12)');
$objPHPExcel->getActiveSheet()->setCellValue('C18', '=AVERAGE(C2:C12)');
$objPHPExcel->getActiveSheet()->setCellValue('C19', '=MEDIAN(C2:C12)');
$objPHPExcel->getActiveSheet()->setCellValue('C20', '=MODE(C2:C12)');

$objPHPExcel->getActiveSheet()->setCellValue('C22', '=COUNTA(C2:C12)');
$objPHPExcel->getActiveSheet()->setCellValue('C23', '=MAXA(C2:C12)');
$objPHPExcel->getActiveSheet()->setCellValue('C24', '=MINA(C2:C12)');

$objPHPExcel->getActiveSheet()->setCellValue('C26', '=STDEV(C2:C12)');
$objPHPExcel->getActiveSheet()->setCellValue('C27', '=STDEVA(C2:C12)');
$objPHPExcel->getActiveSheet()->setCellValue('C28', '=STDEVP(C2:C12)');
$objPHPExcel->getActiveSheet()->setCellValue('C29', '=STDEVPA(C2:C12)');

$objPHPExcel->getActiveSheet()->setCellValue('C31', '=DEVSQ(C2:C12)');
$objPHPExcel->getActiveSheet()->setCellValue('C32', '=VAR(C2:C12)');
$objPHPExcel->getActiveSheet()->setCellValue('C33', '=VARA(C2:C12)');
$objPHPExcel->getActiveSheet()->setCellValue('C34', '=VARP(C2:C12)');
$objPHPExcel->getActiveSheet()->setCellValue('C35', '=VARPA(C2:C12)');


$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Range 3');
$objPHPExcel->getActiveSheet()->setCellValue('D2', 2);
$objPHPExcel->getActiveSheet()->setCellValue('D3', 3);
$objPHPExcel->getActiveSheet()->setCellValue('D4', 4);

$objPHPExcel->getActiveSheet()->setCellValue('D14', '=((D2 * D3) + D4) & " should be 10"');

$objPHPExcel->getActiveSheet()->setCellValue('E12', 'Other functions');
$objPHPExcel->getActiveSheet()->setCellValue('E14', '=PI()');
$objPHPExcel->getActiveSheet()->setCellValue('E15', '=RAND()');
$objPHPExcel->getActiveSheet()->setCellValue('E16', '=RANDBETWEEN(5, 10)');

$objPHPExcel->getActiveSheet()->setCellValue('E17', 'Count of both ranges:');
$objPHPExcel->getActiveSheet()->setCellValue('F17', '=COUNT(B2:C12)');

$objPHPExcel->getActiveSheet()->setCellValue('E18', 'Total of both ranges:');
$objPHPExcel->getActiveSheet()->setCellValue('F18', '=SUM(B2:C12)');

$objPHPExcel->getActiveSheet()->setCellValue('E19', 'Maximum of both ranges:');
$objPHPExcel->getActiveSheet()->setCellValue('F19', '=MAX(B2:C12)');

$objPHPExcel->getActiveSheet()->setCellValue('E20', 'Minimum of both ranges:');
$objPHPExcel->getActiveSheet()->setCellValue('F20', '=MIN(B2:C12)');

$objPHPExcel->getActiveSheet()->setCellValue('E21', 'Average of both ranges:');
$objPHPExcel->getActiveSheet()->setCellValue('F21', '=AVERAGE(B2:C12)');

$objPHPExcel->getActiveSheet()->setCellValue('E22', 'Median of both ranges:');
$objPHPExcel->getActiveSheet()->setCellValue('F22', '=MEDIAN(B2:C12)');

$objPHPExcel->getActiveSheet()->setCellValue('E23', 'Mode of both ranges:');
$objPHPExcel->getActiveSheet()->setCellValue('F23', '=MODE(B2:C12)');


// Calculated data
echo date('H:i:s') , " Calculated data" , PHP_EOL;
for ($col = 'B'; $col != 'G'; ++$col) {
    for($row = 14; $row <= 41; ++$row) {
        if ((!is_null($formula = $objPHPExcel->getActiveSheet()->getCell($col.$row)->getValue())) &&
			($formula[0] == '=')) {
            echo 'Value of ' , $col , $row , ' [' , $formula , ']: ' ,
                               $objPHPExcel->getActiveSheet()->getCell($col.$row)->getCalculatedValue() . PHP_EOL;
        }
    }
}


// Save Excel 2007 file
echo date('H:i:s') , " Write to Excel2007 format" , PHP_EOL;
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
echo date('H:i:s') , " File written to " , str_replace('.php', '.xlsx', __FILE__) , PHP_EOL;


// Echo memory peak usage
echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , PHP_EOL;

// Echo done
echo date('H:i:s') , " Done" , PHP_EOL;
