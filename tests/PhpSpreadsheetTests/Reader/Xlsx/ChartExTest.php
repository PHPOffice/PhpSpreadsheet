<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Chart\BoxWhisker;
use PhpOffice\PhpSpreadsheet\Chart\BoxWhiskerSeries;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PHPUnit\Framework\TestCase;

class ChartExTest extends TestCase
{
    private const FILENAME = 'tests/data/Reader/XLSX/ChartExBoxWhisker.xlsx';

    public function testReadBoxWhiskerChartEx(): void
    {
        $reader = new Xlsx();
        $reader->setIncludeCharts(true);

        $spreadsheet = $reader->load(self::FILENAME);

        $worksheet = $spreadsheet->getActiveSheet();

        self::assertCount(1, $worksheet->getChartCollection());

        $chart = $worksheet->getChartByIndex(0);

        self::assertNotFalse($chart);
        self::assertNotNull($chart->getChartEx());

        $boxWhisker = $chart->getChartEx();

        self::assertInstanceOf(BoxWhisker::class, $boxWhisker);
        self::assertCount(3, $boxWhisker->getSeries());

        $series = $boxWhisker->getSeries();

        self::assertNotNull($series[0]->getLabel());
        self::assertSame('Worksheet!$A$1', $series[0]->getLabel()->getDataSource());

        self::assertNotNull($series[1]->getLabel());
        self::assertSame('Worksheet!$B$1', $series[1]->getLabel()->getDataSource());

        self::assertNotNull($series[2]->getLabel());
        self::assertSame('Worksheet!$C$1', $series[2]->getLabel()->getDataSource());

        self::assertInstanceOf(BoxWhiskerSeries::class, $series[0]);
        self::assertSame('Worksheet!$A$1', $series[0]->getCategories()->getDataSource());
        self::assertSame('Worksheet!$A$2:$A$6', $series[0]->getValues()->getDataSource());

        self::assertInstanceOf(BoxWhiskerSeries::class, $series[1]);
        self::assertSame('Worksheet!$B$1', $series[1]->getCategories()->getDataSource());
        self::assertSame('Worksheet!$B$2:$B$6', $series[1]->getValues()->getDataSource());

        self::assertInstanceOf(BoxWhiskerSeries::class, $series[2]);
        self::assertSame('Worksheet!$C$1', $series[2]->getCategories()->getDataSource());
        self::assertSame('Worksheet!$C$2:$C$6', $series[2]->getValues()->getDataSource());

        $title = $chart->getTitle();

        self::assertNotNull($title);
        self::assertSame('Box & Whisker Chart', $title->getCaptionText($spreadsheet));

        self::assertTrue($boxWhisker->getShowMeanLine());
        self::assertTrue($boxWhisker->getShowMeanMarker());
        self::assertTrue($boxWhisker->getShowInnerPoints());
        self::assertTrue($boxWhisker->getShowOutliers());
        self::assertSame(BoxWhisker::QUARTILE_METHOD_EXCLUSIVE, $boxWhisker->getQuartileMethod());

        $spreadsheet->disconnectWorksheets();
    }

    public function testDoesNotReadChartExWhenChartsAreDisabled(): void
    {
        $reader = new Xlsx();

        $spreadsheet = $reader->load(self::FILENAME);

        self::assertCount(0, $spreadsheet->getActiveSheet()->getChartCollection());

        $spreadsheet->disconnectWorksheets();
    }
}
