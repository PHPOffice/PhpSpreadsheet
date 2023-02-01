<?php

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class LegendTest extends AbstractFunctional
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

    public function testLegendWithOverlay(): void
    {
        $file = self::DIRECTORY . 'chart-with-and-without-overlays.xlsx';
        $reader = new XlsxReader();
        $reader->setIncludeCharts(true);
        $spreadsheet = $reader->load($file);
        $sheetName = 'With Overlay';
        $sheet = $spreadsheet->getSheetByName($sheetName);
        self::assertNotNull($sheet);
        self::assertSame(1, $sheet->getChartCount());
        /** @var callable */
        $callableReader = [$this, 'readCharts'];
        /** @var callable */
        $callableWriter = [$this, 'writeCharts'];
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx', $callableReader, $callableWriter);
        $spreadsheet->disconnectWorksheets();

        $sheet = $reloadedSpreadsheet->getSheetByName($sheetName);
        self::assertNotNull($sheet);
        $charts = $sheet->getChartCollection();
        self::assertCount(1, $charts);
        $chart = $charts[0];
        self::assertNotNull($chart);
        $legend = $chart->getLegend();
        self::assertNotNull($legend);
        self::assertTrue($legend->getOverlay());

        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testLegendWithoutOverlay(): void
    {
        $file = self::DIRECTORY . 'chart-with-and-without-overlays.xlsx';
        $reader = new XlsxReader();
        $reader->setIncludeCharts(true);
        $spreadsheet = $reader->load($file);
        $sheetName = 'Without Overlay';
        $sheet = $spreadsheet->getSheetByName($sheetName);
        self::assertNotNull($sheet);
        self::assertSame(1, $sheet->getChartCount());
        /** @var callable */
        $callableReader = [$this, 'readCharts'];
        /** @var callable */
        $callableWriter = [$this, 'writeCharts'];
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx', $callableReader, $callableWriter);
        $spreadsheet->disconnectWorksheets();

        $sheet = $reloadedSpreadsheet->getSheetByName($sheetName);
        self::assertNotNull($sheet);
        $charts = $sheet->getChartCollection();
        self::assertCount(1, $charts);
        $chart = $charts[0];
        self::assertNotNull($chart);
        $legend = $chart->getLegend();
        self::assertNotNull($legend);
        self::assertFalse($legend->getOverlay());

        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testSetPosition(): void
    {
        $positionValues = [
            Legend::POSITION_RIGHT,
            Legend::POSITION_LEFT,
            Legend::POSITION_TOP,
            Legend::POSITION_BOTTOM,
            Legend::POSITION_TOPRIGHT,
        ];

        $testInstance = new Legend();

        foreach ($positionValues as $positionValue) {
            $result = $testInstance->setPosition($positionValue);
            self::assertTrue($result);
        }
    }

    public function testSetInvalidPositionReturnsFalse(): void
    {
        $testInstance = new Legend();

        $result = $testInstance->setPosition('BottomLeft');
        self::assertFalse($result);
        //    Ensure that value is unchanged
        $result = $testInstance->getPosition();
        self::assertEquals(Legend::POSITION_RIGHT, $result);
    }

    public function testGetPosition(): void
    {
        $PositionValue = Legend::POSITION_BOTTOM;

        $testInstance = new Legend();
        $testInstance->setPosition($PositionValue);

        $result = $testInstance->getPosition();
        self::assertEquals($PositionValue, $result);
    }

    public function testSetPositionXL(): void
    {
        $positionValues = [
            Legend::XL_LEGEND_POSITION_BOTTOM,
            Legend::XL_LEGEND_POSITION_CORNER,
            Legend::XL_LEGEND_POSITION_CUSTOM,
            Legend::XL_LEGEND_POSITION_LEFT,
            Legend::XL_LEGEND_POSITION_RIGHT,
            Legend::XL_LEGEND_POSITION_TOP,
        ];

        $testInstance = new Legend();

        foreach ($positionValues as $positionValue) {
            $result = $testInstance->setPositionXL($positionValue);
            self::assertTrue($result);
        }
    }

    public function testSetInvalidXLPositionReturnsFalse(): void
    {
        $testInstance = new Legend();

        $result = $testInstance->setPositionXL(999);
        self::assertFalse($result);
        //    Ensure that value is unchanged
        $result = $testInstance->getPositionXL();
        self::assertEquals(Legend::XL_LEGEND_POSITION_RIGHT, $result);
    }

    public function testGetPositionXL(): void
    {
        $PositionValue = Legend::XL_LEGEND_POSITION_CORNER;

        $testInstance = new Legend();
        $testInstance->setPositionXL($PositionValue);

        $result = $testInstance->getPositionXL();
        self::assertEquals($PositionValue, $result);
    }

    public function testSetOverlay(): void
    {
        $overlayValues = [
            true,
            false,
        ];

        $testInstance = new Legend();

        foreach ($overlayValues as $overlayValue) {
            $testInstance->setOverlay($overlayValue);
            self::assertSame($overlayValue, $testInstance->getOverlay());
        }
    }

    public function testGetOverlay(): void
    {
        $OverlayValue = true;

        $testInstance = new Legend();
        $testInstance->setOverlay($OverlayValue);

        $result = $testInstance->getOverlay();
        self::assertEquals($OverlayValue, $result);
    }
}
