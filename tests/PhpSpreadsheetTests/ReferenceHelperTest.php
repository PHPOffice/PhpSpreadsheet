<?php

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;
use PhpOffice\PhpSpreadsheet\Comment;
use PhpOffice\PhpSpreadsheet\ReferenceHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class ReferenceHelperTest extends TestCase
{
    protected function setUp(): void
    {
    }

    public function testColumnSort(): void
    {
        $columnBase = $columnExpectedResult = [
            'A', 'B', 'Z',
            'AA', 'AB', 'AZ',
            'BA', 'BB', 'BZ',
            'ZA', 'ZB', 'ZZ',
            'AAA', 'AAB', 'AAZ',
            'ABA', 'ABB', 'ABZ',
            'AZA', 'AZB', 'AZZ',
            'BAA', 'BAB', 'BAZ',
            'BBA', 'BBB', 'BBZ',
            'BZA', 'BZB', 'BZZ',
        ];
        shuffle($columnBase);
        usort($columnBase, [ReferenceHelper::class, 'columnSort']);
        foreach ($columnBase as $key => $value) {
            self::assertEquals($columnExpectedResult[$key], $value);
        }
    }

    public function testColumnReverseSort(): void
    {
        $columnBase = $columnExpectedResult = [
            'A', 'B', 'Z',
            'AA', 'AB', 'AZ',
            'BA', 'BB', 'BZ',
            'ZA', 'ZB', 'ZZ',
            'AAA', 'AAB', 'AAZ',
            'ABA', 'ABB', 'ABZ',
            'AZA', 'AZB', 'AZZ',
            'BAA', 'BAB', 'BAZ',
            'BBA', 'BBB', 'BBZ',
            'BZA', 'BZB', 'BZZ',
        ];
        shuffle($columnBase);
        $columnExpectedResult = array_reverse($columnExpectedResult);
        usort($columnBase, [ReferenceHelper::class, 'columnReverseSort']);
        foreach ($columnBase as $key => $value) {
            self::assertEquals($columnExpectedResult[$key], $value);
        }
    }

    public function testCellSort(): void
    {
        $cellBase = $columnExpectedResult = [
            'A1', 'B1', 'AZB1',
            'BBB1', 'BB2', 'BAB2',
            'BZA2', 'Z3', 'AZA3',
            'BZB3', 'AB5', 'AZ6',
            'ABZ7', 'BA9', 'BZ9',
            'AAA9', 'AAZ9', 'BA10',
            'BZZ10', 'ZA11', 'AAB11',
            'BBZ29', 'BAA32', 'ZZ43',
            'AZZ43', 'BAZ67', 'ZB78',
            'ABA121', 'ABB289', 'BBA544',
        ];
        shuffle($cellBase);
        usort($cellBase, [ReferenceHelper::class, 'cellSort']);
        foreach ($cellBase as $key => $value) {
            self::assertEquals($columnExpectedResult[$key], $value);
        }
    }

    public function testCellReverseSort(): void
    {
        $cellBase = $columnExpectedResult = [
            'BBA544', 'ABB289', 'ABA121',
            'ZB78', 'BAZ67', 'AZZ43',
            'ZZ43', 'BAA32', 'BBZ29',
            'AAB11', 'ZA11', 'BZZ10',
            'BA10', 'AAZ9', 'AAA9',
            'BZ9', 'BA9', 'ABZ7',
            'AZ6', 'AB5', 'BZB3',
            'AZA3', 'Z3', 'BZA2',
            'BAB2', 'BB2', 'BBB1',
            'AZB1', 'B1', 'A1',
        ];
        shuffle($cellBase);
        usort($cellBase, [ReferenceHelper::class, 'cellReverseSort']);
        foreach ($cellBase as $key => $value) {
            self::assertEquals($columnExpectedResult[$key], $value);
        }
    }

    /**
     * @dataProvider providerFormulaUpdates
     */
    public function testUpdateFormula(string $formula, int $insertRows, int $insertColumns, string $worksheet, string $expectedResult): void
    {
        $referenceHelper = ReferenceHelper::getInstance();

        $result = $referenceHelper->updateFormulaReferences($formula, 'A1', $insertRows, $insertColumns, $worksheet);

        self::assertSame($expectedResult, $result);
    }

    public function providerFormulaUpdates(): array
    {
        return require 'tests/data/ReferenceHelperFormulaUpdates.php';
    }

    /**
     * @dataProvider providerMultipleWorksheetFormulaUpdates
     */
    public function testUpdateFormulaForMultipleWorksheets(string $formula, int $insertRows, int $insertColumns, string $expectedResult): void
    {
        $referenceHelper = ReferenceHelper::getInstance();

        $result = $referenceHelper->updateFormulaReferencesAnyWorksheet($formula, $insertRows, $insertColumns);

        self::assertSame($expectedResult, $result);
    }

    public function providerMultipleWorksheetFormulaUpdates(): array
    {
        return require 'tests/data/ReferenceHelperFormulaUpdatesMultipleSheet.php';
    }

    public function testInsertNewBeforeRetainDataType(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cell = $sheet->getCell('A1');
        $cell->setValueExplicit('+1', DataType::TYPE_STRING);
        $oldDataType = $cell->getDataType();
        $oldValue = $cell->getValue();

        $sheet->insertNewRowBefore(1);
        $newCell = $sheet->getCell('A2');
        $newDataType = $newCell->getDataType();
        $newValue = $newCell->getValue();

        self::assertSame($oldValue, $newValue);
        self::assertSame($oldDataType, $newDataType);
    }

    public function testRemoveColumnShiftsCorrectColumnValueIntoRemovedColumnCoordinates(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            ['a1', 'b1', 'c1'],
            ['a2', 'b2', null],
        ]);

        $cells = $sheet->toArray();
        self::assertSame('a1', $cells[0][0]);
        self::assertSame('b1', $cells[0][1]);
        self::assertSame('c1', $cells[0][2]);
        self::assertSame('a2', $cells[1][0]);
        self::assertSame('b2', $cells[1][1]);
        self::assertNull($cells[1][2]);

        $sheet->removeColumn('B');

        $cells = $sheet->toArray();
        self::assertSame('a1', $cells[0][0]);
        self::assertSame('c1', $cells[0][1]);
        self::assertArrayNotHasKey(2, $cells[0]);
        self::assertSame('a2', $cells[1][0]);
        self::assertNull($cells[1][1]);
        self::assertArrayNotHasKey(2, $cells[1]);
    }

    public function testInsertRowsWithPageBreaks(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([[1, 2], [3, 4], [5, 6], [7, 8], [9, 10]], null, 'A1', true);
        $sheet->setBreak('A2', Worksheet::BREAK_ROW);
        $sheet->setBreak('A5', Worksheet::BREAK_ROW);

        $sheet->insertNewRowBefore(2, 2);

        $breaks = $sheet->getBreaks();
        ksort($breaks);
        self::assertSame(['A4' => Worksheet::BREAK_ROW, 'A7' => Worksheet::BREAK_ROW], $breaks);
    }

    public function testDeleteRowsWithPageBreaks(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([[1, 2], [3, 4], [5, 6], [7, 8], [9, 10]], null, 'A1', true);
        $sheet->setBreak('A2', Worksheet::BREAK_ROW);
        $sheet->setBreak('A5', Worksheet::BREAK_ROW);

        $sheet->removeRow(2, 2);

        $breaks = $sheet->getBreaks();
        self::assertSame(['A3' => Worksheet::BREAK_ROW], $breaks);
    }

    public function testInsertRowsWithComments(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([[1, 2], [3, 4], [5, 6], [7, 8], [9, 10]], null, 'A1', true);
        $sheet->getComment('A2')->getText()->createText('First Comment');
        $sheet->getComment('A5')->getText()->createText('Second Comment');

        $sheet->insertNewRowBefore(2, 2);

        $comments = array_map(
            function (Comment $value) {
                return $value->getText()->getPlainText();
            },
            $sheet->getComments()
        );

        self::assertSame(['A4' => 'First Comment', 'A7' => 'Second Comment'], $comments);
    }

    public function testDeleteRowsWithComments(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([[1, 2], [3, 4], [5, 6], [7, 8], [9, 10]], null, 'A1', true);
        $sheet->getComment('A2')->getText()->createText('First Comment');
        $sheet->getComment('A5')->getText()->createText('Second Comment');

        $sheet->removeRow(2, 2);

        $comments = array_map(
            function (Comment $value) {
                return $value->getText()->getPlainText();
            },
            $sheet->getComments()
        );

        self::assertSame(['A3' => 'Second Comment'], $comments);
    }

    public function testInsertRowsWithHyperlinks(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([[1, 2], [3, 4], [5, 6], [7, 8], [9, 10]], null, 'A1', true);
        $sheet->getCell('A2')->getHyperlink()->setUrl('https://github.com/PHPOffice/PhpSpreadsheet');
        $sheet->getCell('A5')->getHyperlink()->setUrl('https://phpspreadsheet.readthedocs.io/en/latest/');

        $sheet->insertNewRowBefore(2, 2);

        $hyperlinks = array_map(
            function (Hyperlink $value) {
                return $value->getUrl();
            },
            $sheet->getHyperlinkCollection()
        );
        ksort($hyperlinks);

        self::assertSame(
            [
                'A4' => 'https://github.com/PHPOffice/PhpSpreadsheet',
                'A7' => 'https://phpspreadsheet.readthedocs.io/en/latest/',
            ],
            $hyperlinks
        );
    }

    public function testDeleteRowsWithHyperlinks(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([[1, 2], [3, 4], [5, 6], [7, 8], [9, 10]], null, 'A1', true);
        $sheet->getCell('A2')->getHyperlink()->setUrl('https://github.com/PHPOffice/PhpSpreadsheet');
        $sheet->getCell('A5')->getHyperlink()->setUrl('https://phpspreadsheet.readthedocs.io/en/latest/');

        $sheet->removeRow(2, 2);

        $hyperlinks = array_map(
            function (Hyperlink $value) {
                return $value->getUrl();
            },
            $sheet->getHyperlinkCollection()
        );

        self::assertSame(['A3' => 'https://phpspreadsheet.readthedocs.io/en/latest/'], $hyperlinks);
    }

    public function testInsertRowsWithDataValidation(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([['First'], ['Second'], ['Third'], ['Fourth']], null, 'A5', true);
        $cellAddress = 'E5';
        $this->setDataValidation($sheet, $cellAddress);

        $sheet->insertNewRowBefore(2, 2);

        self::assertFalse($sheet->getCell($cellAddress)->hasDataValidation());
        self::assertTrue($sheet->getCell('E7')->hasDataValidation());
    }

    public function testDeleteRowsWithDataValidation(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([['First'], ['Second'], ['Third'], ['Fourth']], null, 'A5', true);
        $cellAddress = 'E5';
        $this->setDataValidation($sheet, $cellAddress);

        $sheet->removeRow(2, 2);

        self::assertFalse($sheet->getCell($cellAddress)->hasDataValidation());
        self::assertTrue($sheet->getCell('E3')->hasDataValidation());
    }

    public function testDeleteColumnsWithDataValidation(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([['First'], ['Second'], ['Third'], ['Fourth']], null, 'A5', true);
        $cellAddress = 'E5';
        $this->setDataValidation($sheet, $cellAddress);

        $sheet->removeColumn('B', 2);

        self::assertFalse($sheet->getCell($cellAddress)->hasDataValidation());
        self::assertTrue($sheet->getCell('C5')->hasDataValidation());
    }

    public function testInsertColumnsWithDataValidation(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([['First'], ['Second'], ['Third'], ['Fourth']], null, 'A5', true);
        $cellAddress = 'E5';
        $this->setDataValidation($sheet, $cellAddress);

        $sheet->insertNewColumnBefore('C', 2);

        self::assertFalse($sheet->getCell($cellAddress)->hasDataValidation());
        self::assertTrue($sheet->getCell('G5')->hasDataValidation());
    }

    private function setDataValidation(Worksheet $sheet, string $cellAddress): void
    {
        $validation = $sheet->getCell($cellAddress)
            ->getDataValidation();
        $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Input error');
        $validation->setError('Value is not in list.');
        $validation->setPromptTitle('Pick from list');
        $validation->setPrompt('Please pick a value from the drop-down list.');
        $validation->setFormula1('$A5:$A8');
    }

    public function testInsertRowsWithConditionalFormatting(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([[1, 2, 3, 4], [3, 4, 5, 6], [5, 6, 7, 8], [7, 8, 9, 10], [9, 10, 11, 12]], null, 'C3', true);
        $sheet->getCell('H5')->setValue(5);

        $cellRange = 'C3:F7';
        $this->setConditionalFormatting($sheet, $cellRange);

        $sheet->insertNewRowBefore(4, 2);

        $styles = $sheet->getConditionalStylesCollection();
        // verify that the conditional range has been updated
        self::assertSame('C3:F9', array_keys($styles)[0]);
        // verify that the conditions have been updated
        foreach ($styles as $style) {
            foreach ($style as $conditions) {
                self::assertSame('$H$7', $conditions->getConditions()[0]);
            }
        }
    }

    public function testInsertColumnssWithConditionalFormatting(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([[1, 2, 3, 4], [3, 4, 5, 6], [5, 6, 7, 8], [7, 8, 9, 10], [9, 10, 11, 12]], null, 'C3', true);
        $sheet->getCell('H5')->setValue(5);

        $cellRange = 'C3:F7';
        $this->setConditionalFormatting($sheet, $cellRange);

        $sheet->insertNewColumnBefore('C', 2);

        $styles = $sheet->getConditionalStylesCollection();
        // verify that the conditional range has been updated
        self::assertSame('E3:H7', array_keys($styles)[0]);
        // verify that the conditions have been updated
        foreach ($styles as $style) {
            foreach ($style as $conditions) {
                self::assertSame('$J$5', $conditions->getConditions()[0]);
            }
        }
    }

    public function testDeleteRowsWithConditionalFormatting(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([[1, 2, 3, 4], [3, 4, 5, 6], [5, 6, 7, 8], [7, 8, 9, 10], [9, 10, 11, 12]], null, 'C3', true);
        $sheet->getCell('H5')->setValue(5);

        $cellRange = 'C3:F7';
        $this->setConditionalFormatting($sheet, $cellRange);

        $sheet->removeRow(4, 2);

        $styles = $sheet->getConditionalStylesCollection();
        // verify that the conditional range has been updated
        self::assertSame('C3:F5', array_keys($styles)[0]);
        // verify that the conditions have been updated
        foreach ($styles as $style) {
            foreach ($style as $conditions) {
                self::assertSame('$H$5', $conditions->getConditions()[0]);
            }
        }
    }

    public function testDeleteColumnsWithConditionalFormatting(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([[1, 2, 3, 4], [3, 4, 5, 6], [5, 6, 7, 8], [7, 8, 9, 10], [9, 10, 11, 12]], null, 'C3', true);
        $sheet->getCell('H5')->setValue(5);

        $cellRange = 'C3:F7';
        $this->setConditionalFormatting($sheet, $cellRange);

        $sheet->removeColumn('D', 2);

        $styles = $sheet->getConditionalStylesCollection();
        // verify that the conditional range has been updated
        self::assertSame('C3:D7', array_keys($styles)[0]);
        // verify that the conditions have been updated
        foreach ($styles as $style) {
            foreach ($style as $conditions) {
                self::assertSame('$F$5', $conditions->getConditions()[0]);
            }
        }
    }

    private function setConditionalFormatting(Worksheet $sheet, string $cellRange): void
    {
        $conditionalStyles = [];
        $wizardFactory = new Wizard($cellRange);
        /** @var Wizard\CellValue $cellWizard */
        $cellWizard = $wizardFactory->newRule(Wizard::CELL_VALUE);

        $cellWizard->equals('$H$5', Wizard::VALUE_TYPE_CELL);
        $conditionalStyles[] = $cellWizard->getConditional();

        $cellWizard->greaterThan('$H$5', Wizard::VALUE_TYPE_CELL);
        $conditionalStyles[] = $cellWizard->getConditional();

        $cellWizard->lessThan('$H$5', Wizard::VALUE_TYPE_CELL);
        $conditionalStyles[] = $cellWizard->getConditional();

        $sheet->getStyle($cellWizard->getCellRange())
            ->setConditionalStyles($conditionalStyles);
    }

    public function testInsertRowsWithPrintArea(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setPrintArea('A1:J10');

        $sheet->insertNewRowBefore(2, 2);

        $printArea = $sheet->getPageSetup()->getPrintArea();
        self::assertSame('A1:J12', $printArea);
    }

    public function testInsertColumnsWithPrintArea(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setPrintArea('A1:J10');

        $sheet->insertNewColumnBefore('B', 2);

        $printArea = $sheet->getPageSetup()->getPrintArea();
        self::assertSame('A1:L10', $printArea);
    }

    public function testDeleteRowsWithPrintArea(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setPrintArea('A1:J10');

        $sheet->removeRow(2, 2);

        $printArea = $sheet->getPageSetup()->getPrintArea();
        self::assertSame('A1:J8', $printArea);
    }

    public function testDeleteColumnsWithPrintArea(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setPrintArea('A1:J10');

        $sheet->removeColumn('B', 2);

        $printArea = $sheet->getPageSetup()->getPrintArea();
        self::assertSame('A1:H10', $printArea);
    }
}
