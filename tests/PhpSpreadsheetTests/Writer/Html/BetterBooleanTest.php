<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
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
        // Default change with release 4.0.0
        self::assertTrue($writer->getBetterBoolean());
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
        $sheet->getCell('A1')->setValue(1);
        $sheet->getCell('B1')->setValue('Hello');
        $sheet->getCell('C1')->setValue(true);
        $sheet->getCell('D1')->setValue('=IF(1>2, TRUE, FALSE)');
        $sheet->getCell('E1')->setValueExplicit(1, DataType::TYPE_STRING);
        $sheet->getCell('F1')->setValue('="A"&"B"');
        $sheet->getCell('G1')->setValue('=1+2');

        $reloaded = $this->writeAndReload($spreadsheet, 'Html');
        $falseTrue = Calculation::getInstance($spreadsheet)->getFalseTrueArray();
        $countPortTrue = 0;
        $portTrue = 'VERDADEIRO';
        $portFalse = 'FALSO';
        $countArray = count($falseTrue[1]);
        for ($i = 0; $i < $countArray; ++$i) {
            if ($falseTrue[1][$i] === $portTrue) {
                ++$countPortTrue;
                self::assertSame($portFalse, $falseTrue[0][$i]);
            }
        }
        self::assertSame(2, $countPortTrue, '1 for pt, 1 for pt-br');
        $spreadsheet->disconnectWorksheets();

        $rsheet = $reloaded->getActiveSheet();
        self::assertSame(1, $rsheet->getCell('A1')->getValue());
        self::assertSame('Hello', $rsheet->getCell('B1')->getValue());
        self::assertTrue($rsheet->getCell('C1')->getValue());
        self::assertFalse($rsheet->getCell('D1')->getValue());
        self::assertSame('1', $rsheet->getCell('E1')->getValue());
        self::assertSame('AB', $rsheet->getCell('F1')->getValue());
        self::assertSame(3, $rsheet->getCell('G1')->getValue());
        $reloaded->disconnectWorksheets();
    }

    public function testNotBetterBoolean(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue(1);
        $sheet->getCell('B1')->setValue('Hello');
        $sheet->getCell('C1')->setValue(true);
        $sheet->getCell('D1')->setValue('=IF(1>2, TRUE, FALSE)');
        $sheet->getCell('E1')->setValueExplicit(1, DataType::TYPE_STRING);
        $sheet->getCell('F1')->setValue('="A"&"B"');
        $sheet->getCell('G1')->setValue('=1+2');

        $reloaded = $this->writeAndReload($spreadsheet, 'Html', null, $this->setNotBetter(...));
        $spreadsheet->disconnectWorksheets();

        $rsheet = $reloaded->getActiveSheet();
        self::assertSame(1, $rsheet->getCell('A1')->getValue());
        self::assertSame('Hello', $rsheet->getCell('B1')->getValue());
        self::assertSame(1, $rsheet->getCell('C1')->getValue());
        self::assertNull($rsheet->getCell('D1')->getValue());
        self::assertSame(1, $rsheet->getCell('E1')->getValue());
        self::assertSame('AB', $rsheet->getCell('F1')->getValue());
        self::assertSame(3, $rsheet->getCell('G1')->getValue());
        $reloaded->disconnectWorksheets();
    }

    public function testLocale(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue(1);
        $sheet->getCell('B1')->setValue('Hello');
        $sheet->getCell('C1')->setValue(true);
        $sheet->getCell('D1')->setValue('=IF(1>2, TRUE, FALSE)');
        $sheet->getCell('E1')->setValueExplicit(1, DataType::TYPE_STRING);
        $sheet->getCell('F1')->setValue('="A"&"B"');
        $sheet->getCell('G1')->setValue('=1+2');
        $calc = Calculation::getInstance();
        $calc->setLocale('fr');
        $writer = new HtmlWriter($spreadsheet);
        $writer->setBetterBoolean(true);
        $html = $writer->generateHtmlAll();
        self::assertStringNotContainsString('TRUE', $html);
        self::assertStringContainsString('<td data-type="b" class="column2 style0 b">VRAI</td>', $html);
        self::assertStringContainsString('<td data-type="b" class="column3 style0 b">FAUX</td>', $html);
        self::assertStringContainsString('<td data-type="s" class="column4 style0 s">1</td>', $html);
        self::assertStringContainsString('<td class="column5 style0 s">AB</td>', $html);
        self::assertStringContainsString('<td class="column6 style0 n">3</td>', $html);

        $reloaded = $this->writeAndReload($spreadsheet, 'Html', null, $this->setBetter(...));
        $spreadsheet->disconnectWorksheets();

        $rsheet = $reloaded->getActiveSheet();
        self::assertSame(1, $rsheet->getCell('A1')->getValue());
        self::assertSame('Hello', $rsheet->getCell('B1')->getValue());
        self::assertTrue($rsheet->getCell('C1')->getValue());
        self::assertFalse($rsheet->getCell('D1')->getValue());
        self::assertSame('1', $rsheet->getCell('E1')->getValue());
        self::assertSame('AB', $rsheet->getCell('F1')->getValue());
        self::assertSame(3, $rsheet->getCell('G1')->getValue());
        $reloaded->disconnectWorksheets();
    }

    public function testInline(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setPrintGridlines(true);
        $sheet->getCell('A1')->setValue(1);
        $sheet->getCell('B1')->setValue('Hello');
        $sheet->getCell('C1')->setValue(true);
        $sheet->getCell('D1')->setValue('=IF(1>2, TRUE, FALSE)');
        $sheet->getCell('E1')->setValueExplicit(1, DataType::TYPE_STRING);
        $sheet->getCell('F1')->setValue('="A"&"B"');
        $sheet->getCell('G1')->setValue('=1+2');
        $calc = Calculation::getInstance();
        $calc->setLocale('fr');
        $writer = new HtmlWriter($spreadsheet);
        $writer->setBetterBoolean(true);
        $writer->setUseInlineCss(true);
        $html = $writer->generateHtmlAll();
        $html = str_replace('vertical-align:bottom; color:#000000; font-family:\'Calibri\'; font-size:11pt; ', '', $html);
        $html = str_replace(' width:42pt"', '"', $html);
        self::assertStringNotContainsString('TRUE', $html);
        self::assertStringContainsString('<td class="gridlines gridlinesp" style="text-align:right;">1</td>', $html);
        self::assertStringContainsString('<td class="gridlines gridlinesp" style="text-align:left;">Hello</td>', $html);
        self::assertStringContainsString('<td data-type="b" class="gridlines gridlinesp" style="text-align:center;">VRAI</td>', $html);
        self::assertStringContainsString('<td data-type="b" class="gridlines gridlinesp" style="text-align:center;">FAUX</td>', $html);
        self::assertStringContainsString('<td data-type="s" class="gridlines gridlinesp" style="text-align:left;">1</td>', $html);
        self::assertStringContainsString('<td class="gridlines gridlinesp" style="text-align:left;">AB</td>', $html);
        self::assertStringContainsString('<td class="gridlines gridlinesp" style="text-align:right;">3</td>', $html);

        $reloaded = $this->writeAndReload($spreadsheet, 'Html', null, $this->setBetter(...));
        $spreadsheet->disconnectWorksheets();

        $rsheet = $reloaded->getActiveSheet();
        self::assertSame(1, $rsheet->getCell('A1')->getValue());
        self::assertSame('Hello', $rsheet->getCell('B1')->getValue());
        self::assertTrue($rsheet->getCell('C1')->getValue());
        self::assertFalse($rsheet->getCell('D1')->getValue());
        self::assertSame('1', $rsheet->getCell('E1')->getValue());
        self::assertSame('AB', $rsheet->getCell('F1')->getValue());
        self::assertSame(3, $rsheet->getCell('G1')->getValue());
        $reloaded->disconnectWorksheets();
    }

    public function testForeignNoLocale(): void
    {
        $fragment = '<table><tbody><tr>'
            . '<td>1</td>'
            . '<td>Hello</td>'
            . '<td data-type="b">ИСТИНА</td>' // Bulgarian TRUE
            . '<td data-type="b">EPÄTOSI</td>' // Finnish FALSE
            . '<td data-type="b">whatever</td>'
            . '<td data-type="b">tRuE</td>'
            . '<td data-type="s">1</td>'
            . '</tr></tbody></table>';
        $reader = new HtmlReader();
        $spreadsheet = $reader->loadFromString($fragment);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame(1, $sheet->getCell('A1')->getValue());
        self::assertSame('Hello', $sheet->getCell('B1')->getValue());
        self::assertTrue($sheet->getCell('C1')->getValue());
        self::assertFalse($sheet->getCell('D1')->getValue());
        self::assertSame('whatever', $sheet->getCell('E1')->getValue());
        self::assertTrue($sheet->getCell('F1')->getValue());
        self::assertSame('1', $sheet->getCell('G1')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testNoPreCalc(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', true);
        $writer = new HtmlWriter($spreadsheet);
        $writer->setPreCalculateFormulas(false);
        $writer->setBetterBoolean(true);
        $html = $writer->generateHtmlAll();
        self::assertStringContainsString('<td data-type="b" class="column0 style0 b">TRUE</td>', $html);
        $spreadsheet->disconnectWorksheets();
    }
}
