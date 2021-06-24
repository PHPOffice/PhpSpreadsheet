<?php

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
        $sheet->getCell('A3')->setValue('cba');
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

    public function providerCustomText(): array
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

    public function testCustomTestNotEqual(): void
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

    public function testCustomTestGreaterThan(): void
    {
        $sheet = $this->initSheet();
        $maxRow = $this->maxRow;
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange("A1:A$maxRow");
        $columnFilter = $autoFilter->getColumn('A');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_CUSTOMFILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_GREATERTHAN,
                ''
            )
            ->setRuleType(Rule::AUTOFILTER_RULETYPE_CUSTOMFILTER);

        self::assertEquals([2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12], $this->getVisible());
    }
}
