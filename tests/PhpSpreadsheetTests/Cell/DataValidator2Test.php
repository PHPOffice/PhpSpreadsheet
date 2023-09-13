<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class DataValidator2Test extends AbstractFunctional
{
    public function testList(): void
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load('tests/data/Reader/XLSX/issue.1432b.xlsx');
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('H1', $sheet->getTopLeftCell());
        self::assertSame('K3', $sheet->getSelectedCells());

        $testCell = $sheet->getCell('K3');
        $validation = $testCell->getDataValidation();
        self::assertSame(DataValidation::TYPE_LIST, $validation->getType());

        $testCell = $sheet->getCell('R2');
        $validation = $testCell->getDataValidation();
        self::assertSame(DataValidation::TYPE_LIST, $validation->getType());

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $sheet = $reloadedSpreadsheet->getActiveSheet();

        $cell = 'K3';
        $testCell = $sheet->getCell($cell);
        $validation = $testCell->getDataValidation();
        self::assertSame(DataValidation::TYPE_LIST, $validation->getType());
        $testCell->setValue('Y');
        self::assertTrue($testCell->hasValidValue(), 'K3 other sheet has valid value');
        $testCell = $sheet->getCell($cell);
        $testCell->setValue('X');
        self::assertFalse($testCell->hasValidValue(), 'K3 other sheet has invalid value');

        $cell = 'J2';
        $testCell = $sheet->getCell($cell);
        $validation = $testCell->getDataValidation();
        self::assertSame(DataValidation::TYPE_LIST, $validation->getType());
        $testCell = $sheet->getCell($cell);
        $testCell->setValue('GBP');
        self::assertTrue($testCell->hasValidValue(), 'J2 other sheet has valid value');
        $testCell = $sheet->getCell($cell);
        $testCell->setValue('XYZ');
        self::assertFalse($testCell->hasValidValue(), 'J2 other sheet has invalid value');

        $cell = 'R2';
        $testCell = $sheet->getCell($cell);
        $validation = $testCell->getDataValidation();
        self::assertSame(DataValidation::TYPE_LIST, $validation->getType());
        $testCell->setValue('ListItem2');
        self::assertTrue($testCell->hasValidValue(), 'R2 same sheet has valid value');
        $testCell = $sheet->getCell($cell);
        $testCell->setValue('ListItem99');
        self::assertFalse($testCell->hasValidValue(), 'R2 same sheet has invalid value');

        $styles = $sheet->getConditionalStyles('I1:I1048576');
        self::assertCount(1, $styles);
        $style = $styles[0];
        self::assertSame(Conditional::CONDITION_CELLIS, $style->getConditionType());
        self::assertSame(Conditional::OPERATOR_BETWEEN, $style->getOperatorType());
        $conditions = $style->getConditions();
        self::assertSame('10', $conditions[0]);
        self::assertSame('20', $conditions[1]);
        self::assertSame('FF70AD47', $style->getStyle()->getFill()->getEndColor()->getARGB());

        $spreadsheet->disconnectWorksheets();
        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
