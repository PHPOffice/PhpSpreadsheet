<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet\AutoFilter;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AutoFilterCustomNumericTest extends SetupTeardown
{
    public function initsheet(): Worksheet
    {
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue('Header');
        $sheet->getCell('A2')->setValue(1);
        $sheet->getCell('A3')->setValue(3);
        $sheet->getCell('A4')->setValue(5);
        // nothing in cell A5
        $sheet->getCell('A6')->setValue(7);
        $sheet->getCell('A7')->setValue(9);
        $sheet->getCell('A8')->setValue(7);
        $sheet->getCell('A9')->setValue(5);
        $sheet->getCell('A10')->setValue(3);
        $sheet->getCell('A11')->setValue(1);
        $sheet->getCell('A12')->setValue('x');
        $this->maxRow = 12;

        return $sheet;
    }

    public static function providerCustomRule(): array
    {
        return [
            'equal to 3' => [[3, 10], Rule::AUTOFILTER_COLUMN_RULE_EQUAL, 3],
            'not equal to 3' => [[2, 4, 5, 6, 7, 8, 9, 11, 12], Rule::AUTOFILTER_COLUMN_RULE_NOTEQUAL, 3],
            'greater than 3' => [[4, 6, 7, 8, 9], Rule::AUTOFILTER_COLUMN_RULE_GREATERTHAN, 3],
            'greater than or equal to 3' => [[3, 4, 6, 7, 8, 9, 10], Rule::AUTOFILTER_COLUMN_RULE_GREATERTHANOREQUAL, 3],
            'less than 3' => [[2, 11], Rule::AUTOFILTER_COLUMN_RULE_LESSTHAN, 3],
            'less than or equal to 3' => [[2, 3, 10, 11], Rule::AUTOFILTER_COLUMN_RULE_LESSTHANOREQUAL, 3],
        ];
    }

    /**
     * @dataProvider providerCustomRule
     */
    public function testCustomTest(array $expectedVisible, string $rule, int $comparand): void
    {
        $sheet = $this->initSheet();
        $maxRow = $this->maxRow;
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange("A1:A$maxRow");
        $columnFilter = $autoFilter->getColumn('A');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_CUSTOMFILTER);
        $columnFilter->createRule()
            ->setRule(
                $rule,
                $comparand
            )
            ->setRuleType(Rule::AUTOFILTER_RULETYPE_CUSTOMFILTER);

        self::assertEquals($expectedVisible, $this->getVisible());
    }

    public function testEqualsListSimple(): void
    {
        $sheet = $this->initSheet();
        $maxRow = $this->maxRow;
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange("A1:A$maxRow");
        $columnFilter = $autoFilter->getColumn('A');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                3
            )
            ->setRuleType(Rule::AUTOFILTER_RULETYPE_FILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                5
            )
            ->setRuleType(Rule::AUTOFILTER_RULETYPE_FILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                7
            )
            ->setRuleType(Rule::AUTOFILTER_RULETYPE_FILTER);

        self::assertEquals([3, 4, 6, 8, 9, 10], $this->getVisible());
    }

    public function testEqualsList(): void
    {
        $sheet = $this->initSheet();
        $sheet->getRowDimension(4)->setRowHeight(25);
        $maxRow = $this->maxRow;
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange("A1:A$maxRow");
        $columnFilter = $autoFilter->getColumn('A');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_CUSTOMFILTER);
        $columnFilter->setJoin(Column::AUTOFILTER_COLUMN_JOIN_OR);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                3
            )
            ->setRuleType(Rule::AUTOFILTER_RULETYPE_CUSTOMFILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                5
            )
            ->setRuleType(Rule::AUTOFILTER_RULETYPE_CUSTOMFILTER);

        self::assertEquals([3, 4, 9, 10], $this->getVisible());
        self::assertTrue($sheet->rowDimensionExists(2));
        self::assertFalse($sheet->rowDimensionExists(3), 'row visible by default');
        self::assertTrue($sheet->rowDimensionExists(4), 'row is visible but height has been set');
        self::assertTrue($sheet->rowDimensionExists(5));
        self::assertTrue($sheet->rowDimensionExists(6));
        self::assertTrue($sheet->rowDimensionExists(7));
        self::assertTrue($sheet->rowDimensionExists(8));
        self::assertFalse($sheet->rowDimensionExists(9), 'row visible by default');
        self::assertFalse($sheet->rowDimensionExists(10), 'row visible by default');
        self::assertTrue($sheet->rowDimensionExists(11));
        self::assertTrue($sheet->rowDimensionExists(12));
    }

    public function testNotEqualsList(): void
    {
        $sheet = $this->initSheet();
        $maxRow = $this->maxRow;
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange("A1:A$maxRow");
        $columnFilter = $autoFilter->getColumn('A');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_CUSTOMFILTER);
        $columnFilter->setJoin(Column::AUTOFILTER_COLUMN_JOIN_AND);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_NOTEQUAL,
                3
            )
            ->setRuleType(Rule::AUTOFILTER_RULETYPE_CUSTOMFILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_NOTEQUAL,
                5
            )
            ->setRuleType(Rule::AUTOFILTER_RULETYPE_CUSTOMFILTER);

        self::assertEquals([2, 5, 6, 7, 8, 11, 12], $this->getVisible());
    }

    public function testNotEqualsListWith3Members(): void
    {
        $this->expectException(PhpSpreadsheetException::class);
        $this->expectExceptionMessage('No more than 2 rules');
        $sheet = $this->initSheet();
        $maxRow = $this->maxRow;
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange("A1:A$maxRow");
        $columnFilter = $autoFilter->getColumn('A');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_CUSTOMFILTER);
        $columnFilter->setJoin(Column::AUTOFILTER_COLUMN_JOIN_AND);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_NOTEQUAL,
                3
            )
            ->setRuleType(Rule::AUTOFILTER_RULETYPE_CUSTOMFILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_NOTEQUAL,
                5
            )
            ->setRuleType(Rule::AUTOFILTER_RULETYPE_CUSTOMFILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_NOTEQUAL,
                7
            )
            ->setRuleType(Rule::AUTOFILTER_RULETYPE_CUSTOMFILTER);

        self::assertEquals([2, 5, 7, 11, 12], $this->getVisible());
    }

    public function testNotEqualsListWith3MembersFilterTypeAfterRules(): void
    {
        $this->expectException(PhpSpreadsheetException::class);
        $this->expectExceptionMessage('No more than 2 rules');
        $sheet = $this->initSheet();
        $maxRow = $this->maxRow;
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange("A1:A$maxRow");
        $columnFilter = $autoFilter->getColumn('A');
        $columnFilter->setJoin(Column::AUTOFILTER_COLUMN_JOIN_AND);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_NOTEQUAL,
                3
            )
            ->setRuleType(Rule::AUTOFILTER_RULETYPE_CUSTOMFILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_NOTEQUAL,
                5
            )
            ->setRuleType(Rule::AUTOFILTER_RULETYPE_CUSTOMFILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_NOTEQUAL,
                7
            )
            ->setRuleType(Rule::AUTOFILTER_RULETYPE_CUSTOMFILTER);
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_CUSTOMFILTER);

        self::assertEquals([2, 5, 7, 11, 12], $this->getVisible());
    }
}
