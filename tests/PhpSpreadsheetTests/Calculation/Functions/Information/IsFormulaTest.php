<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Information;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\NamedRange as NamedRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class IsFormulaTest extends TestCase
{
    public function testIsFormula(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('SheetOne'); // no space in sheet title
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Sheet Two'); // space in sheet title
        $values = [
            [null, false],
            [-1, false],
            [0, false],
            [1, false],
            ['', false],
            [false, false],
            [true, false],
            ['=-1', true],
            ['="ABC"', true],
            ['=SUM(1,2,3)', true],
        ];
        $row = 0;
        foreach ($values as $valArray) {
            ++$row;
            if ($valArray[0] !== null) {
                $sheet1->getCell("A$row")->setValue($valArray[0]);
            }
            $sheet1->getCell("B$row")->setValue("=ISFORMULA(A$row)");
            self::assertSame($valArray[1], $sheet1->getCell("B$row")->getCalculatedValue(), "sheet1 error in B$row");
        }
        $row = 0;
        foreach ($values as $valArray) {
            ++$row;
            if ($valArray[0] !== null) {
                $sheet2->getCell("A$row")->setValue($valArray[0]);
            }
            $sheet2->getCell("B$row")->setValue("=ISFORMULA(A$row)");
            self::assertSame($valArray[1], $sheet2->getCell("B$row")->getCalculatedValue(), "sheet2 error in B$row");
        }
        $sheet1->getCell('C1')->setValue(0);
        $sheet1->getCell('C2')->setValue('=0');
        $sheet2->getCell('C3')->setValue(0);
        $sheet2->getCell('C4')->setValue('=0');
        $sheet1->getCell('D1')->setValue('=ISFORMULA(SheetOne!C1)');
        $sheet1->getCell('D2')->setValue('=ISFORMULA(SheetOne!C2)');
        $sheet1->getCell('E1')->setValue('=ISFORMULA(\'SheetOne\'!C1)');
        $sheet1->getCell('E2')->setValue('=ISFORMULA(\'SheetOne\'!C2)');
        $sheet1->getCell('F1')->setValue('=ISFORMULA(\'Sheet Two\'!C3)');
        $sheet1->getCell('F2')->setValue('=ISFORMULA(\'Sheet Two\'!C4)');
        self::assertFalse($sheet1->getCell('D1')->getCalculatedValue());
        self::assertTrue($sheet1->getCell('D2')->getCalculatedValue());
        self::assertFalse($sheet1->getCell('E1')->getCalculatedValue());
        self::assertTrue($sheet1->getCell('E2')->getCalculatedValue());
        self::assertFalse($sheet1->getCell('F1')->getCalculatedValue());
        self::assertTrue($sheet1->getCell('F2')->getCalculatedValue());

        $spreadsheet->addNamedRange(new NamedRange('range1f', $sheet1, '$C$1'));
        $spreadsheet->addNamedRange(new NamedRange('range1t', $sheet1, '$C$2'));
        $spreadsheet->addNamedRange(new NamedRange('range2f', $sheet2, '$C$3'));
        $spreadsheet->addNamedRange(new NamedRange('range2t', $sheet2, '$C$4'));
        $spreadsheet->addNamedRange(new NamedRange('range2ft', $sheet2, '$C$3:$C$4'));
        $sheet1->getCell('G1')->setValue('=ISFORMULA(ABCDEFG)');
        $sheet1->getCell('G3')->setValue('=ISFORMULA(range1f)');
        $sheet1->getCell('G4')->setValue('=ISFORMULA(range1t)');
        $sheet1->getCell('G5')->setValue('=ISFORMULA(range2f)');
        $sheet1->getCell('G6')->setValue('=ISFORMULA(range2t)');
        $sheet1->getCell('G7')->setValue('=ISFORMULA(range2ft)');
        self::assertSame('#NAME?', $sheet1->getCell('G1')->getCalculatedValue());
        self::assertFalse($sheet1->getCell('G3')->getCalculatedValue());
        self::assertTrue($sheet1->getCell('G4')->getCalculatedValue());
        self::assertFalse($sheet1->getCell('G5')->getCalculatedValue());
        self::assertTrue($sheet1->getCell('G6')->getCalculatedValue());
        self::assertFalse($sheet1->getCell('G7')->getCalculatedValue());

        $sheet1->getCell('H1')->setValue('=ISFORMULA(C1:C2)');
        $sheet1->getCell('H3')->setValue('=ISFORMULA(C2:C3)');
        self::assertFalse($sheet1->getCell('H1')->getCalculatedValue());
        self::assertTrue($sheet1->getCell('H3')->getCalculatedValue());

        $spreadsheet->disconnectWorksheets();
    }

    public function testIsFormulaArray(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue('=5/2');
        $sheet->getCell('A2')->setValueExplicit('=5/2', DataType::TYPE_STRING);
        $sheet->getCell('A3')->setValue('=5/0');
        $sheet->getCell('A4')->setValue(2.5);
        $sheet->getCell('A5')->setValue('=NA()');
        $sheet->getCell('A6')->setValue(true);
        $sheet->getCell('A7')->setValue('=5/0');
        $sheet->getCell('A7')->getStyle()->setQuotePrefix(true);

        $calculation = Calculation::getInstance($spreadsheet);

        $formula = '=ISFORMULA(A1:A7)';
        $result = $calculation->_calculateFormulaValue($formula, 'C1', $sheet->getCell('C1'));
        self::assertEquals([true, false, true, false, true, false, false], $result);

        $spreadsheet->disconnectWorksheets();
    }
}
