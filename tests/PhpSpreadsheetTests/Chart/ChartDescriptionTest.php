<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;
use ZipArchive;

class ChartDescriptionTest extends TestCase
{
    public function testDescriptionAndNameOfEveryAnchor(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([['L1', 1], ['L2', 3], ['L3', 2]]);
        $twoCell = self::chart('Sales', 'Sales rise in L2');
        $twoCell->setTopLeftPosition('D1');
        $twoCell->setBottomRightPosition('J10');
        $sheet->addChart($twoCell);
        $oneCell = self::chart('', 'One cell');
        $oneCell->setTopLeftPosition('D12');
        $oneCell->setBottomRightPosition('', 300, 200);
        $oneCell->setOneCellAnchor(true);
        $sheet->addChart($oneCell);
        $absolute = self::chart('Absolute', 'Absolute anchor');
        $absolute->setBottomRightPosition('', 300, 200);
        $sheet->addChart($absolute);
        $sheet->addChart(self::chart('Plain', ''));

        $file = File::temporaryFilename();
        $writer = new XlsxWriter($spreadsheet);
        $writer->setIncludeCharts(true);
        $writer->save($file);
        $spreadsheet->disconnectWorksheets();

        $zip = new ZipArchive();
        $zip->open($file);
        $drawing = (string) $zip->getFromName('xl/drawings/drawing1.xml');
        $zip->close();
        preg_match_all('~<xdr:cNvPr [^>]*>~', $drawing, $frames);
        self::assertSame([
            '<xdr:cNvPr name="Sales" id="1025" descr="Sales rise in L2"/>',
            '<xdr:cNvPr name="Chart 2" id="2050" descr="One cell"/>',
            '<xdr:cNvPr name="Absolute" id="3075" descr="Absolute anchor"/>',
            '<xdr:cNvPr name="Plain" id="4100"/>',
        ], $frames[0]);

        $reader = new XlsxReader();
        $reader->setIncludeCharts(true);
        $reloaded = $reader->load($file);
        unlink($file);
        $descriptions = [];
        foreach ($reloaded->getActiveSheet()->getChartCollection() as $chart) {
            $descriptions[] = $chart->getDescription();
        }
        self::assertSame(['Sales rise in L2', 'One cell', 'Absolute anchor', ''], $descriptions);
        // The reader keeps naming a chart after its part, as getChartByName() callers expect
        self::assertSame(['chart1', 'chart2', 'chart3', 'chart4'], $reloaded->getActiveSheet()->getChartNames());
        $reloaded->disconnectWorksheets();
    }

    private static function chart(string $name, string $description): Chart
    {
        $series = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_STANDARD,
            [0],
            [],
            [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$A$1:$A$3')],
            [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$B$1:$B$3')],
        );

        return (new Chart($name, plotArea: new PlotArea(null, [$series])))->setDescription($description);
    }
}
