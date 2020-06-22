<?php

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\DefinedName;
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

    public function providerRangeOrFormula()
    {
        return [
            'simple range' => ['A1', false],
            'simple absolute range' => ['$A$1', false],
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
}
