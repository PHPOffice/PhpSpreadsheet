<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class TitleTest extends AbstractFunctional
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

    public function testTitleHasCenteredOverlay(): void
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
        $title = $chart->getTitle();
        self::assertNotNull($title);
        self::assertTrue($title->getOverlay());

        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testTitleIsAboveChart(): void
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
        $title = $chart->getTitle();
        self::assertNotNull($title);
        self::assertFalse($title->getOverlay());

        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testString(): void
    {
        $title = new Title('hello');
        self::assertSame('hello', $title->getCaption());
        self::assertSame('hello', $title->getCaptionText());
    }

    public function testStringArray(): void
    {
        $title = new Title();
        $title->setCaption(['Hello', ', ', 'world.']);
        self::assertSame('Hello, world.', $title->getCaptionText());
    }

    public function testRichText(): void
    {
        $title = new Title();
        $richText = new RichText();
        $part = $richText->createTextRun('Hello');
        $font = $part->getFont();
        if ($font === null) {
            self::fail('Unable to retrieve font');
        } else {
            $font->setBold(true);
            $title->setCaption($richText);
            self::assertSame('Hello', $title->getCaptionText());
        }
    }

    public function testMixedArray(): void
    {
        $title = new Title();
        $richText1 = new RichText();
        $part1 = $richText1->createTextRun('Hello');
        $font1 = $part1->getFont();
        $richText2 = new RichText();
        $part2 = $richText2->createTextRun('world');
        $font2 = $part2->getFont();
        if ($font1 === null || $font2 === null) {
            self::fail('Unable to retrieve font');
        } else {
            $font1->setBold(true);
            $font2->setItalic(true);
            $title->setCaption([$richText1, ', ', $richText2, '.']);
            self::assertSame('Hello, world.', $title->getCaptionText());
        }
    }

    public function testSetOverlay(): void
    {
        $overlayValues = [
            true,
            false,
        ];

        $testInstance = new Title();

        foreach ($overlayValues as $overlayValue) {
            $testInstance->setOverlay($overlayValue);
            self::assertSame($overlayValue, $testInstance->getOverlay());
        }
    }

    public function testGetOverlay(): void
    {
        $overlayValue = true;

        $testInstance = new Title();
        $testInstance->setOverlay($overlayValue);

        $result = $testInstance->getOverlay();
        self::assertEquals($overlayValue, $result);
    }
}
