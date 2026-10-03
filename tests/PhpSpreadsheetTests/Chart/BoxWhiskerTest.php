<?php

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\BoxWhisker;
use PhpOffice\PhpSpreadsheet\Chart\BoxWhiskerSeries;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Exception;
use PHPUnit\Framework\TestCase;
use stdClass;

class BoxWhiskerTest extends TestCase
{
    public function testDefaultOptions(): void
    {
        $boxWhisker = new BoxWhisker([]);

        self::assertSame(BoxWhisker::TYPE, $boxWhisker->getChartType());
        self::assertSame([], $boxWhisker->getSeries());
        self::assertFalse($boxWhisker->getShowMeanLine());
        self::assertTrue($boxWhisker->getShowMeanMarker());
        self::assertFalse($boxWhisker->getShowInnerPoints());
        self::assertTrue($boxWhisker->getShowOutliers());
        self::assertSame(
            BoxWhisker::QUARTILE_METHOD_EXCLUSIVE,
            $boxWhisker->getQuartileMethod()
        );
    }

    public function testInvalidSeriesThrowsException(): void
    {
        $this->expectException(Exception::class);

        /** @phpstan-ignore argument.type (intentionally passing an invalid series type to test constructor validation) */
        new BoxWhisker([new stdClass()]);
    }

    public function testOptionsCanBeChanged(): void
    {
        $boxWhisker = new BoxWhisker([]);

        $boxWhisker->setShowMeanLine(true)
            ->setShowMeanMarker(false)
            ->setShowInnerPoints(true)
            ->setShowOutliers(false)
            ->setQuartileMethod(BoxWhisker::QUARTILE_METHOD_INCLUSIVE);

        self::assertTrue($boxWhisker->getShowMeanLine());
        self::assertFalse($boxWhisker->getShowMeanMarker());
        self::assertTrue($boxWhisker->getShowInnerPoints());
        self::assertFalse($boxWhisker->getShowOutliers());
        self::assertSame(
            BoxWhisker::QUARTILE_METHOD_INCLUSIVE,
            $boxWhisker->getQuartileMethod()
        );
    }

    public function testInvalidQuartileMethodThrowsException(): void
    {
        $this->expectException(Exception::class);

        (new BoxWhisker([]))->setQuartileMethod('invalid');
    }

    public function testChartCanContainChartEx(): void
    {
        $boxWhisker = new BoxWhisker([]);

        $chart = new Chart(
            'boxWhisker',
            new Title('Test'),
            null,
            null
        );

        $chart->setChartEx($boxWhisker);

        self::assertSame($boxWhisker, $chart->getChartEx());
        self::assertNull($chart->getPlotArea());
    }

    public function testChartCannotContainPlotAreaAndChartEx(): void
    {
        $chart = new Chart(
            'boxWhisker',
            new Title('Test'),
            null,
            null
        );

        $chart->setChartEx(new BoxWhisker([]));

        $this->expectException(Exception::class);

        $chart->setPlotArea(new \PhpOffice\PhpSpreadsheet\Chart\PlotArea());
    }

    public function testBoxWhiskerSeriesStoresValues(): void
    {
        $categories = new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_STRING,
            'Worksheet!$A$2:$A$4',
            null,
            3
        );

        $values = new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_NUMBER,
            'Worksheet!$B$2:$B$4',
            null,
            3
        );

        $label = new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_STRING,
            'Worksheet!$B$1',
            null,
            1
        );

        $series = new BoxWhiskerSeries(
            $categories,
            $values,
            $label
        );

        self::assertSame($categories, $series->getCategories());
        self::assertSame($values, $series->getValues());
        self::assertSame($label, $series->getLabel());
    }
}
