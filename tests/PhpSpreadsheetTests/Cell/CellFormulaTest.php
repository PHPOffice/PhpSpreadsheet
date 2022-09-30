<?php

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
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
        self::assertFalse($cell->isArrayFormula());

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
        self::assertFalse($cell->isArrayFormula());

        $spreadsheet->disconnectWorksheets();
    }

    public function testSetFormulaInvalidValue(): void
    {
        $formula = true;

        $spreadsheet = new Spreadsheet();
        $cell = $spreadsheet->getActiveSheet()->getCell('A1');

        $cell->setValueExplicit($formula, DataType::TYPE_FORMULA);

        self::assertSame('TRUE', $cell->getValue());
        self::assertFalse($cell->isFormula());

        $spreadsheet->disconnectWorksheets();
    }

    public function testSetFormulaInvalidFormulaValue(): void
    {
        $formula = 'invalid formula';

        $spreadsheet = new Spreadsheet();
        $cell = $spreadsheet->getActiveSheet()->getCell('A1');

        $cell->setValueExplicit($formula, DataType::TYPE_FORMULA);

        self::assertSame($formula, $cell->getValue());
        self::assertFalse($cell->isFormula());

        $spreadsheet->disconnectWorksheets();
    }

    public function testSetArrayFormulaExplicitNoRange(): void
    {
        $formula = '=SUM(B2:B6*C2:C6)';

        $spreadsheet = new Spreadsheet();
        $cell = $spreadsheet->getActiveSheet()->getCell('A1');
        $cell->setValueExplicit($formula, DataType::TYPE_FORMULA, true);

        self::assertSame($formula, $cell->getValue());
        self::assertTrue($cell->isFormula());
        self::assertTrue($cell->isArrayFormula());
        self::assertArrayHasKey('ref', $cell->getFormulaAttributes());
        self::assertSame('A1', $cell->getFormulaAttributes()['ref']);

        $spreadsheet->disconnectWorksheets();
    }

    public function testSetArrayFormulaExplicitWithRange(): void
    {
        $formula = '=SEQUENCE(3,3,-10,2.5)';

        $spreadsheet = new Spreadsheet();
        $cell = $spreadsheet->getActiveSheet()->getCell('A1');
        $cell->setValueExplicit($formula, DataType::TYPE_FORMULA, true, 'A1:C3');

        self::assertSame($formula, $cell->getValue());
        self::assertTrue($cell->isFormula());
        self::assertTrue($cell->isArrayFormula());
        self::assertArrayHasKey('ref', $cell->getFormulaAttributes());
        self::assertSame('A1:C3', $cell->getFormulaAttributes()['ref']);

        $spreadsheet->disconnectWorksheets();
    }
}
