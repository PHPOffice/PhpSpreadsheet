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

/** PHPExcel_IOFactory */
require_once '../Classes/PHPExcel/IOFactory.php';

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>PHPExcel OOCalc Reader Test</title>

</head>
<body>

<?php

echo date('H:i:s') . " Load from OOCalc file\n";
$callStartTime = microtime(true);

$objReader = PHPExcel_IOFactory::createReader('OOCalc');
$objPHPExcel = $objReader->load("OOCalcTest.ods");


$callEndTime = microtime(true);
$callTime = $callEndTime - $callStartTime;
echo '<br />Call time to read Workbook was '.sprintf('%.4f',$callTime)." seconds<br />\n";
// Echo memory usage
echo date('H:i:s').' Current memory usage: '.(memory_get_usage(true) / 1024 / 1024)." MB<br /><hr />\n";


echo date('H:i:s') . " Write to Excel2007 format<br />";
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save(str_replace('.php', '.xlsx', __FILE__));


// Echo memory peak usage
echo date('H:i:s') . " Peak memory usage: " . (memory_get_peak_usage(true) / 1024 / 1024) . " MB<br />";

// Echo done
echo date('H:i:s') . " Done writing file.<br />";

?>
<body>
</html>