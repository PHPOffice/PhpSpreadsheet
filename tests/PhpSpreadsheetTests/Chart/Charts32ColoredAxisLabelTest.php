<?php

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\RichText\Run;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class Charts32ColoredAxisLabelTest extends AbstractFunctional
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

    public function testStock5(): void
    {
        $file = self::DIRECTORY . '32readwriteStockChart5.xlsx';
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
        self::assertSame('Charts', $sheet->getTitle());
        $charts = $sheet->getChartCollection();
        self::assertCount(1, $charts);
        $chart = $charts[0];
        self::assertNotNull($chart);

        $xAxisLabel = $chart->getXAxisLabel();
        self::assertNotNull($xAxisLabel);
        $captionArray = $xAxisLabel->getCaption();
        self::assertIsArray($captionArray);
        self::assertCount(1, $captionArray);
        $caption = $captionArray[0];
        self::assertInstanceOf(RichText::class, $caption);
        self::assertSame('X-Axis Title in Green', $caption->getPlainText());
        $elements = $caption->getRichTextElements();
        self::assertCount(1, $elements);
        $run = $elements[0];
        self::assertInstanceOf(Run::class, $run);
        $font = $run->getFont();
        self::assertInstanceOf(Font::class, $font);
        $chartColor = $font->getChartColor();
        self::assertNotNull($chartColor);
        self::assertSame('00B050', $chartColor->getValue());
        self::assertSame('srgbClr', $chartColor->getType());

        $yAxisLabel = $chart->getYAxisLabel();
        self::assertNotNull($yAxisLabel);
        $captionArray = $yAxisLabel->getCaption();
        self::assertIsArray($captionArray);
        self::assertCount(1, $captionArray);
        $caption = $captionArray[0];
        self::assertInstanceOf(RichText::class, $caption);
        self::assertSame('Y-Axis Title in Red', $caption->getPlainText());
        $elements = $caption->getRichTextElements();
        self::assertCount(1, $elements);
        $run = $elements[0];
        self::assertInstanceOf(Run::class, $run);
        $font = $run->getFont();
        self::assertInstanceOf(Font::class, $font);
        $chartColor = $font->getChartColor();
        self::assertNotNull($chartColor);
        self::assertSame('FF0000', $chartColor->getValue());
        self::assertSame('srgbClr', $chartColor->getType());

        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
