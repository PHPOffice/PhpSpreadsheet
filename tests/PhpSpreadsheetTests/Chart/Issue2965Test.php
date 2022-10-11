<?php

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PHPUnit\Framework\TestCase;

class Issue2965Test extends TestCase
{
    private const DIRECTORY = 'tests' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'Reader' . DIRECTORY_SEPARATOR . 'XLSX' . DIRECTORY_SEPARATOR;

    public function testPreliminaries(): void
    {
        $file = 'zip://';
        $file .= self::DIRECTORY . 'issue.2965.xlsx';
        $file .= '#xl/charts/chart1.xml';
        $data = file_get_contents($file);
        // confirm that file contains expected namespaced xml tag
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            self::assertStringContainsString('<c:title><c:tx><c:strRef><c:f>Sheet1!$A$1</c:f><c:strCache><c:ptCount val="1"/><c:pt idx="0"><c:v>NewTitle</c:v></c:pt></c:strCache></c:strRef></c:tx>', $data);
        }
    }

    public function testChartTitleFormula(): void
    {
        $reader = new XlsxReader();
        $reader->setIncludeCharts(true);
        $spreadsheet = $reader->load(self::DIRECTORY . 'issue.2965.xlsx');
        $worksheet = $spreadsheet->getActiveSheet();
        $charts = $worksheet->getChartCollection();
        self::assertCount(1, $charts);
        $originalChart1 = $charts[0];
        self::assertNotNull($originalChart1);
        $originalTitle1 = $originalChart1->getTitle();
        self::assertNotNull($originalTitle1);
        self::assertSame('NewTitle', $originalTitle1->getCaptionText());

        $spreadsheet->disconnectWorksheets();
    }
}
