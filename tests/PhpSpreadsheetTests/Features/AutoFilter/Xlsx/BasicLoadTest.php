<?php

namespace PhpOffice\PhpSpreadsheetTests\Features\AutoFilter\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule;
use PHPUnit\Framework\TestCase;

class BasicLoadTest extends TestCase
{
    public function testLoadAutoFilter(): void
    {
        $filename = 'tests/data/Features/AutoFilter/Xlsx/AutoFilter_Basic.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);

        $worksheet = $spreadsheet->getActiveSheet();
        self::assertSame('A1:D57', $worksheet->getAutoFilter()->getRange());
        self::assertSame(2, $worksheet->getAutoFilter()->getColumn('C')->ruleCount());
        self::assertSame(
            Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
            $worksheet->getAutoFilter()->getColumn('C')->getRules()[0]->getOperator()
        );
        self::assertSame('UK', $worksheet->getAutoFilter()->getColumn('C')->getRules()[0]->getValue());
        self::assertSame(
            Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
            $worksheet->getAutoFilter()->getColumn('C')->getRules()[1]->getOperator()
        );
        self::assertSame('United States', $worksheet->getAutoFilter()->getColumn('C')->getRules()[1]->getValue());
        self::assertSame(2, $worksheet->getAutoFilter()->getColumn('D')->ruleCount());
        self::assertSame(
            Rule::AUTOFILTER_COLUMN_RULE_GREATERTHAN,
            $worksheet->getAutoFilter()->getColumn('D')->getRules()[0]->getOperator()
        );
        self::assertSame('650', $worksheet->getAutoFilter()->getColumn('D')->getRules()[0]->getValue());
        self::assertSame(
            Rule::AUTOFILTER_COLUMN_RULE_LESSTHAN,
            $worksheet->getAutoFilter()->getColumn('D')->getRules()[1]->getOperator()
        );
        self::assertSame('800', $worksheet->getAutoFilter()->getColumn('D')->getRules()[1]->getValue());
    }

    public function testLoadOffice365AutoFilter(): void
    {
        $filename = 'tests/data/Features/AutoFilter/Xlsx/AutoFilter_Basic_Office365.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);

        $worksheet = $spreadsheet->getActiveSheet();
        self::assertSame('A1:D57', $worksheet->getAutoFilter()->getRange());
        self::assertSame(2, $worksheet->getAutoFilter()->getColumn('C')->ruleCount());
        self::assertSame(
            Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
            $worksheet->getAutoFilter()->getColumn('C')->getRules()[0]->getOperator()
        );
        self::assertSame('UK', $worksheet->getAutoFilter()->getColumn('C')->getRules()[0]->getValue());
        self::assertSame(
            Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
            $worksheet->getAutoFilter()->getColumn('C')->getRules()[1]->getOperator()
        );
        self::assertSame('United States', $worksheet->getAutoFilter()->getColumn('C')->getRules()[1]->getValue());
        self::assertSame(2, $worksheet->getAutoFilter()->getColumn('D')->ruleCount());
        self::assertSame(
            Rule::AUTOFILTER_COLUMN_RULE_GREATERTHAN,
            $worksheet->getAutoFilter()->getColumn('D')->getRules()[0]->getOperator()
        );
        self::assertSame('650', $worksheet->getAutoFilter()->getColumn('D')->getRules()[0]->getValue());
        self::assertSame(
            Rule::AUTOFILTER_COLUMN_RULE_LESSTHAN,
            $worksheet->getAutoFilter()->getColumn('D')->getRules()[1]->getOperator()
        );
        self::assertSame('800', $worksheet->getAutoFilter()->getColumn('D')->getRules()[1]->getValue());
    }
}
