<?php

/** Error reporting */
error_reporting(E_ALL);

date_default_timezone_set('Europe/London');

/**
 * PHPExcel
 *
 * Copyright (C) 2006 - 2008 PHPExcel
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
 * @copyright  Copyright (c) 2006 - 2008 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */

/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . '../Classes/');

/** PHPExcel_IOFactory */
include 'PHPExcel/IOFactory.php';

$inputFileType = 'Excel2007';
$inputFileName = 'templates/32chartreadwrite.xlsx';


echo date('H:i:s')." Load Test from $inputFileType file" , PHP_EOL;

$objReader = PHPExcel_IOFactory::createReader($inputFileType);
$objPHPExcel = $objReader->load($inputFileName);


echo date('H:i:s') , " Iterate worksheets looking at the charts" , PHP_EOL;
foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
	$sheetName = $worksheet->getTitle();
	echo 'Worksheet: ' , $sheetName , PHP_EOL;

	$chartNames = $worksheet->getChartNames();
	if(empty($chartNames)) {
		echo '    There are no charts in this worksheet' , PHP_EOL;
	} else {
		natsort($chartNames);
		foreach($chartNames as $i => $chartName) {
			$chart = $worksheet->getChartByName($chartName);
			if (!is_null($chart->getTitle())) {
				$caption = '"' . implode(' ',$chart->getTitle()->getCaption()) . '"';
			} else {
				$caption = 'Untitled';
			}
			echo '    ' , $chartName , ' - ' , $caption , PHP_EOL;
			echo str_repeat(' ',strlen($chartName)+3);
			$groupCount = $chart->getPlotArea()->getPlotGroupCount();
			if ($groupCount == 1) {
				$chartType = $chart->getPlotArea()->getPlotGroupByIndex(0)->getPlotType();
				echo '    ' , $chartType , PHP_EOL;
			} else {
				$chartTypes = array();
				for($i = 0; $i < $groupCount; ++$i) {
					$chartTypes[] = $chart->getPlotArea()->getPlotGroupByIndex($i)->getPlotType();
				}
				$chartTypes = array_unique($chartTypes);
				if (count($chartTypes) == 1) {
					$chartType = 'Multiple Plot ' . array_pop($chartTypes);
					echo '    ' , $chartType , PHP_EOL;
				} elseif (count($chartTypes) == 0) {
					echo '    *** Type not yet implemented' , PHP_EOL;
				} else {
					echo '    Combination Chart' , PHP_EOL;
				}
			}
		}
	}
}


echo date('H:i:s')." Write Tests to Excel2007 file" , PHP_EOL;
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
echo date('H:i:s') , " File written to " , str_replace('.php', '.xlsx', __FILE__) , PHP_EOL;

// Echo memory peak usage
echo date('H:i:s').' Peak memory usage: '.(memory_get_peak_usage(true) / 1024 / 1024)." MB" , PHP_EOL;

// Echo done
echo date('H:i:s')." Done writing files." , PHP_EOL;
