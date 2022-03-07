<?php

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\DefinedName;
use PhpOffice\PhpSpreadsheet\NamedFormula;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class DefinedNameFormulaTest extends TestCase
{
    /**
     * @dataProvider providerRangeOrFormula
     */
    public function testRangeOrFormula(string $value, bool $expectedResult): void
    {
        $actualResult = DefinedName::testIfFormula($value);
        self::assertSame($expectedResult, $actualResult);
    }

    public function testAddDefinedNames(): void
    {
        $spreadSheet = new Spreadsheet();
        $workSheet = $spreadSheet->getActiveSheet();

        $definedNamesForTest = $this->providerRangeOrFormula();
        foreach ($definedNamesForTest as $key => $definedNameData) {
            [$value] = $definedNameData;
            $name = str_replace([' ', '-'], '_', $key);
            $spreadSheet->addDefinedName(DefinedName::createInstance($name, $workSheet, $value));
        }

        $allDefinedNames = $spreadSheet->getDefinedNames();
        self::assertCount(count($definedNamesForTest), $allDefinedNames);
    }

    public function testGetNamedRanges(): void
    {
        $spreadSheet = new Spreadsheet();
        $workSheet = $spreadSheet->getActiveSheet();

        $rangeOrFormula = [];
        $definedNamesForTest = $this->providerRangeOrFormula();
        foreach ($definedNamesForTest as $key => $definedNameData) {
            [$value, $isFormula] = $definedNameData;
            $rangeOrFormula[] = !$isFormula;
            $name = str_replace([' ', '-'], '_', $key);
            $spreadSheet->addDefinedName(DefinedName::createInstance($name, $workSheet, $value));
        }

        $allNamedRanges = $spreadSheet->getNamedRanges();
        self::assertCount(count(array_filter($rangeOrFormula)), $allNamedRanges);
    }

    public function testGetScopedNamedRange(): void
    {
        $rangeName = 'NAMED_RANGE';
        $globalRangeValue = 'A1';
        $localRangeValue = 'A2';

        $spreadSheet = new Spreadsheet();
        $workSheet = $spreadSheet->getActiveSheet();

        $spreadSheet->addDefinedName(DefinedName::createInstance($rangeName, $workSheet, $globalRangeValue));
        $spreadSheet->addDefinedName(DefinedName::createInstance($rangeName, $workSheet, $localRangeValue, true));

        $localScopedRange = $spreadSheet->getNamedRange($rangeName, $workSheet);
        self::assertNotNull($localScopedRange);
        self::assertSame($localRangeValue, $localScopedRange->getValue());
    }

    public function testGetGlobalNamedRange(): void
    {
        $rangeName = 'NAMED_RANGE';
        $globalRangeValue = 'A1';
        $localRangeValue = 'A2';

        $spreadSheet = new Spreadsheet();
        $workSheet1 = $spreadSheet->getActiveSheet();
        $spreadSheet->createSheet(1);
        $workSheet2 = $spreadSheet->getSheet(1);

        $spreadSheet->addDefinedName(DefinedName::createInstance($rangeName, $workSheet1, $globalRangeValue));
        $spreadSheet->addDefinedName(DefinedName::createInstance($rangeName, $workSheet1, $localRangeValue, true));

        $localScopedRange = $spreadSheet->getNamedRange($rangeName, $workSheet2);
        self::assertNotNull($localScopedRange);
        self::assertSame($globalRangeValue, $localScopedRange->getValue());
    }

    public function testGetNamedFormulae(): void
    {
        $spreadSheet = new Spreadsheet();
        $workSheet = $spreadSheet->getActiveSheet();

        $rangeOrFormula = [];
        $definedNamesForTest = $this->providerRangeOrFormula();
        foreach ($definedNamesForTest as $key => $definedNameData) {
            [$value, $isFormula] = $definedNameData;
            $rangeOrFormula[] = $isFormula;
            $name = str_replace([' ', '-'], '_', $key);
            $spreadSheet->addDefinedName(DefinedName::createInstance($name, $workSheet, $value));
        }

        $allNamedFormulae = $spreadSheet->getNamedFormulae();
        self::assertCount(count(array_filter($rangeOrFormula)), $allNamedFormulae);
    }

    public function testGetScopedNamedFormula(): void
    {
        $formulaName = 'GERMAN_VAT_RATE';
        $globalFormulaValue = '=19.0%';
        $localFormulaValue = '=16.0%';

        $spreadSheet = new Spreadsheet();
        $workSheet = $spreadSheet->getActiveSheet();

        $spreadSheet->addDefinedName(DefinedName::createInstance($formulaName, $workSheet, $globalFormulaValue));
        $spreadSheet->addDefinedName(DefinedName::createInstance($formulaName, $workSheet, $localFormulaValue, true));

        $localScopedFormula = $spreadSheet->getNamedFormula($formulaName, $workSheet);
        self::assertNotNull($localScopedFormula);
        self::assertSame($localFormulaValue, $localScopedFormula->getValue());
    }

    public function testGetGlobalNamedFormula(): void
    {
        $formulaName = 'GERMAN_VAT_RATE';
        $globalFormulaValue = '=19.0%';
        $localFormulaValue = '=16.0%';

        $spreadSheet = new Spreadsheet();
        $workSheet1 = $spreadSheet->getActiveSheet();
        $spreadSheet->createSheet(1);
        $workSheet2 = $spreadSheet->getSheet(1);

        $spreadSheet->addDefinedName(DefinedName::createInstance($formulaName, $workSheet1, $globalFormulaValue));
        $spreadSheet->addDefinedName(DefinedName::createInstance($formulaName, $workSheet1, $localFormulaValue, true));

        $localScopedFormula = $spreadSheet->getNamedFormula($formulaName, $workSheet2);
        self::assertNotNull($localScopedFormula);
        self::assertSame($globalFormulaValue, $localScopedFormula->getValue());
    }

    public function providerRangeOrFormula(): array
    {
        return [
            'simple range' => ['A1', false],
            'simple absolute range' => ['$A$1', false],
            'simple integer value' => ['42', true],
            'simple float value' => ['12.5', true],
            'simple string value' => ['"HELLO WORLD"', true],
            'range with a worksheet name' => ['Sheet2!$A$1', false],
            'range with a quoted worksheet name' => ["'Work Sheet #2'!\$A\$1:\$E\$1", false],
            'range with a quoted worksheet name containing quotes' => ["'Mark''s WorkSheet'!\$A\$1:\$E\$1", false],
            'range with a utf-8 worksheet name' => ['Γειά!$A$1', false],
            'range with a quoted utf-8 worksheet name' => ["'Γειά σου Κόσμε'!\$A\$1", false],
            'range with a quoted worksheet name with quotes in a formula' => ["'Mark''s WorkSheet'!\$A\$1+5", true],
            'range with a quoted worksheet name in a formula' => ["5*'Work Sheet #2'!\$A\$1", true],
            'multiple ranges with quoted worksheet names with quotes in a formula' => ["'Mark''s WorkSheet'!\$A\$1+'Mark''s WorkSheet'!\$B\$2", true],
            'named range in a formula' => ['NAMED_RANGE_VALUE+12', true],
            'named range and range' => ['NAMED_RANGE_VALUE_1,Sheet2!$A$1', false],
            'range with quoted utf-8 worksheet name and a named range' => ["NAMED_RANGE_VALUE_1,'Γειά σου Κόσμε'!\$A\$1", false],
            'composite named range' => ['NAMED_RANGE_VALUE_1,NAMED_RANGE_VALUE_2 NAMED_RANGE_VALUE_3', false],
            'named ranges in a formula' => ['NAMED_RANGE_VALUE_1/NAMED_RANGE_VALUE_2', true],
            'utf-8 named range' => ['Γειά', false],
            'utf-8 named range in a formula' => ['2*Γειά', true],
            'utf-8 named ranges' => ['Γειά,σου Κόσμε', false],
            'utf-8 named ranges in a formula' => ['Здравствуй+мир', true],
        ];
    }

    public function testEmptyNamedFormula(): void
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);
        $spreadSheet = new Spreadsheet();
        $workSheet1 = $spreadSheet->getActiveSheet();
        new NamedFormula('namedformula', $workSheet1);
    }

    public function testChangeFormula(): void
    {
        $spreadSheet = new Spreadsheet();
        $workSheet1 = $spreadSheet->getActiveSheet();
        $namedFormula = new NamedFormula('namedformula', $workSheet1, '=1');
        self::assertEquals('=1', $namedFormula->getFormula());
        $namedFormula->setFormula('=2');
        self::assertEquals('=2', $namedFormula->getFormula());
        $namedFormula->setFormula('');
        self::assertEquals('=2', $namedFormula->getFormula());
    }
}
