<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Settings;

require __DIR__ . '/../Header.php';

// Change these values to select the Rendering library that you wish to use
Settings::setChartRenderer(\PhpOffice\PhpSpreadsheet\Chart\Renderer\JpGraph::class);

$inputFileType = 'Xlsx';
$inputFileNames = __DIR__ . '/../templates/32readwrite*[0-9].xlsx';

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

                $jpegFile = $helper->getFilename('35-' . $inputFileNameShort, 'png');
                if (file_exists($jpegFile)) {
                    unlink($jpegFile);
                }

                try {
                    $chart->render($jpegFile);
                    $helper->log('Rendered image: ' . $jpegFile);
                } catch (Exception $e) {
                    $helper->log('Error rendering chart: ' . $e->getMessage());
                }
            }
        }
    }

    $spreadsheet->disconnectWorksheets();
    unset($spreadsheet);
}

$helper->log('Done rendering charts as images');
