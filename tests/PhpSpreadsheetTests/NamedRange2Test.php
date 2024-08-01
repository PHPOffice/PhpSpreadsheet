<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\DefinedName;
use PhpOffice\PhpSpreadsheet\Exception as Except;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class NamedRange2Test extends TestCase
{
    private ?Spreadsheet $spreadsheet = null;

    protected function setUp(): void
    {
        $spreadsheet = $this->spreadsheet = new Spreadsheet();

        $worksheet1 = $spreadsheet->getActiveSheet();
        $worksheet1->setTitle('SheetOne');
        $spreadsheet->addNamedRange(
            new NamedRange('FirstRel', $worksheet1, '=A1')
        );
        $spreadsheet->addNamedRange(
            new NamedRange('FirstAbs', $worksheet1, '=$B$1')
        );
        $spreadsheet->addNamedRange(
            new NamedRange('FirstRelMult', $worksheet1, '=C1:D2')
        );
        $spreadsheet->addNamedRange(
            new NamedRange('FirstAbsMult', $worksheet1, '$E$3:$F$4')
        );

        $worksheet2 = $spreadsheet->createSheet();
        $worksheet2->setTitle('SheetTwo');
        $spreadsheet->addNamedRange(
            new NamedRange('SecondRel', $worksheet2, '=A1')
        );
        $spreadsheet->addNamedRange(
            new NamedRange('SecondAbs', $worksheet2, '=$B$1')
        );
        $spreadsheet->addNamedRange(
            new NamedRange('SecondRelMult', $worksheet2, '=C1:D2')
        );
        $spreadsheet->addNamedRange(
            new NamedRange('SecondAbsMult', $worksheet2, '$E$3:$F$4')
        );

        $spreadsheet->addDefinedName(DefinedName::createInstance('FirstFormula', $worksheet1, '=TODAY()-1'));
        $spreadsheet->addDefinedName(DefinedName::createInstance('SecondFormula', $worksheet2, '=TODAY()-2'));

        $this->spreadsheet->setActiveSheetIndex(0);
    }

    protected function tearDown(): void
    {
        if ($this->spreadsheet !== null) {
            $this->spreadsheet->disconnectWorksheets();
            $this->spreadsheet = null;
        }
    }

    private function getSpreadsheet(): Spreadsheet
    {
        if ($this->spreadsheet !== null) {
            return $this->spreadsheet;
        }
        $this->spreadsheet = new Spreadsheet();

        return $this->spreadsheet;
    }

    public function testNamedRangeSetStyle(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $sheet = $spreadsheet->getSheet(0);
        $sheet->getStyle('FirstRel')->getNumberFormat()->setFormatCode('yyyy-mm-dd');
        self::assertSame('yyyy-mm-dd', $sheet->getStyle('A1')->getNumberFormat()->getFormatCode());
        $sheet->getStyle('FirstAbs')->getFont()->setName('Georgia');
        self::assertSame('Georgia', $sheet->getStyle('B1')->getFont()->getName());
        $sheet->getStyle('FirstRelMult')->getFont()->setItalic(true);
        self::assertTrue($sheet->getStyle('D2')->getFont()->getItalic());
        $sheet->getStyle('FirstAbsMult')->getFill()->setFillType('gray125');
        self::assertSame('gray125', $sheet->getStyle('F4')->getFill()->getFillType());
        self::assertSame('none', $sheet->getStyle('A1')->getFill()->getFillType());
        $sheet = $spreadsheet->getSheet(1);
        $sheet->getStyle('SecondAbsMult')->getFill()->setFillType('lightDown');
        self::assertSame('lightDown', $sheet->getStyle('E3')->getFill()->getFillType());
    }

    /**
     * @dataProvider providerRangeOrFormula
     */
    public function testNamedRangeSetStyleBad(string $exceptionMessage, string $name): void
    {
        $this->expectException(Except::class);
        $this->expectExceptionMessage($exceptionMessage);
        $spreadsheet = $this->getSpreadsheet();
        $sheet = $spreadsheet->getSheet(0);
        $sheet->getStyle($name)->getNumberFormat()->setFormatCode('yyyy-mm-dd');
        self::assertSame('yyyy-mm-dd', $sheet->getStyle('A1')->getNumberFormat()->getFormatCode());
    }

    public static function providerRangeOrFormula(): array
    {
        return [
            'wrong sheet rel' => ['Invalid cell coordinate', 'SecondRel'],
            'wrong sheet abs' => ['Invalid cell coordinate', 'SecondAbs'],
            'wrong sheet relmult' => ['Invalid cell coordinate', 'SecondRelMult'],
            'wrong sheet absmult' => ['Invalid cell coordinate', 'SecondAbsMult'],
            'wrong sheet formula' => ['Invalid cell coordinate', 'SecondFormula'],
            'right sheet formula' => ['Invalid cell coordinate', 'FirstFormula'],
            'non-existent name' => ['Invalid cell coordinate', 'NonExistentName'],
            'other sheet name' => ['Invalid Worksheet', 'SheetTwo!G7'],
            'non-existent sheet name' => ['Invalid Worksheet', 'SheetAbc!G7'],
            'unnamed formula' => ['Invalid cell coordinate', '=2+3'],
        ];
    }
}
