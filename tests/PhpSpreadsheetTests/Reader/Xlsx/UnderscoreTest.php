<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;
use PHPUnit\Framework\Attributes\DataProvider;

class UnderscoreTest extends AbstractFunctional
{
    private ?Spreadsheet $spreadsheet = null;

    private ?Spreadsheet $reloadedSpreadsheet = null;

    private const TEST_FILE = 'tests/data/Reader/XLSX/issue.4724.xlsx';

    protected function tearDown(): void
    {
        if ($this->spreadsheet !== null) {
            $this->spreadsheet->disconnectWorksheets();
            $this->spreadsheet = null;
        }
        if ($this->reloadedSpreadsheet !== null) {
            $this->reloadedSpreadsheet->disconnectWorksheets();
            $this->reloadedSpreadsheet = null;
        }
    }

    #[DataProvider('underscoreProvider')]
    public function testUnderscore(string $value): void
    {
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', $value);
        $this->reloadedSpreadsheet = $this->writeAndReload($this->spreadsheet, 'Xlsx');
        $rsheet = $this->reloadedSpreadsheet->getActiveSheet();
        self::assertSame($value, $rsheet->getCell('A1')->getValue());
    }

    #[DataProvider('underscoreProvider')]
    public function testUnderscoreInline(string $value): void
    {
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setCellValueExplicit('A1', $value, DataType::TYPE_INLINE);
        $this->reloadedSpreadsheet = $this->writeAndReload($this->spreadsheet, 'Xlsx');
        $rsheet = $this->reloadedSpreadsheet->getActiveSheet();
        self::assertSame($value, $rsheet->getCell('A1')->getValueString());
    }

    public static function underscoreProvider(): array
    {
        return [
            ['A_x0030_'],
            ['A_x0030_B'],
            ['A_B'],
        ];
    }

    public function testPreliminaries(): void
    {
        $file = 'zip://';
        $file .= self::TEST_FILE;
        $file .= '#xl/sharedStrings.xml';
        $data = file_get_contents($file);
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            self::assertStringContainsString('count="8"', $data);
            self::assertStringContainsString(
                "<t>line_x000D_\nwith_x000D_\nbreaks</t>",
                $data
            );
            self::assertStringContainsString('<t>A_x005F_x0030_B</t>', $data);
            self::assertStringContainsString(
                '<t>ABC_x0031__x0032__x0033_DEF</t>',
                $data
            );
            self::assertStringContainsString('<t>_xC1EF_</t>', $data);
            self::assertStringContainsString('<t>_xD801__xDC05_</t>', $data);
            self::assertStringContainsString('<t>_xD801__x0038_</t>', $data);
            self::assertStringContainsString('<t>_x0039__xDC05_</t>', $data);
            self::assertStringContainsString('<t>_xDF39__xDC05_</t>', $data);
        }
    }

    public function testX000dPreserved(): void
    {
        $reader = new XlsxReader();
        $binder = new DefaultValueBinder();
        $binder->setPreserveCr(true);
        $reader->setValueBinder($binder);
        $infile = self::TEST_FILE;
        $this->spreadsheet = $reader->load($infile);
        $sheet = $this->spreadsheet->getActiveSheet();
        $expected = "line\r\nwith\r\nbreaks";
        self::assertSame($expected, $sheet->getCell('A1')->getValue());
        $expected = 'A_x0030_B';
        self::assertSame($expected, $sheet->getCell('A2')->getValue());
        $expected = 'ABC123DEF';
        self::assertSame($expected, $sheet->getCell('A3')->getValue());
        $expected = '쇯';
        self::assertSame($expected, $sheet->getCell('A4')->getValue());
        $expected = '𐐅';
        self::assertSame($expected, $sheet->getCell('A5')->getValue(), 'outside BMP');
        $expected = '�8';
        self::assertSame($expected, $sheet->getCell('A6')->getValue(), 'high surrogate without low');
        $expected = '9�';
        self::assertSame($expected, $sheet->getCell('A7')->getValue(), 'low surrogate without high');
        $expected = '�';
        self::assertSame($expected, $sheet->getCell('A8')->getValue(), '2 low surrogates');
    }

    public function testX000dNotPreserved(): void
    {
        $reader = new XlsxReader();
        $infile = self::TEST_FILE;
        $this->spreadsheet = $reader->load($infile);
        $sheet = $this->spreadsheet->getActiveSheet();
        $expected = "line\nwith\nbreaks";
        self::assertSame($expected, $sheet->getCell('A1')->getValue());
        $expected = 'A_x0030_B';
        self::assertSame($expected, $sheet->getCell('A2')->getValue());
    }
}
