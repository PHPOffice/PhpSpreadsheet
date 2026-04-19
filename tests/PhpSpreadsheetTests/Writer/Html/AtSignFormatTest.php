<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;
use PHPUnit\Framework\TestCase;

class AtSignFormatTest extends TestCase
{
    /** @dataProvider providerFormat */
    public function testAtSignFormat(string $formatCode): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $payload = '<img src=x onerror=alert(document.domain)>';
        $sheet->setCellValue('A1', $payload);
        $sheet->getStyle('A1')
            ->getNumberFormat()
            ->setFormatCode($formatCode);

        $writer = new HtmlWriter($spreadsheet);
        $html = $writer->generateHTMLAll();
        self::assertStringContainsString('&lt;img src=x onerror=alert(document.domain)&gt', $html);
        self::assertStringNotContainsString('<img src=x onerror=alert(document.domain)>', $html);
        $spreadsheet->disconnectWorksheets();
    }

    /** @return array<array{string}> */
    public static function providerFormat(): array
    {
        return [
            ['General'],
            ['#'],
            ['yyyy-mm-dd'],
            ['0%'],
            ['@'],
            ['@ "items"'],
            ['. @'],
            ['@ '],
        ];
    }
}
