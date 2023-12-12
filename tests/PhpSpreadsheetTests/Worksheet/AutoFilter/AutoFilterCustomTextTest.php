<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet\AutoFilter;

use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AutoFilterCustomTextTest extends SetupTeardown
{
    public function initsheet(): Worksheet
    {
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue('Header');
        $sheet->getCell('A2')->setValue('abc');
        $sheet->getCell('A3')->setValue('cBa');
        $sheet->getCell('A4')->setValue('cab');
        // nothing in cell A5
        $sheet->getCell('A6')->setValue('c*b');
        $sheet->getCell('A7')->setValue('c?b');
        $sheet->getCell('A8')->setValue('a');
        $sheet->getCell('A9')->setValue('zzbc');
        $sheet->getCell('A10')->setValue('zzbcd');
        $sheet->getCell('A11')->setValue('~pqr');
        $sheet->getCell('A12')->setValue('pqr~');
        $this->maxRow = 12;

        return $sheet;
    }

    public static function providerCustomText(): array
    {
        return [
            'begins with a' => [[2, 8], 'a*'],
            'ends with b' => [[4, 6, 7], '*b'],
            'contains c' => [[2, 3, 4, 6, 7, 9, 10], '*c*'],
            'empty' => [[5], ''],
            'contains asterisk' => [[6], '*~**'],
            'contains question mark' => [[7], '*~?*'],
            'c followed by character followed by b' => [[4, 6, 7], 'c?b'],
            'one character followed by bc' => [[2], '?bc'],
            'two characters followed by bc' => [[9], '??bc'],
            'starts with z ends with c' => [[9], 'z*c'],
            'starts with tilde' => [[11], '~~*'],
            'contains tilde' => [[11, 12], '*~~*'],
        ];
    }

    /**
     * @dataProvider providerCustomText
     */
    public function testCustomTest(array $expectedVisible, string $pattern): void
    {
        $sheet = $this->initSheet();
        $maxRow = $this->maxRow;
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange("A1:A$maxRow");
        $columnFilter = $autoFilter->getColumn('A');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_CUSTOMFILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                $pattern
            )
            ->setRuleType(Rule::AUTOFILTER_RULETYPE_CUSTOMFILTER);

        self::assertEquals($expectedVisible, $this->getVisible());
    }

    public function testCustomTestNotEqualBlank(): void
    {
        $sheet = $this->initSheet();
        $maxRow = $this->maxRow;
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange("A1:A$maxRow");
        $columnFilter = $autoFilter->getColumn('A');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_CUSTOMFILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_NOTEQUAL,
                ''
            )
            ->setRuleType(Rule::AUTOFILTER_RULETYPE_CUSTOMFILTER);

        self::assertEquals([2, 3, 4, 6, 7, 8, 9, 10, 11, 12], $this->getVisible());
    }

    public function testCustomTestNotEqualString(): void
    {
        $sheet = $this->initSheet();
        $maxRow = $this->maxRow;
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange("A1:A$maxRow");
        $columnFilter = $autoFilter->getColumn('A');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_CUSTOMFILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_NOTEQUAL,
                'cba'
            )
            ->setRuleType(Rule::AUTOFILTER_RULETYPE_CUSTOMFILTER);

        self::assertEquals([2, 4, 5, 6, 7, 8, 9, 10, 11, 12], $this->getVisible());
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
                'c?b' // simple filter - no wildcards
            )
            ->setRuleType(Rule::AUTOFILTER_RULETYPE_FILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                'a'
            )
            ->setRuleType(Rule::AUTOFILTER_RULETYPE_FILTER);

        self::assertEquals([7, 8], $this->getVisible());
    }

    public function testEqualsList(): void
    {
        $sheet = $this->initSheet();
        $maxRow = $this->maxRow;
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange("A1:A$maxRow");
        $columnFilter = $autoFilter->getColumn('A');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_CUSTOMFILTER);
        $columnFilter->setJoin(Column::AUTOFILTER_COLUMN_JOIN_OR);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                'a*'
            )
            ->setRuleType(Rule::AUTOFILTER_RULETYPE_CUSTOMFILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                '*c*'
            )
            ->setRuleType(Rule::AUTOFILTER_RULETYPE_CUSTOMFILTER);

        self::assertEquals([2, 3, 4, 6, 7, 8, 9, 10], $this->getVisible());
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
                'a*'
            )
            ->setRuleType(Rule::AUTOFILTER_RULETYPE_CUSTOMFILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_NOTEQUAL,
                '*c*'
            )
            ->setRuleType(Rule::AUTOFILTER_RULETYPE_CUSTOMFILTER);

        self::assertEquals([5, 11, 12], $this->getVisible());
    }

    public static function providerCustomRule(): array
    {
        return [
            'equal to cba' => [[3], Rule::AUTOFILTER_COLUMN_RULE_EQUAL, 'cba'],
            'not equal to cba' => [[2, 4, 5, 6, 7, 8, 9, 10, 11, 12], Rule::AUTOFILTER_COLUMN_RULE_NOTEQUAL, 'cba'],
            'greater than cba' => [[9, 10, 11, 12], Rule::AUTOFILTER_COLUMN_RULE_GREATERTHAN, 'cba'],
            'greater than or equal to cba' => [[3, 9, 10, 11, 12], Rule::AUTOFILTER_COLUMN_RULE_GREATERTHANOREQUAL, 'cba'],
            'less than cba' => [[2, 4, 5, 6, 7, 8], Rule::AUTOFILTER_COLUMN_RULE_LESSTHAN, 'cba'],
            'less than or equal to cba' => [[2, 3, 4, 5, 6, 7, 8], Rule::AUTOFILTER_COLUMN_RULE_LESSTHANOREQUAL, 'cba'],
        ];
    }

    /**
     * @dataProvider providerCustomRule
     */
    public function testCustomRuleTest(array $expectedVisible, string $rule, string $comparand): void
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
}
