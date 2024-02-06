<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Settings;

require __DIR__ . '/../Header.php';

$inputFileType = 'Xlsx';
$inputFileName = __DIR__ . '/../templates/37dynamictitle.xlsx';
var_dump($inputFileName);
var_dump(realpath($inputFileName));
$inputFileNames = [$inputFileName];

foreach ($inputFileNames as $inputFileName) {
    $inputFileNameShort = basename($inputFileName);

    if (!file_exists($inputFileName)) {
        $helper->log('File ' . $inputFileNameShort . ' does not exist');

        continue;
    }
    $reader = IOFactory::createReader($inputFileType);
    $reader->setIncludeCharts(true);
    $callStartTime = microtime(true);
    $spreadsheet = $reader->load($inputFileName);
    $helper->logRead($inputFileType, $inputFileName, $callStartTime);

    $helper->log('Iterate worksheets looking at the charts');
    foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
        $sheetName = $worksheet->getTitle();
        $worksheet->getCell('A1')->setValue('Changed Title');
        $helper->log('Worksheet: ' . $sheetName);

        $chartNames = $worksheet->getChartNames();
        if (empty($chartNames)) {
            $helper->log('    There are no charts in this worksheet');
        } else {
            natsort($chartNames);
            foreach ($chartNames as $i => $chartName) {
                $chart = $worksheet->getChartByNameOrThrow($chartName);
                if ($chart->getTitle() !== null) {
                    $caption = '"' . $chart->getTitle()->getCaptionText($spreadsheet) . '"';
                } else {
                    $caption = 'Untitled';
                }
                $helper->log('    ' . $chartName . ' - ' . $caption);
                $indentation = str_repeat(' ', strlen($chartName) + 3);
                $groupCount = $chart->getPlotAreaOrThrow()->getPlotGroupCount();
                if ($groupCount == 1) {
                    $chartType = $chart->getPlotAreaOrThrow()->getPlotGroupByIndex(0)->getPlotType();
                    $helper->log($indentation . '    ' . $chartType);
                    $helper->renderChart($chart, __FILE__, $spreadsheet);
                } else {
                    $chartTypes = [];
                    for ($i = 0; $i < $groupCount; ++$i) {
                        $chartTypes[] = $chart->getPlotAreaOrThrow()->getPlotGroupByIndex($i)->getPlotType();
                    }
                    $chartTypes = array_unique($chartTypes);
                    if (count($chartTypes) == 1) {
                        $chartType = 'Multiple Plot ' . array_pop($chartTypes);
                        $helper->log($indentation . '    ' . $chartType);
                        $helper->renderChart($chart, __FILE__);
                    } elseif (count($chartTypes) == 0) {
                        $helper->log($indentation . '    *** Type not yet implemented');
                    } else {
                        $helper->log($indentation . '    Combination Chart');
                        $helper->renderChart($chart, __FILE__);
                    }
                }
            }
        }
    }

    $callStartTime = microtime(true);
    $helper->write($spreadsheet, $inputFileName, ['Xlsx'], true);

    Settings::setChartRenderer(PhpOffice\PhpSpreadsheet\Chart\Renderer\MtJpGraphRenderer::class);
    $callStartTime = microtime(true);
    $helper->write($spreadsheet, $inputFileName, ['Html'], true);

    $spreadsheet->disconnectWorksheets();
    unset($spreadsheet);
}
