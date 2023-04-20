<?php

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PHPUnit\Framework\TestCase;

class CellTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cell::setValueBinder(new DefaultValueBinder());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Cell::setValueBinder(new DefaultValueBinder());
    }

    public function testSetValueBinderOverride(): void
    {
        $value = '12.5%';
        $spreadsheet = new Spreadsheet();

        $cell = $spreadsheet->getActiveSheet()->getCell('A1');
        $cell->setValue($value); // Using the Default Value Binder

        self::assertSame('12.5%', $cell->getValue());
        self::assertSame('General', $cell->getStyle()->getNumberFormat()->getFormatCode());

        $cell = $spreadsheet->getActiveSheet()->getCell('A2');
        $cell->setValue($value, new AdvancedValueBinder()); // Overriding the Default Value Binder

        self::assertSame(0.125, $cell->getValue());
        self::assertSame('0.00%', $cell->getStyle()->getNumberFormat()->getFormatCode());

        $spreadsheet->disconnectWorksheets();
    }

    public function testSetValueBinderOverride2(): void
    {
        $value = '12.5%';
        $spreadsheet = new Spreadsheet();
        Cell::setValueBinder(new AdvancedValueBinder());

        $cell = $spreadsheet->getActiveSheet()->getCell('A1');
        $cell->setValue($value); // Using the Advanced Value Binder

        self::assertSame(0.125, $cell->getValue());
        self::assertSame('0.00%', $cell->getStyle()->getNumberFormat()->getFormatCode());

        $cell = $spreadsheet->getActiveSheet()->getCell('A2');
        $cell->setValue($value, new StringValueBinder()); // Overriding the Advanced Value Binder

        self::assertSame('12.5%', $cell->getValue());
        self::assertSame('General', $cell->getStyle()->getNumberFormat()->getFormatCode());

        $spreadsheet->disconnectWorksheets();
    }

    /**
     * @dataProvider providerSetValueExplicit
     *
     * @param mixed $expected
     * @param mixed $value
     */
    public function testSetValueExplicit($expected, $value, string $dataType): void
    {
        $spreadsheet = new Spreadsheet();
        $cell = $spreadsheet->getActiveSheet()->getCell('A1');
        $cell->setValueExplicit($value, $dataType);

        self::assertSame($expected, $cell->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public static function providerSetValueExplicit(): array
    {
        return require 'tests/data/Cell/SetValueExplicit.php';
    }

    public function testInvalidIsoDateSetValueExplicit(): void
    {
        $spreadsheet = new Spreadsheet();
        $cell = $spreadsheet->getActiveSheet()->getCell('A1');

        $dateValue = '2022-02-29'; // Invalid leap year
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Invalid string {$dateValue} supplied for datatype Date");
        $cell->setValueExplicit($dateValue, DataType::TYPE_ISO_DATE);

        $spreadsheet->disconnectWorksheets();
    }

    /**
     * @dataProvider providerSetValueExplicitException
     *
     * @param mixed $value
     */
    public function testSetValueExplicitException($value, string $dataType): void
    {
        $this->expectException(Exception::class);

        $spreadsheet = new Spreadsheet();
        $cell = $spreadsheet->getActiveSheet()->getCell('A1');
        $cell->setValueExplicit($value, $dataType);
    }

    public static function providerSetValueExplicitException(): array
    {
        return require 'tests/data/Cell/SetValueExplicitException.php';
    }

    public function testNoChangeToActiveSheet(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Sheet 1');
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Sheet 3');
        $sheet1->setCellValue('C1', 123);
        $sheet1->setCellValue('D1', 124);
        $sheet3->setCellValue('A1', "='Sheet 1'!C1+'Sheet 1'!D1");
        $sheet1->setCellValue('A1', "='Sheet 3'!A1");
        $cell = 'A1';
        $spreadsheet->setActiveSheetIndex(0);
        self::assertEquals(0, $spreadsheet->getActiveSheetIndex());
        $value = $spreadsheet->getActiveSheet()->getCell($cell)->getCalculatedValue();
        self::assertEquals(0, $spreadsheet->getActiveSheetIndex());
        self::assertEquals(247, $value);
        $spreadsheet->disconnectWorksheets();
    }

    public function testDestroyWorksheet(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cell = $sheet->getCell('A1');
        self::assertSame($sheet, $cell->getWorksheet());
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Worksheet no longer exists');
        $spreadsheet->disconnectWorksheets();
        $cell->getWorksheet();
    }

    public function testDestroyCell1(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cell = $sheet->getCell('A1');
        self::assertSame('A1', $cell->getCoordinate());
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Coordinate no longer exists');
        $spreadsheet->disconnectWorksheets();
        $cell->getCoordinate();
    }

    public function testDestroyCell2(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cell = $sheet->getCell('A1');
        self::assertSame('A1', $cell->getCoordinate());
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Coordinate no longer exists');
        $parent = $cell->getParent();
        if ($parent === null) {
            self::fail('Unexpected null parent');
        } else {
            $parent->delete('A1');
            $cell->getCoordinate();
        }
    }

    public function testAppliedStyleWithRange(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', -1);
        $sheet->setCellValue('A2', 0);
        $sheet->setCellValue('A3', 1);

        $cellRange = 'A1:A3';
        $sheet->getStyle($cellRange)->getFont()->setBold(true);

        $yellowStyle = new Style(false, true);
        $yellowStyle->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getEndColor()->setARGB(Color::COLOR_YELLOW);
        $greenStyle = new Style(false, true);
        $greenStyle->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getEndColor()->setARGB(Color::COLOR_GREEN);
        $redStyle = new Style(false, true);
        $redStyle->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getEndColor()->setARGB(Color::COLOR_RED);

        $conditionalStyles = [];
        $wizardFactory = new Wizard($cellRange);
        /** @var Wizard\CellValue $cellWizard */
        $cellWizard = $wizardFactory->newRule(Wizard::CELL_VALUE);

        $cellWizard->equals(0)
            ->setStyle($yellowStyle);
        $conditionalStyles[] = $cellWizard->getConditional();

        $cellWizard->greaterThan(0)
            ->setStyle($greenStyle);
        $conditionalStyles[] = $cellWizard->getConditional();

        $cellWizard->lessThan(0)
            ->setStyle($redStyle);
        $conditionalStyles[] = $cellWizard->getConditional();

        $sheet->getStyle($cellWizard->getCellRange())
            ->setConditionalStyles($conditionalStyles);

        $style = $sheet->getCell('A1')->getAppliedStyle();
        self::assertTrue($style->getFont()->getBold());
        self::assertEquals($redStyle->getFill()->getFillType(), $style->getFill()->getFillType());
        self::assertEquals($redStyle->getFill()->getEndColor()->getARGB(), $style->getFill()->getEndColor()->getARGB());

        $style = $sheet->getCell('A2')->getAppliedStyle();
        self::assertTrue($style->getFont()->getBold());
        self::assertEquals($yellowStyle->getFill()->getFillType(), $style->getFill()->getFillType());
        self::assertEquals(
            $yellowStyle->getFill()->getEndColor()->getARGB(),
            $style->getFill()->getEndColor()->getARGB()
        );

        $style = $sheet->getCell('A3')->getAppliedStyle();
        self::assertTrue($style->getFont()->getBold());
        self::assertEquals($greenStyle->getFill()->getFillType(), $style->getFill()->getFillType());
        self::assertEquals(
            $greenStyle->getFill()->getEndColor()->getARGB(),
            $style->getFill()->getEndColor()->getARGB()
        );
    }

    /**
     * @dataProvider appliedStyling
     */
    public function testAppliedStyleSingleCell(string $cellAddress, string $fillStyle, ?string $fillColor): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', -1);
        $sheet->setCellValue('A2', 0);
        $sheet->setCellValue('B1', 0);
        $sheet->setCellValue('C1', 1);
        $sheet->setCellValue('C2', -1);

        $cellRange = 'A1:C2';
        $sheet->getStyle($cellRange)->getFont()->setBold(true);

        $yellowStyle = new Style(false, true);
        $yellowStyle->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getEndColor()->setARGB(Color::COLOR_YELLOW);
        $redStyle = new Style(false, true);
        $redStyle->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getEndColor()->setARGB(Color::COLOR_RED);

        $conditionalCellRange = 'A1:C1';
        $conditionalStyles = [];
        $wizardFactory = new Wizard($conditionalCellRange);
        /** @var Wizard\CellValue $cellWizard */
        $cellWizard = $wizardFactory->newRule(Wizard::CELL_VALUE);

        $cellWizard->equals(0)
            ->setStyle($yellowStyle);
        $conditionalStyles[] = $cellWizard->getConditional();

        $cellWizard->lessThan(0)
            ->setStyle($redStyle);
        $conditionalStyles[] = $cellWizard->getConditional();

        $sheet->getStyle($cellWizard->getCellRange())
            ->setConditionalStyles($conditionalStyles);

        $style = $sheet->getCell($cellAddress)->getAppliedStyle();

        self::assertTrue($style->getFont()->getBold());
        self::assertEquals($fillStyle, $style->getFill()->getFillType());
        if ($fillStyle === Fill::FILL_SOLID) {
            self::assertEquals($fillColor, $style->getFill()->getEndColor()->getARGB());
        }
    }

    public static function appliedStyling(): array
    {
        return [
            'A1 - Conditional with Match' => ['A1', Fill::FILL_SOLID, Color::COLOR_RED],
            'A2 - No Conditionals' => ['A2', Fill::FILL_NONE, null],
            'B1 - Conditional with Match' => ['B1', Fill::FILL_SOLID, Color::COLOR_YELLOW],
            'C1 - Conditionals, but No Match' => ['C1', Fill::FILL_NONE, null],
            'C2 - No Conditionals' => ['C2', Fill::FILL_NONE, null],
        ];
    }
}
