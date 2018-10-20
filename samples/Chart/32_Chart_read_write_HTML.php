<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Settings;

require __DIR__ . '/../Header.php';

// Change these values to select the Rendering library that you wish to use
Settings::setChartRenderer(\PhpOffice\PhpSpreadsheet\Chart\Renderer\JpGraph::class);

$inputFileType = 'Xlsx';
$inputFileNames = __DIR__ . '/../templates/36write*.xlsx';

if ((isset($argc)) && ($argc > 1)) {
    $inputFileNames = [];
    for ($i = 1; $i < $argc; ++$i) {
        $inputFileNames[] = __DIR__ . '/../templates/' . $argv[$i];
    }
} else {
    $inputFileNames = glob($inputFileNames);
}
foreach ($inputFileNames as $inputFileName) {
    $inputFileNameShort = basename($inputFileName);

    if (!file_exists($inputFileName)) {
        $helper->log('File ' . $inputFileNameShort . ' does not exist');

        continue;
    }

    $helper->log("Load Test from $inputFileType file " . $inputFileNameShort);

    $reader = IOFactory::createReader($inputFileType);
    $reader->setIncludeCharts(true);
    $spreadsheet = $reader->load($inputFileName);

    $helper->log('Iterate worksheets looking at the charts');
    foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
        $sheetName = $worksheet->getTitle();
        $helper->log('Worksheet: ' . $sheetName);

        $chartNames = $worksheet->getChartNames();
        if (empty($chartNames)) {
            $helper->log('    There are no charts in this worksheet');
        } else {
            natsort($chartNames);
            foreach ($chartNames as $i => $chartName) {
                $chart = $worksheet->getChartByName($chartName);
                if ($chart->getTitle() !== null) {
                    $caption = '"' . implode(' ', $chart->getTitle()->getCaption()) . '"';
                } else {
                    $caption = 'Untitled';
                }
                $helper->log('    ' . $chartName . ' - ' . $caption);
                $helper->log(str_repeat(' ', strlen($chartName) + 3));
                $groupCount = $chart->getPlotArea()->getPlotGroupCount();
                if ($groupCount == 1) {
                    $chartType = $chart->getPlotArea()->getPlotGroupByIndex(0)->getPlotType();
                    $helper->log('    ' . $chartType);
                } else {
                    $chartTypes = [];
                    for ($i = 0; $i < $groupCount; ++$i) {
                        $chartTypes[] = $chart->getPlotArea()->getPlotGroupByIndex($i)->getPlotType();
                    }
                    $chartTypes = array_unique($chartTypes);
                    if (count($chartTypes) == 1) {
                        $chartType = 'Multiple Plot ' . array_pop($chartTypes);
                        $helper->log('    ' . $chartType);
                    } elseif (count($chartTypes) == 0) {
                        $helper->log('    *** Type not yet implemented');
                    } else {
                        $helper->log('    Combination Chart');
                    }
                }
            }
        }
    }

    // Save
    $filename = $helper->getFilename($inputFileName, 'html');
    $writer = IOFactory::createWriter($spreadsheet, 'Html');
    $writer->setIncludeCharts(true);
    $callStartTime = microtime(true);
    $writer->save($filename);
    $helper->logWrite($writer, $filename, $callStartTime);

    $spreadsheet->disconnectWorksheets();
    unset($spreadsheet);
}
