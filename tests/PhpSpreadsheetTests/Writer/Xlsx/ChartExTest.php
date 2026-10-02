<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Chart\BoxWhisker;
use PhpOffice\PhpSpreadsheet\Chart\BoxWhiskerSeries;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\ChartEx;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PHPUnit\Framework\TestCase;
use stdClass;
use ZipArchive;

class ChartExTest extends TestCase
{
    public function testBoxWhiskerIsWrittenAsChartEx(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        $worksheet->fromArray([
            ['Group', 'Score A', 'Score B'],
            ['A', 45, 52],
            ['A', 48, 55],
            ['A', 51, 57],
            ['A', 53, 60],
            ['A', 58, 62],
            ['B', 41, 49],
            ['B', 46, 54],
            ['B', 50, 58],
            ['B', 55, 61],
            ['B', 60, 66],
        ]);

        $categories = new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_STRING,
            'Worksheet!$A$2:$A$11',
            null,
            10
        );

        $series = [
            new BoxWhiskerSeries(
                $categories,
                new DataSeriesValues(
                    DataSeriesValues::DATASERIES_TYPE_NUMBER,
                    'Worksheet!$B$2:$B$11',
                    '0',
                    10
                ),
                new DataSeriesValues(
                    DataSeriesValues::DATASERIES_TYPE_STRING,
                    'Worksheet!$B$1',
                    null,
                    1
                )
            ),
            new BoxWhiskerSeries(
                $categories,
                new DataSeriesValues(
                    DataSeriesValues::DATASERIES_TYPE_NUMBER,
                    'Worksheet!$C$2:$C$11',
                    '0',
                    10
                ),
                new DataSeriesValues(
                    DataSeriesValues::DATASERIES_TYPE_STRING,
                    'Worksheet!$C$1',
                    null,
                    1
                )
            ),
        ];

        $boxWhisker = new BoxWhisker($series);

        $chart = new Chart(
            'boxWhisker',
            new Title('Score Distribution'),
            null,
            null
        );

        $chart->setChartEx($boxWhisker)
            ->setTopLeftPosition('E2')
            ->setBottomRightPosition('L18');

        $worksheet->addChart($chart);

        $filename = tempnam(sys_get_temp_dir(), 'phpspreadsheet');

        self::assertIsString($filename);

        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true);
        $writer->save($filename);

        $zip = new ZipArchive();

        self::assertTrue($zip->open($filename));

        $chartXml = $zip->getFromName('xl/charts/chartEx1.xml');

        self::assertIsString($chartXml);
        self::assertStringContainsString(
            'http://schemas.microsoft.com/office/drawing/2014/chartex',
            $chartXml
        );
        self::assertSame(2, substr_count($chartXml, 'layoutId="boxWhisker"'));
        self::assertStringContainsString(
            'quartileMethod="exclusive"',
            $chartXml
        );
        self::assertStringContainsString(
            'meanLine="0"',
            $chartXml
        );
        self::assertStringContainsString(
            'meanMarker="1"',
            $chartXml
        );
        self::assertStringContainsString(
            'nonoutliers="0"',
            $chartXml
        );
        self::assertStringContainsString(
            'outliers="1"',
            $chartXml
        );
        self::assertStringContainsString(
            '_xlchart.v1.1',
            $chartXml
        );
        self::assertStringContainsString(
            '_xlchart.v1.2',
            $chartXml
        );
        self::assertStringContainsString(
            '_xlchart.v1.3',
            $chartXml
        );
        self::assertStringContainsString(
            '_xlchart.v1.4',
            $chartXml
        );

        $chartExRelationships = $zip->getFromName(
            'xl/charts/_rels/chartEx1.xml.rels'
        );

        self::assertIsString($chartExRelationships);
        self::assertStringContainsString(
            'http://schemas.microsoft.com/office/2011/relationships/chartColorStyle',
            $chartExRelationships
        );
        self::assertStringContainsString(
            'http://schemas.microsoft.com/office/2011/relationships/chartStyle',
            $chartExRelationships
        );
        self::assertStringContainsString(
            'Target="colors1.xml"',
            $chartExRelationships
        );
        self::assertStringContainsString(
            'Target="style1.xml"',
            $chartExRelationships
        );

        $style = $zip->getFromName('xl/charts/style1.xml');

        self::assertIsString($style);
        self::assertStringContainsString(
            '<cs:chartStyle',
            $style
        );
        self::assertStringContainsString(
            'id="406"',
            $style
        );

        $colourStyle = $zip->getFromName('xl/charts/colors1.xml');

        self::assertIsString($colourStyle);
        self::assertStringContainsString(
            '<cs:colorStyle',
            $colourStyle
        );
        self::assertStringContainsString(
            'meth="cycle"',
            $colourStyle
        );
        self::assertStringContainsString(
            'id="10"',
            $colourStyle
        );

        $contentTypes = $zip->getFromName('[Content_Types].xml');

        self::assertIsString($contentTypes);
        self::assertStringContainsString(
            'PartName="/xl/charts/chartEx1.xml"',
            $contentTypes
        );
        self::assertStringContainsString(
            'ContentType="application/vnd.ms-office.chartex+xml"',
            $contentTypes
        );
        self::assertStringContainsString(
            'PartName="/xl/charts/style1.xml"',
            $contentTypes
        );
        self::assertStringContainsString(
            'ContentType="application/vnd.ms-office.chartstyle+xml"',
            $contentTypes
        );
        self::assertStringContainsString(
            'PartName="/xl/charts/colors1.xml"',
            $contentTypes
        );
        self::assertStringContainsString(
            'ContentType="application/vnd.ms-office.chartcolorstyle+xml"',
            $contentTypes
        );

        $relationships = $zip->getFromName(
            'xl/drawings/_rels/drawing1.xml.rels'
        );

        self::assertIsString($relationships);
        self::assertStringContainsString(
            'http://schemas.microsoft.com/office/2014/relationships/chartEx',
            $relationships
        );
        self::assertStringContainsString(
            '../charts/chartEx1.xml',
            $relationships
        );

        $workbook = $zip->getFromName('xl/workbook.xml');

        self::assertIsString($workbook);
        self::assertStringContainsString(
            '_xlchart.v1.0',
            $workbook
        );
        self::assertStringContainsString(
            '_xlchart.v1.1',
            $workbook
        );
        self::assertStringContainsString(
            '_xlchart.v1.2',
            $workbook
        );
        self::assertStringContainsString(
            '_xlchart.v1.3',
            $workbook
        );
        self::assertStringContainsString(
            '_xlchart.v1.4',
            $workbook
        );
        self::assertStringContainsString(
            'Worksheet!$A$2:$A$11',
            $workbook
        );
        self::assertStringContainsString(
            'Worksheet!$B$2:$B$11',
            $workbook
        );
        self::assertStringContainsString(
            'Worksheet!$C$2:$C$11',
            $workbook
        );

        $zip->close();
        unlink($filename);
    }

    public function testBoxWhiskerIsWrittenAsChartExUsingDiskCaching(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        $worksheet->fromArray([
            ['Group', 'Score'],
            ['A', 45],
            ['A', 48],
        ]);

        $categories = new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_STRING,
            'Worksheet!$A$2:$A$3',
            null,
            2
        );

        $series = [
            new BoxWhiskerSeries(
                $categories,
                new DataSeriesValues(
                    DataSeriesValues::DATASERIES_TYPE_NUMBER,
                    'Worksheet!$B$2:$B$3',
                    null,
                    2
                )
            ),
        ];

        $chart = new Chart(
            'boxWhisker',
            null,
            null,
            null
        );

        $chart->setChartEx(new BoxWhisker($series));
        $worksheet->addChart($chart);

        $filename = tempnam(sys_get_temp_dir(), 'phpspreadsheet');

        self::assertIsString($filename);

        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true);
        $writer->setUseDiskCaching(true, sys_get_temp_dir());
        $writer->save($filename);

        $zip = new ZipArchive();

        self::assertTrue($zip->open($filename));
        self::assertIsString($zip->getFromName('xl/charts/chartEx1.xml'));

        $zip->close();
        unlink($filename);
    }

    public function testNonBoxWhiskerChartExIsNotWritten(): void
    {
        $chartEx = new class () implements ChartEx {
            public function getChartType(): string
            {
                return 'test';
            }

            public function refresh(Worksheet $worksheet): void
            {
            }
        };

        $chart = new Chart(
            'chartEx',
            null,
            null,
            null
        );

        $chart->setChartEx($chartEx);

        $writer = new Xlsx(new Spreadsheet());

        $chartExWriter = new Xlsx\ChartEx($writer);

        $this->expectException(WriterException::class);
        $this->expectExceptionMessage('Unsupported ChartEx type.');

        $chartExWriter->writeChart($chart);
    }

    public function testSetChartExThrowsWhenPlotAreaExists(): void
    {
        $chart = new Chart(
            'chart',
            null,
            null,
            new PlotArea()
        );

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(
            'A chart cannot contain both a PlotArea and ChartEx data.'
        );

        $chart->setChartEx(new BoxWhisker([]));
    }

    public function testInvalidSeriesThrowsException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(
            'BoxWhisker series must contain BoxWhiskerSeries objects.'
        );

        /** @phpstan-ignore argument.type (intentionally passing an invalid series type to test constructor validation) */
        new BoxWhisker([new stdClass()]);
    }
}
