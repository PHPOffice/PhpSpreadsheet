<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Settings;

require __DIR__ . '/../Header.php';

// Change these values to select the Rendering library that you wish to use
//Settings::setChartRenderer(\PhpOffice\PhpSpreadsheet\Chart\Renderer\JpGraph::class);
Settings::setChartRenderer(PhpOffice\PhpSpreadsheet\Chart\Renderer\MtJpGraphRenderer::class);

$inputFileType = 'Xlsx';
$inputFileNames = __DIR__ . '/../templates/32readwrite*[0-9].xlsx';
//$inputFileNames = __DIR__ . '/../templates/32readwriteStockChart5.xlsx';

if ((isset($argc)) && ($argc > 1)) {
    $inputFileNames = [];
    for ($i = 1; $i < $argc; ++$i) {
        $inputFileNames[] = __DIR__ . '/../templates/' . $argv[$i];
    }
} else {
    $inputFileNames = glob($inputFileNames) ?: [];
}
if (count($inputFileNames) === 1) {
    /** @var string[] */
    $unresolvedErrors = [];
} else {
    /** @var string[] */
    $unresolvedErrors = [
        // The following spreadsheet was created by 3rd party software,
        // and doesn't include the data that usually accompanies a chart.
        // That is good enough for Excel, but not for JpGraph.
        '32readwriteBubbleChart2.xlsx',
    ];
}
foreach ($inputFileNames as $inputFileName) {
    $inputFileNameShort = basename($inputFileName);

    if (!file_exists($inputFileName)) {
        $helper->log('File ' . $inputFileNameShort . ' does not exist');

        continue;
    }
    if (in_array($inputFileNameShort, $unresolvedErrors, true)) {
        $helper->log('*****');
        $helper->log('***** File ' . $inputFileNameShort . ' does not yet work with this script');
        $helper->log('*****');

        continue;
    }

    $helper->log("Load Test from $inputFileType file " . $inputFileNameShort);

    $reader = IOFactory::createReader($inputFileType);
    $reader->setIncludeCharts(true);
    $spreadsheet = $reader->load($inputFileName);

    $helper->log('Iterate worksheets looking at the charts');
    $renderedCharts = 0;
    foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
        $sheetName = $worksheet->getTitle();
        $helper->log('Worksheet: ' . $sheetName);

        $chartNames = $worksheet->getChartNames();
        if (empty($chartNames)) {
            $helper->log('    There are no charts in this worksheet');
        } else {
            natsort($chartNames);
            foreach ($chartNames as $j => $chartName) {
                $i = $renderedCharts + $j;
                $chart = $worksheet->getChartByNameOrThrow($chartName);
                if ($chart->getTitle() !== null) {
                    $caption = '"' . $chart->getTitle()->getCaptionText($spreadsheet) . '"';
                } else {
                    $caption = 'Untitled';
                }
                $helper->log('    ' . $chartName . ' - ' . $caption);

                $pngFile = $helper->getFilename('35-' . $inputFileNameShort, 'png');
                if ($i !== 0) {
                    $pngFile = substr($pngFile, 0, -3) . "$i.png";
                }
                if (file_exists($pngFile)) {
                    unlink($pngFile);
                }

                try {
                    $chart->render($pngFile);
                    $helper->log('Rendered image: ' . $pngFile);
                } catch (Exception $e) {
                    $helper->log('Error rendering chart: ' . $e->getMessage());
                }

                ++$renderedCharts;
            }
        }
    }

    $spreadsheet->disconnectWorksheets();
    unset($spreadsheet);
    gc_collect_cycles();
}

$helper->log('Done rendering charts as images');
