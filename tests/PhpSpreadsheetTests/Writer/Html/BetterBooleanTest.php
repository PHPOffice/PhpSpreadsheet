<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Reader\Html as HtmlReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;
use PhpOffice\PhpSpreadsheetTests\Functional;

class BetterBooleanTest extends Functional\AbstractFunctional
{
    private string $locale;

    protected function setUp(): void
    {
        $calculation = Calculation::getInstance();
        $this->locale = $calculation->getLocale();
    }

    protected function tearDown(): void
    {
        $calculation = Calculation::getInstance();
        $calculation->setLocale($this->locale);
    }

    public function testDefault(): void
    {
        $spreadsheet = new Spreadsheet();
        $writer = new HtmlWriter($spreadsheet);
        // Default will change with next PhpSpreadsheet release
        self::assertFalse($writer->getBetterBoolean());
        $spreadsheet->disconnectWorksheets();
    }

    public function setBetter(HtmlWriter $writer): void
    {
        $writer->setBetterBoolean(true);
    }

    public function setNotBetter(HtmlWriter $writer): void
    {
        $writer->setBetterBoolean(false);
    }

    public function testBetterBoolean(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue(10);
        $sheet->getCell('B1')->setValue('Hello');
        $sheet->getCell('C1')->setValue(true);
        $sheet->getCell('D1')->setValue('=IF(1>2, TRUE, FALSE)');

        /** @var callable */
        $callableWriter = [$this, 'setBetter'];
        $reloaded = $this->writeAndReload($spreadsheet, 'Html', null, $callableWriter);
        $spreadsheet->disconnectWorksheets();

        $rsheet = $reloaded->getActiveSheet();
        self::assertSame(10, $rsheet->getCell('A1')->getValue());
        self::assertSame('Hello', $rsheet->getCell('B1')->getValue());
        self::assertTrue($rsheet->getCell('C1')->getValue());
        self::assertFalse($rsheet->getCell('D1')->getValue());
        $reloaded->disconnectWorksheets();
    }

    public function testNotBetterBoolean(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue(10);
        $sheet->getCell('B1')->setValue('Hello');
        $sheet->getCell('C1')->setValue(true);
        $sheet->getCell('D1')->setValue('=IF(1>2, TRUE, FALSE)');

        /** @var callable */
        $callableWriter = [$this, 'setNotBetter'];
        $reloaded = $this->writeAndReload($spreadsheet, 'Html', null, $callableWriter);
        $spreadsheet->disconnectWorksheets();

        $rsheet = $reloaded->getActiveSheet();
        self::assertSame(10, $rsheet->getCell('A1')->getValue());
        self::assertSame('Hello', $rsheet->getCell('B1')->getValue());
        self::assertSame(1, $rsheet->getCell('C1')->getValue());
        self::assertNull($rsheet->getCell('D1')->getValue());
        $reloaded->disconnectWorksheets();
    }

    public function testLocale(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue(10);
        $sheet->getCell('B1')->setValue('Hello');
        $sheet->getCell('C1')->setValue(true);
        $sheet->getCell('D1')->setValue('=IF(1>2, TRUE, FALSE)');
        $calc = Calculation::getInstance();
        $calc->setLocale('fr');
        $writer = new HtmlWriter($spreadsheet);
        $writer->setBetterBoolean(true);
        $html = $writer->generateHtmlAll();
        self::assertStringContainsString('VRAI', $html);
        self::assertStringNotContainsString('TRUE', $html);

        /** @var callable */
        $callableWriter = [$this, 'setBetter'];
        $reloaded = $this->writeAndReload($spreadsheet, 'Html', null, $callableWriter);
        $spreadsheet->disconnectWorksheets();

        $rsheet = $reloaded->getActiveSheet();
        self::assertSame(10, $rsheet->getCell('A1')->getValue());
        self::assertSame('Hello', $rsheet->getCell('B1')->getValue());
        self::assertTrue($rsheet->getCell('C1')->getValue());
        self::assertFalse($rsheet->getCell('D1')->getValue());
        $reloaded->disconnectWorksheets();
    }

    public function testForeignNoLocale(): void
    {
        $fragment = '<table><tbody><tr>'
            . '<td>10</td>'
            . '<td>Hello</td>'
            . '<td data-type="b">ИСТИНА</td>' // Bulgarian TRUE
            . '<td data-type="b">EPÄTOSI</td>' // Finnish FALSE
            . '<td data-type="b">whatever</td>'
            . '<td data-type="b">tRuE</td>'
            . '</tr></tbody></table>';
        $reader = new HtmlReader();
        $spreadsheet = $reader->loadFromString($fragment);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertTrue($sheet->getCell('C1')->getValue());
        self::assertFalse($sheet->getCell('D1')->getValue());
        self::assertSame('whatever', $sheet->getCell('E1')->getValue());
        self::assertTrue($sheet->getCell('F1')->getValue());
        $spreadsheet->disconnectWorksheets();
    }
}
