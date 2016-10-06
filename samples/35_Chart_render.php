<?php

require __DIR__ . '/Header.php';

// Change these values to select the Rendering library that you wish to use
// and its directory location on your server
$rendererName = \PhpOffice\PhpSpreadsheet\Settings::CHART_RENDERER_JPGRAPH;
$rendererLibrary = 'jpgraph3.5.0b1/src/';
$rendererLibraryPath = '/php/libraries/Charts/' . $rendererLibrary;

if (!\PhpOffice\PhpSpreadsheet\Settings::setChartRenderer($rendererName, $rendererLibraryPath)) {
    $helper->log('NOTICE: Please set the $rendererName and $rendererLibraryPath values at the top of this script as appropriate for your directory structure');

    return;
}

$inputFileType = 'Xlsx';
$inputFileNames = __DIR__ . '/templates/32readwrite*[0-9].xlsx';

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
        $helper->log('File ', $inputFileNameShort, ' does not exist');
        continue;
    }

    $helper->log("Load Test from $inputFileType file ", $inputFileNameShort);

    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
    $reader->setIncludeCharts(true);
    $spreadsheet = $reader->load($inputFileName);

    $helper->log('Iterate worksheets looking at the charts');
    foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
        $sheetName = $worksheet->getTitle();
        $helper->log('Worksheet: ', $sheetName);

        $chartNames = $worksheet->getChartNames();
        if (empty($chartNames)) {
            $helper->log('    There are no charts in this worksheet');
        } else {
            natsort($chartNames);
            foreach ($chartNames as $i => $chartName) {
                $chart = $worksheet->getChartByName($chartName);
                if (!is_null($chart->getTitle())) {
                    $caption = '"' . implode(' ', $chart->getTitle()->getCaption()) . '"';
                } else {
                    $caption = 'Untitled';
                }
                $helper->log('    ', $chartName, ' - ', $caption);

                $jpegFile = $helper->getFilename('35-' . $inputFileNameShort, 'jpg');
                if (file_exists($jpegFile)) {
                    unlink($jpegFile);
                }
                try {
                    $chart->render($jpegFile);
                } catch (Exception $e) {
                    $helper->log('Error rendering chart: ', $e->getMessage());
                }
            }
        }
    }

    $spreadsheet->disconnectWorksheets();
    unset($spreadsheet);
}

// Echo done
$helper->log('Done rendering charts as images');
