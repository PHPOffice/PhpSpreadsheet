<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Style\ConditionalFormatting\Wizard;

use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;
use PHPUnit\Framework\TestCase;

class WizardFactoryTest extends TestCase
{
    protected Wizard $wizardFactory;

    protected function setUp(): void
    {
        $range = '$C$3:$E$5';
        $this->wizardFactory = new Wizard($range);
    }

    /**
     * @dataProvider basicWizardFactoryProvider
     *
     * @psalm-param class-string<object> $expectedWizard
     */
    public function testBasicWizardFactory(string $ruleType, string $expectedWizard): void
    {
        $wizard = $this->wizardFactory->newRule($ruleType);
        self::assertInstanceOf($expectedWizard, $wizard);
    }

    public static function basicWizardFactoryProvider(): array
    {
        return [
            'CellValue Wizard' => [Wizard::CELL_VALUE, Wizard\CellValue::class],
            'TextValue Wizard' => [Wizard::TEXT_VALUE, Wizard\TextValue::class],
            'Blanks Wizard' => [Wizard::BLANKS, Wizard\Blanks::class],
            'Blanks Wizard (NOT)' => [Wizard::NOT_BLANKS, Wizard\Blanks::class],
            'Errors Wizard' => [Wizard::ERRORS, Wizard\Errors::class],
            'Errors Wizard (NOT)' => [Wizard::NOT_ERRORS, Wizard\Errors::class],
            'Expression Wizard' => [Wizard::EXPRESSION, Wizard\Expression::class],
            'DateValue Wizard' => [Wizard::DATES_OCCURRING, Wizard\DateValue::class],
        ];
    }

    /**
     * @dataProvider conditionalProvider
     */
    public function testWizardFromConditional(string $sheetName, string $cellAddress, array $expectedWizads): void
    {
        $filename = 'tests/data/Style/ConditionalFormatting/CellMatcher.xlsx';
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filename);
        $worksheet = $spreadsheet->getSheetByNameOrThrow($sheetName);
        $cell = $worksheet->getCell($cellAddress);

        $cfRange = $worksheet->getConditionalRange($cell->getCoordinate());
        if ($cfRange === null) {
            self::markTestSkipped("{$cellAddress} is not in a Conditional Format range");
        }
        $conditionals = $worksheet->getConditionalStyles($cfRange);

        foreach ($conditionals as $index => $conditional) {
            $wizard = Wizard::fromConditional($conditional);
            self::assertEquals($expectedWizads[$index], $wizard::class);
        }
    }

    public static function conditionalProvider(): array
    {
        return [
            'cellIs Comparison A2' => ['cellIs Comparison', 'A2', [Wizard\CellValue::class, Wizard\CellValue::class, Wizard\CellValue::class]],
            'cellIs Expression A2' => ['cellIs Expression', 'A2', [Wizard\Expression::class, Wizard\Expression::class]],
            'Text Expressions A2' => ['Text Expressions', 'A2', [Wizard\TextValue::class]],
            'Text Expressions A8' => ['Text Expressions', 'A8', [Wizard\TextValue::class]],
            'Text Expressions A14' => ['Text Expressions', 'A14', [Wizard\TextValue::class]],
            'Text Expressions A20' => ['Text Expressions', 'A20', [Wizard\TextValue::class]],
            'Blank Expressions A2' => ['Blank Expressions', 'A2', [Wizard\Blanks::class, Wizard\Blanks::class]],
            'Error Expressions C2' => ['Error Expressions', 'C2', [Wizard\Errors::class, Wizard\Errors::class]],
            'Date Expressions B10' => ['Date Expressions', 'B10', [Wizard\DateValue::class]],
            'Duplicates Expressions A2' => ['Duplicates Expressions', 'A2', [Wizard\Duplicates::class, Wizard\Duplicates::class]],
        ];
    }

    public function testWizardFactoryException(): void
    {
        $ruleType = 'Unknown';
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No wizard exists for this CF rule type');
        $this->wizardFactory->newRule($ruleType);

        $conditional = new Conditional();
        $conditional->setConditionType('UNKNOWN');
        Wizard::fromConditional($conditional);
    }

    public function testWizardFactoryFromConditionalException(): void
    {
        $ruleType = 'Unknown';
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No wizard exists for this CF rule type');

        $conditional = new Conditional();
        $conditional->setConditionType($ruleType);
        Wizard::fromConditional($conditional);
    }
}
