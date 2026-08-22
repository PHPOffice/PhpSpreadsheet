<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class Issue2506Test extends AbstractFunctional
{
    private const DIRECTORY = 'tests' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'Reader' . DIRECTORY_SEPARATOR . 'XLSX' . DIRECTORY_SEPARATOR;

    public function readCharts(XlsxReader $reader): void
    {
        $reader->setIncludeCharts(true);
    }

    public function writeCharts(XlsxWriter $writer): void
    {
        $writer->setIncludeCharts(true);
    }

    public function testDataSeriesValues(): void
    {
        $reader = new XlsxReader();
        $this->readCharts($reader);
        $spreadsheet = $reader->load(self::DIRECTORY . 'issue.2506.xlsx');
        $worksheet = $spreadsheet->getActiveSheet();
        $charts = $worksheet->getChartCollection();
        self::assertCount(4, $charts);
        $originalChart1 = $charts[0];
        self::assertNotNull($originalChart1);
        $originalPlotArea1 = $originalChart1->getPlotArea();
        self::assertNotNull($originalPlotArea1);
        $originalPlotSeries1 = $originalPlotArea1->getPlotGroup();
        self::assertCount(1, $originalPlotSeries1);
        self::assertSame('0', $originalPlotSeries1[0]->getPlotStyle());
        $originalChart2 = $charts[1];
        self::assertNotNull($originalChart2);
        $originalPlotArea2 = $originalChart2->getPlotArea();
        self::assertNotNull($originalPlotArea2);
        $originalPlotSeries2 = $originalPlotArea2->getPlotGroup();
        self::assertCount(1, $originalPlotSeries2);
        self::assertSame('5', $originalPlotSeries2[0]->getPlotStyle());

        /** @var callable */
        $callableReader = [$this, 'readCharts'];
        /** @var callable */
        $callableWriter = [$this, 'writeCharts'];
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx', $callableReader, $callableWriter);
        $spreadsheet->disconnectWorksheets();

        $sheet = $reloadedSpreadsheet->getActiveSheet();
        $charts2 = $sheet->getChartCollection();
        self::assertCount(4, $charts2);
        $chart1 = $charts[0];
        self::assertNotNull($chart1);
        $plotArea1 = $chart1->getPlotArea();
        self::assertNotNull($plotArea1);
        $plotSeries1 = $plotArea1->getPlotGroup();
        self::assertCount(1, $plotSeries1);
        self::assertSame('0', $plotSeries1[0]->getPlotStyle());
        $chart2 = $charts[1];
        self::assertNotNull($chart2);
        $plotArea2 = $chart2->getPlotArea();
        self::assertNotNull($plotArea2);
        $plotSeries2 = $plotArea2->getPlotGroup();
        self::assertCount(1, $plotSeries2);
        self::assertSame('5', $plotSeries2[0]->getPlotStyle());

        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
