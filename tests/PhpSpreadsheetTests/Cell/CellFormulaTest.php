<?php

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class CellFormulaTest extends TestCase
{
    public function testSetFormulaExplicit(): void
    {
        $formula = '=A2+B2';

        $spreadsheet = new Spreadsheet();
        $cell = $spreadsheet->getActiveSheet()->getCell('A1');
        $cell->setValueExplicit($formula, DataType::TYPE_FORMULA);

        self::assertSame($formula, $cell->getValue());
        self::assertTrue($cell->isFormula());

        $spreadsheet->disconnectWorksheets();
    }

    public function testSetFormulaDeterminedByBinder(): void
    {
        $formula = '=A2+B2';

        $spreadsheet = new Spreadsheet();
        $cell = $spreadsheet->getActiveSheet()->getCell('A1');
        $cell->setValue($formula);

        self::assertSame($formula, $cell->getValue());
        self::assertTrue($cell->isFormula());

        $spreadsheet->disconnectWorksheets();
    }

    public function testSetFormulaInvalidValue(): void
    {
        $formula = (object) true;

        $spreadsheet = new Spreadsheet();
        $cell = $spreadsheet->getActiveSheet()->getCell('A1');

        try {
            $cell->setValueExplicit($formula, DataType::TYPE_FORMULA);
            self::fail('setValueExplicit should have thrown exception');
        } catch (SpreadsheetException $e) {
            self::assertStringContainsString('Invalid unstringable value for datatype Formula', $e->getMessage());
        }

        $spreadsheet->disconnectWorksheets();
    }

    public function testSetArrayFormulaExplicitNoArray(): void
    {
        $formula = '=SUM(B2:B6*C2:C6)';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(
            [
                [1, 6],
                [2, 7],
                [3, 8],
                [4, 9],
                [5, 10],
            ],
            null,
            'B2'
        );
        $sheet->getCell('A1')->setValueExplicit($formula, DataType::TYPE_FORMULA);

        self::assertSame($formula, $sheet->getCell('A1')->getValue());
        self::assertTrue($sheet->getCell('A1')->isFormula());
        self::assertSame(130, $sheet->getCell('A1')->getCalculatedValue());
        self::assertEmpty($sheet->getCell('A1')->getFormulaAttributes());

        $spreadsheet->disconnectWorksheets();
    }

    public function testSetArrayFormulaExplicitWithRange(): void
    {
        $formula = '=SEQUENCE(3,3,-10,2.5)';

        $spreadsheet = new Spreadsheet();
        Calculation::getInstance($spreadsheet)->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        $cell = $spreadsheet->getActiveSheet()->getCell('A1');
        $cell->setValueExplicit($formula, DataType::TYPE_FORMULA);

        self::assertSame($formula, $cell->getValue());
        self::assertTrue($cell->isFormula());
        $expected = [
            [-10.0, -7.5, -5.0],
            [-2.5, 0.0, 2.5],
            [5.0, 7.5, 10.0],
        ];
        self::assertSame($expected, $cell->getCalculatedValue());
        self::assertSame(['t' => 'array', 'ref' => 'A1:C3'], $cell->getFormulaAttributes());

        $spreadsheet->disconnectWorksheets();
    }
}
