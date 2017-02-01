<?php

require __DIR__ . '/Header.php';

//	Change these values to select the Rendering library that you wish to use
//		and its directory location on your server
$rendererName = \PhpOffice\PhpSpreadsheet\Settings::CHART_RENDERER_JPGRAPH;
$rendererLibrary = 'jpgraph3.5.0b1/src/';
$rendererLibraryPath = '/php/libraries/Charts/' . $rendererLibrary;

if (!\PhpOffice\PhpSpreadsheet\Settings::setChartRenderer($rendererName, $rendererLibraryPath)) {
    $helper->log('NOTICE: Please set the $rendererName and $rendererLibraryPath values at the top of this script as appropriate for your directory structure');

    return;
}

$inputFileType = 'Xlsx';
$inputFileNames = __DIR__ . '/templates/36write*.xlsx';

if ((isset($argc)) && ($argc > 1)) {
    $inputFileNames = [];
    for ($i = 1; $i < $argc; ++$i) {
        $inputFileNames[] = __DIR__ . '/templates/' . $argv[$i];
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

    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
    $reader->setIncludeCharts(true);
    $spreadsheet = $reader->load($inputFileName);

    $helper->log('Iterate worksheets looking at the charts');
    foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
        $sheetName = $worksheet->getTitle();
        echo 'Worksheet: ', $sheetName, EOL;

        $chartNames = $worksheet->getChartNames();
        if (empty($chartNames)) {
            echo '    There are no charts in this worksheet', EOL;
        } else {
            natsort($chartNames);
            foreach ($chartNames as $i => $chartName) {
                $chart = $worksheet->getChartByName($chartName);
                if (!is_null($chart->getTitle())) {
                    $caption = '"' . implode(' ', $chart->getTitle()->getCaption()) . '"';
                } else {
                    $caption = 'Untitled';
                }
                echo '    ', $chartName, ' - ', $caption, EOL;
                echo str_repeat(' ', strlen($chartName) + 3);
                $groupCount = $chart->getPlotArea()->getPlotGroupCount();
                if ($groupCount == 1) {
                    $chartType = $chart->getPlotArea()->getPlotGroupByIndex(0)->getPlotType();
                    echo '    ', $chartType, EOL;
                } else {
                    $chartTypes = [];
                    for ($i = 0; $i < $groupCount; ++$i) {
                        $chartTypes[] = $chart->getPlotArea()->getPlotGroupByIndex($i)->getPlotType();
                    }
                    $chartTypes = array_unique($chartTypes);
                    if (count($chartTypes) == 1) {
                        $chartType = 'Multiple Plot ' . array_pop($chartTypes);
                        echo '    ', $chartType, EOL;
                    } elseif (count($chartTypes) == 0) {
                        echo '    *** Type not yet implemented', EOL;
                    } else {
                        echo '    Combination Chart', EOL;
                    }
                }
            }
        }
    }

    // Save
    $filename = $helper->getFilename($inputFileName);
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Html');
    $writer->setIncludeCharts(true);
    $callStartTime = microtime(true);
    $writer->save($filename);
    $helper->logWrite($writer, $filename, $callStartTime);

    $spreadsheet->disconnectWorksheets();
    unset($spreadsheet);
}
