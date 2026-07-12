<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use finfo;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\Renderer\MtJpGraphRenderer;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Settings;
use PHPUnit\Framework\TestCase;

class RenderTest extends TestCase
{
    protected function tearDown(): void
    {
        Settings::unsetChartRenderer();
    }

    public function testNoRenderer(): void
    {
        $chart = new Chart('Chart1');
        self::assertFalse($chart->render());
    }

    public function testPhpOutput(): void
    {
        $infile = 'samples/templates/32readwriteAreaChart1.xlsx';
        $reader = new XlsxReader();
        $reader->setIncludeCharts(true);
        $spreadsheet = $reader->load($infile);
        $sheet = $spreadsheet->getActiveSheet();
        $charts = $sheet->getChartCollection();
        self::assertCount(1, $charts);
        $chart = $charts[0];
        self::assertInstanceOf(Chart::class, $chart);
        Settings::setChartRenderer(MtJpGraphRenderer::class);
        ob_start();
        $chart->render('php://output');
        $data = ob_get_clean();
        self::assertNotFalse($data);
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $type = $finfo->buffer($data);
        self::assertSame('image/png', $type);
        $spreadsheet->disconnectWorksheets();
    }
}
