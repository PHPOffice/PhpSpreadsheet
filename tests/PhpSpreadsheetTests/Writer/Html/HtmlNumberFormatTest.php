<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use DOMDocument;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use PhpOffice\PhpSpreadsheetTests\Functional;
use PHPUnit\Framework\Attributes\DataProvider;

class HtmlNumberFormatTest extends Functional\AbstractFunctional
{
    protected function setUp(): void
    {
        StringHelper::setCurrencyCode('$');
        StringHelper::setDecimalSeparator('.');
        StringHelper::setThousandsSeparator(',');
    }

    protected function tearDown(): void
    {
        StringHelper::setCurrencyCode(null);
        StringHelper::setDecimalSeparator(null);
        StringHelper::setThousandsSeparator(null);
    }

    public function testColorNumberFormat(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', -50);
        $sheet->setCellValue('A2', 3000);
        $sheet->setCellValue('A3', 0);
        $sheet->setCellValue('A4', '<br>');
        $fmt = '[Blue]$#,##0;[Red]$#,##0;$#,##0';
        $sheet->getStyle('A1:A4')->getNumberFormat()->setFormatCode($fmt);

        $writer = new Html($spreadsheet);
        $html = $writer->generateHTMLAll();
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        self::assertNotNull($body);
        $divs = $body->getElementsByTagName('div');

        $tabl = $divs->item(0)?->getElementsByTagName('table');
        $tbod = $tabl?->item(0)?->getElementsByTagName('tbody');
        $rows = $tbod?->item(0)?->getElementsByTagName('tr');
        self::assertCount(4, $rows);

        $tds = $rows?->item(0)?->getElementsByTagName('td');
        self::assertCount(1, $tds);
        $spans = $tds?->item(0)?->getElementsByTagName('span');
        self::assertCount(1, $spans);
        $style = $spans?->item(0)?->getAttribute('style');
        self::assertSame(1, preg_match('/color:red/', "$style"));
        self::assertSame('$50', $spans?->item(0)?->textContent);

        $tds = $rows?->item(1)?->getElementsByTagName('td');
        self::assertCount(1, $tds);
        $spans = $tds?->item(0)?->getElementsByTagName('span');
        self::assertCount(1, $spans);
        $style = $spans?->item(0)?->getAttribute('style');
        self::assertSame(1, preg_match('/color:blue/', "$style"));
        self::assertSame('$3,000', $spans?->item(0)?->textContent);

        $tds = $rows?->item(2)?->getElementsByTagName('td');
        self::assertCount(1, $tds);
        $spans = $tds?->item(0)?->getElementsByTagName('span');
        self::assertCount(0, $spans);
        self::assertSame('$0', $tds?->item(0)?->textContent);

        $tds = $rows?->item(3)?->getElementsByTagName('td');
        self::assertCount(1, $tds);
        $spans = $tds?->item(0)?->getElementsByTagName('span');
        self::assertCount(0, $spans);
        self::assertEquals('<br>', $tds?->item(0)?->textContent);

        $rls = $this->writeAndReload($spreadsheet, 'Html');
        $spreadsheet->disconnectWorksheets();
        $rls->disconnectWorksheets();
    }

    public function testColorNumberFormatComplex(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', -50);
        $sheet->setCellValue('A2', 3000.75);
        $sheet->setCellValue('A3', 0);
        $sheet->setCellValue('A4', 3000.25);
        $fmt = '[Blue][>=3000.5]$#,##0.00;[Red][<0]$#,##0.00;$#,##0.00';
        $sheet->getStyle('A1:A4')->getNumberFormat()->setFormatCode($fmt);

        $writer = new Html($spreadsheet);
        $html = $writer->generateHTMLAll();
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        self::assertNotNull($body);
        $divs = $body->getElementsByTagName('div');

        $tabl = $divs->item(0)?->getElementsByTagName('table');
        $tbod = $tabl?->item(0)?->getElementsByTagName('tbody');
        $rows = $tbod?->item(0)?->getElementsByTagName('tr');
        self::assertCount(4, $rows);

        $tds = $rows?->item(0)?->getElementsByTagName('td');
        self::assertCount(1, $tds);
        $spans = $tds?->item(0)?->getElementsByTagName('span');
        self::assertCount(1, $spans);
        $style = $spans?->item(0)?->getAttribute('style');
        self::assertSame(1, preg_match('/color:red/', "$style"));
        self::assertSame('$50.00', $spans?->item(0)?->textContent);

        $tds = $rows?->item(1)?->getElementsByTagName('td');
        self::assertCount(1, $tds);
        $spans = $tds?->item(0)?->getElementsByTagName('span');
        self::assertCount(1, $spans);
        $style = $spans?->item(0)?->getAttribute('style');
        self::assertSame(1, preg_match('/color:blue/', "$style"));
        self::assertSame('$3,000.75', $spans?->item(0)?->textContent);

        $tds = $rows?->item(2)?->getElementsByTagName('td');
        self::assertCount(1, $tds);
        $spans = $tds?->item(0)?->getElementsByTagName('span');
        self::assertCount(0, $spans);
        self::assertSame('$0.00', $tds?->item(0)?->textContent);

        $tds = $rows?->item(3)?->getElementsByTagName('td');
        self::assertCount(1, $tds);
        $spans = $tds?->item(0)?->getElementsByTagName('span');
        self::assertCount(0, $spans);
        self::assertSame('$3,000.25', $tds?->item(0)?->textContent);

        $rls = $this->writeAndReload($spreadsheet, 'Html');
        $spreadsheet->disconnectWorksheets();
        $rls->disconnectWorksheets();
    }

    #[DataProvider('numberFormatProvider')]
    public function testFormatValueWithMask(mixed $expectedResult, mixed $val, string $fmt): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue($val)->getStyle()->getNumberFormat()->setFormatCode($fmt);

        $writer = new Html($spreadsheet);
        $html = $writer->generateHTMLAll();
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        self::assertNotNull($body);
        $divs = $body->getElementsByTagName('div');

        $tabl = $divs->item(0)?->getElementsByTagName('table');
        $tbod = $tabl?->item(0)?->getElementsByTagName('tbody');
        $rows = $tbod?->item(0)?->getElementsByTagName('tr');

        $tds = $rows?->item(0)?->getElementsByTagName('td');
        $nbsp = html_entity_decode('&nbsp;', Settings::htmlEntityFlags());
        self::assertEquals($expectedResult, str_replace($nbsp, ' ', (string) $tds?->item(0)?->textContent));

        $rls = $this->writeAndReload($spreadsheet, 'Html');
        $spreadsheet->disconnectWorksheets();
        $rls->disconnectWorksheets();
    }

    /** @return mixed[] */
    public static function numberFormatProvider(): array
    {
        /** @var mixed[] */
        $retVal = require __DIR__ . '/../../../data/Style/NumberFormat.php';

        return $retVal;
    }

    #[DataProvider('numberFormatDatesProvider')]
    public function testFormatValueWithMaskDate(mixed $expectedResult, mixed $val, string $fmt): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue($val)->getStyle()->getNumberFormat()->setFormatCode($fmt);

        $writer = new Html($spreadsheet);
        $html = $writer->generateHTMLAll();
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        self::assertNotNull($body);
        $divs = $body->getElementsByTagName('div');

        $tabl = $divs->item(0)?->getElementsByTagName('table');
        $tbod = $tabl?->item(0)?->getElementsByTagName('tbody');
        $rows = $tbod?->item(0)?->getElementsByTagName('tr');

        $tds = $rows?->item(0)?->getElementsByTagName('td');
        $nbsp = html_entity_decode('&nbsp;', Settings::htmlEntityFlags());
        self::assertSame($expectedResult, str_replace($nbsp, ' ', (string) $tds?->item(0)?->textContent));

        $rls = $this->writeAndReload($spreadsheet, 'Html');
        $spreadsheet->disconnectWorksheets();
        $rls->disconnectWorksheets();
    }

    /** @return mixed[] */
    public static function numberFormatDatesProvider(): array
    {
        /** @var mixed[] */
        $retVal = require __DIR__ . '/../../../data/Style/NumberFormatDates.php';

        return $retVal;
    }
}
