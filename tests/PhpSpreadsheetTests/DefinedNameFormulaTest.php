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
            ['A1', false],
            ['$A$1', false],
            ['Sheet2!$A$1', false],
            ["'Work Sheet #2'!\$A\$1:\$E\$1", false],
            ["'Mark''s WorkSheet'!\$A\$1:\$E\$1", false],
            ['Γειά!$A$1', false],
            ["'Γειά σου Κόσμε'!\$A\$1", false],
            ["'Mark''s WorkSheet'!\$A\$1+5", true],
            ["5*'Mark''s WorkSheet'!\$A\$1", true],
            ["'Mark''s WorkSheet'!\$A\$1+'Mark''s WorkSheet'!\$B\$2", true],
            ['NAMED_RANGE_VALUE+12', true],
            ['NAMED_RANGE_VALUE_1,Sheet2!$A$1', false],
            ["NAMED_RANGE_VALUE_1,'Γειά!\$A\$1'", false],
            ['NAMED_RANGE_VALUE_1,NAMED_RANGE_VALUE_2', false],
            ['NAMED_RANGE_VALUE_1 NAMED_RANGE_VALUE_2', false],
            ['NAMED_RANGE_VALUE_1/NAMED_RANGE_VALUE_2', true],
            ['Γειά', false],
            ['2*Γειά', true],
            ['Γειά,σου Κόσμε', false],
            ['Здравствуй+мир', true],
        ];
    }
}
