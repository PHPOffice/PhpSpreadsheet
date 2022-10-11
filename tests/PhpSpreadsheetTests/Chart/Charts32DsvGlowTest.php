<?php

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class Charts32DsvGlowTest extends AbstractFunctional
{
    private const DIRECTORY = 'samples' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR;

    public function readCharts(XlsxReader $reader): void
    {
        $reader->setIncludeCharts(true);
    }

    public function writeCharts(XlsxWriter $writer): void
    {
        $writer->setIncludeCharts(true);
    }

    public function testLine4(): void
    {
        $file = self::DIRECTORY . '32readwriteLineChart4.xlsx';
        $reader = new XlsxReader();
        $reader->setIncludeCharts(true);
        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame(1, $sheet->getChartCount());
        /** @var callable */
        $callableReader = [$this, 'readCharts'];
        /** @var callable */
        $callableWriter = [$this, 'writeCharts'];
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx', $callableReader, $callableWriter);
        $spreadsheet->disconnectWorksheets();

        $sheet = $reloadedSpreadsheet->getActiveSheet();
        $charts = $sheet->getChartCollection();
        self::assertCount(1, $charts);
        $chart = $charts[0];
        self::assertNotNull($chart);

        $plotArea = $chart->getPlotArea();
        self::assertNotNull($plotArea);
        $dataSeriesArray = $plotArea->getPlotGroup();
        self::assertCount(1, $dataSeriesArray);
        $dataSeries = $dataSeriesArray[0];
        $dataSeriesValuesArray = $dataSeries->getPlotValues();
        self::assertCount(3, $dataSeriesValuesArray);
        $dataSeriesValues = $dataSeriesValuesArray[1];
        self::assertEquals(5, $dataSeriesValues->getGlowSize());
        self::assertSame('schemeClr', $dataSeriesValues->getGlowProperty(['color', 'type']));
        self::assertSame('accent2', $dataSeriesValues->getGlowProperty(['color', 'value']));
        self::assertSame(60, $dataSeriesValues->getGlowProperty(['color', 'alpha']));

        $yAxis = $chart->getChartAxisY();
        $majorGridlines = $yAxis->getMajorGridlines();
        self::assertNotNull($majorGridlines);
        self::assertSame('triangle', $majorGridlines->getLineStyleProperty(['arrow', 'head', 'type']));
        self::assertSame('triangle', $majorGridlines->getLineStyleProperty(['arrow', 'end', 'type']));
        $minorGridlines = $yAxis->getMinorGridlines();
        self::assertNotNull($minorGridlines);
        self::assertSame('sysDot', $minorGridlines->getLineStyleProperty('dash'));
        self::assertSame('FFC000', $minorGridlines->getLineColor()->getValue());

        $xAxis = $chart->getChartAxisX();
        $majorGridlines = $xAxis->getMajorGridlines();
        $minorGridlines = $xAxis->getMinorGridlines();
        self::assertNotNull($majorGridlines);
        self::assertSame('7030A0', $majorGridlines->getLineColor()->getValue());
        self::assertNotNull($minorGridlines);
        self::assertFalse($minorGridlines->getLineColor()->isUsable());

        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
