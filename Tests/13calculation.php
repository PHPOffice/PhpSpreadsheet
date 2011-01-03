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


// List functions
echo date('H:i:s') . " List implemented functions\n";
$objCalc = PHPExcel_Calculation::getInstance();
print_r($objCalc->listFunctionNames());

// Create new PHPExcel object
echo date('H:i:s') . " Create new PHPExcel object\n";
$objPHPExcel = new PHPExcel();

// Add some data, we will use some formulas here
echo date('H:i:s') . " Add some data\n";
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

$objPHPExcel->getActiveSheet()->setCellValue('D5', '=((D2 * D3) + D4) & " should be 10"');

$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Other functions');
$objPHPExcel->getActiveSheet()->setCellValue('E2', '=PI()');
$objPHPExcel->getActiveSheet()->setCellValue('E3', '=RAND()');
$objPHPExcel->getActiveSheet()->setCellValue('E4', '=RANDBETWEEN(5, 10)');

$objPHPExcel->getActiveSheet()->setCellValue('E14', 'Count of both ranges:');
$objPHPExcel->getActiveSheet()->setCellValue('F14', '=COUNT(B2:C12)');

$objPHPExcel->getActiveSheet()->setCellValue('E15', 'Total of both ranges:');
$objPHPExcel->getActiveSheet()->setCellValue('F15', '=SUM(B2:C12)');

$objPHPExcel->getActiveSheet()->setCellValue('E16', 'Maximum of both ranges:');
$objPHPExcel->getActiveSheet()->setCellValue('F16', '=MAX(B2:C12)');

$objPHPExcel->getActiveSheet()->setCellValue('E17', 'Minimum of both ranges:');
$objPHPExcel->getActiveSheet()->setCellValue('F17', '=MIN(B2:C12)');

$objPHPExcel->getActiveSheet()->setCellValue('E18', 'Average of both ranges:');
$objPHPExcel->getActiveSheet()->setCellValue('F18', '=AVERAGE(B2:C12)');

$objPHPExcel->getActiveSheet()->setCellValue('E19', 'Median of both ranges:');
$objPHPExcel->getActiveSheet()->setCellValue('F19', '=MEDIAN(B2:C12)');

$objPHPExcel->getActiveSheet()->setCellValue('E20', 'Mode of both ranges:');
$objPHPExcel->getActiveSheet()->setCellValue('F20', '=MODE(B2:C12)');


// Calculated data
echo date('H:i:s') . " Calculated data\n";
echo 'Value of B14 [=COUNT(B2:B12)]: ' . $objPHPExcel->getActiveSheet()->getCell('B14')->getCalculatedValue() . "\r\n";
echo 'Value of B15 [=SUM(B2:B12)]: ' . $objPHPExcel->getActiveSheet()->getCell('B15')->getCalculatedValue() . "\r\n";
echo 'Value of B16 [=MAX(B2:B12)]: ' . $objPHPExcel->getActiveSheet()->getCell('B16')->getCalculatedValue() . "\r\n";
echo 'Value of B17 [=MIN(B2:B12)]: ' . $objPHPExcel->getActiveSheet()->getCell('B17')->getCalculatedValue() . "\r\n";
echo 'Value of B18 [=AVERAGE(B2:B12)]: ' . $objPHPExcel->getActiveSheet()->getCell('B18')->getCalculatedValue() . "\r\n";
echo 'Value of B19 [=MEDIAN(B2:B12)]: ' . $objPHPExcel->getActiveSheet()->getCell('B19')->getCalculatedValue() . "\r\n";
echo 'Value of B20 [=MODE(B2:B12)]: ' . $objPHPExcel->getActiveSheet()->getCell('B20')->getCalculatedValue() . "\r\n";

echo 'Value of B22 [=COUNTA(B2:B12)]: ' . $objPHPExcel->getActiveSheet()->getCell('B22')->getCalculatedValue() . "\r\n";
echo 'Value of B23 [=MAXA(B2:B12)]: ' . $objPHPExcel->getActiveSheet()->getCell('B23')->getCalculatedValue() . "\r\n";
echo 'Value of B24 [=MINA(B2:B12)]: ' . $objPHPExcel->getActiveSheet()->getCell('B24')->getCalculatedValue() . "\r\n";

echo 'Value of B26 [=STDEV(B2:B12)]: ' . $objPHPExcel->getActiveSheet()->getCell('B26')->getCalculatedValue() . "\r\n";
echo 'Value of B27 [=STDEVA(B2:B12)]: ' . $objPHPExcel->getActiveSheet()->getCell('B27')->getCalculatedValue() . "\r\n";
echo 'Value of B28 [=STDEVP(B2:B12)]: ' . $objPHPExcel->getActiveSheet()->getCell('B28')->getCalculatedValue() . "\r\n";
echo 'Value of B29 [=STDEVPA(B2:B12)]: ' . $objPHPExcel->getActiveSheet()->getCell('B29')->getCalculatedValue() . "\r\n";

echo 'Value of B31 [=DEVSQ(B2:B12)]: ' . $objPHPExcel->getActiveSheet()->getCell('B31')->getCalculatedValue() . "\r\n";
echo 'Value of B32 [=VAR(B2:B12)]: ' . $objPHPExcel->getActiveSheet()->getCell('B32')->getCalculatedValue() . "\r\n";
echo 'Value of B33 [=VARA(B2:B12)]: ' . $objPHPExcel->getActiveSheet()->getCell('B33')->getCalculatedValue() . "\r\n";
echo 'Value of B34 [=VARP(B2:B12)]: ' . $objPHPExcel->getActiveSheet()->getCell('B34')->getCalculatedValue() . "\r\n";
echo 'Value of B35 [=VARPA(B2:B12)]: ' . $objPHPExcel->getActiveSheet()->getCell('B35')->getCalculatedValue() . "\r\n";

echo 'Value of C14 [=COUNT(C2:C12)]: ' . $objPHPExcel->getActiveSheet()->getCell('C14')->getCalculatedValue() . "\r\n";
echo 'Value of C15 [=SUM(C2:C12)]: ' . $objPHPExcel->getActiveSheet()->getCell('C15')->getCalculatedValue() . "\r\n";
echo 'Value of C16 [=MAX(C2:C12)]: ' . $objPHPExcel->getActiveSheet()->getCell('C16')->getCalculatedValue() . "\r\n";
echo 'Value of C17 [=MIN(C2:C12)]: ' . $objPHPExcel->getActiveSheet()->getCell('C17')->getCalculatedValue() . "\r\n";
echo 'Value of C18 [=AVERAGE(C2:C12)]: ' . $objPHPExcel->getActiveSheet()->getCell('C18')->getCalculatedValue() . "\r\n";
echo 'Value of C19 [=MEDIAN(C2:C12)]: ' . $objPHPExcel->getActiveSheet()->getCell('C19')->getCalculatedValue() . "\r\n";
echo 'Value of C20 [=MODE(C2:C12)]: ' . $objPHPExcel->getActiveSheet()->getCell('C20')->getCalculatedValue() . "\r\n";

echo 'Value of C22 [=COUNTA(C2:C12)]: ' . $objPHPExcel->getActiveSheet()->getCell('C22')->getCalculatedValue() . "\r\n";
echo 'Value of C23 [=MAXA(C2:C12)]: ' . $objPHPExcel->getActiveSheet()->getCell('C23')->getCalculatedValue() . "\r\n";
echo 'Value of C24 [=MINA(C2:C12)]: ' . $objPHPExcel->getActiveSheet()->getCell('C24')->getCalculatedValue() . "\r\n";

echo 'Value of C26 [=STDEV(C2:C12)]: ' . $objPHPExcel->getActiveSheet()->getCell('C26')->getCalculatedValue() . "\r\n";
echo 'Value of C27 [=STDEVA(C2:C12)]: ' . $objPHPExcel->getActiveSheet()->getCell('C27')->getCalculatedValue() . "\r\n";
echo 'Value of C28 [=STDEVP(C2:C12)]: ' . $objPHPExcel->getActiveSheet()->getCell('C28')->getCalculatedValue() . "\r\n";
echo 'Value of C29 [=STDEVPA(C2:C12)]: ' . $objPHPExcel->getActiveSheet()->getCell('C29')->getCalculatedValue() . "\r\n";

echo 'Value of C31 [=DEVSQ(C2:C12)]: ' . $objPHPExcel->getActiveSheet()->getCell('C31')->getCalculatedValue() . "\r\n";
echo 'Value of C32 [=VAR(C2:C12)]: ' . $objPHPExcel->getActiveSheet()->getCell('C32')->getCalculatedValue() . "\r\n";
echo 'Value of C33 [=VARA(C2:C12)]: ' . $objPHPExcel->getActiveSheet()->getCell('C33')->getCalculatedValue() . "\r\n";
echo 'Value of C34 [=VARP(C2:C12)]: ' . $objPHPExcel->getActiveSheet()->getCell('C34')->getCalculatedValue() . "\r\n";
echo 'Value of C35 [=VARPA(C2:C12)]: ' . $objPHPExcel->getActiveSheet()->getCell('C35')->getCalculatedValue() . "\r\n";

echo 'Value of B37 [=DATE(2007, 12, 21)]: ' . $objPHPExcel->getActiveSheet()->getCell('B37')->getCalculatedValue() . "\r\n";
echo 'Value of B38 [=DATEDIF( DATE(2007, 12, 21), DATE(2007, 12, 22), "D" )]: ' . $objPHPExcel->getActiveSheet()->getCell('B38')->getCalculatedValue() . "\r\n";
echo 'Value of B39 [=DATEVALUE("01-Feb-2006 10:06 AM")]: ' . $objPHPExcel->getActiveSheet()->getCell('B39')->getCalculatedValue() . "\r\n";
echo 'Value of B40 [=DAY( DATE(2006, 1, 2) )]: ' . $objPHPExcel->getActiveSheet()->getCell('B40')->getCalculatedValue() . "\r\n";
echo 'Value of B41 [=DAYS360( DATE(2002, 2, 3), DATE(2005, 5, 31) )]: ' . $objPHPExcel->getActiveSheet()->getCell('B41')->getCalculatedValue() . "\r\n";


// Echo memory peak usage
echo date('H:i:s') . " Peak memory usage: " . (memory_get_peak_usage(true) / 1024 / 1024) . " MB\r\n";

// Echo done
echo date('H:i:s') . " Done.\r\n";
