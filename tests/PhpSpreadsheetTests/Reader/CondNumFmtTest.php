<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PHPUnit\Framework\TestCase;

class CondNumFmtTest extends TestCase
{
    public function testLoadCondNumFmt(): void
    {
        $filename = 'tests/data/Reader/XLSX/condfmtnum.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);

        $worksheet = $spreadsheet->getActiveSheet();
        // NumberFormat explicitly set in following conditional style
        $conditionalStyle = $worksheet->getConditionalStyles('A1:A3');
        self::assertNotEmpty($conditionalStyle);
        $conditionalRule = $conditionalStyle[0];
        $conditions = $conditionalRule->getConditions();
        self::assertNotEmpty($conditions);
        self::assertEquals(Conditional::CONDITION_EXPRESSION, $conditionalRule->getConditionType());
        self::assertEquals('MONTH(A1)=10', $conditions[0]);
        $numfmt = $conditionalRule->getStyle()->getNumberFormat()->getFormatCode();
        self::assertEquals('yyyy/mm/dd', $numfmt);
        // NumberFormat not set in following conditional style
        $conditionalStyle = $worksheet->getConditionalStyles('B1');
        self::assertNotEmpty($conditionalStyle);
        $conditionalRule = $conditionalStyle[0];
        $conditions = $conditionalRule->getConditions();
        self::assertNotEmpty($conditions);
        self::assertEquals(Conditional::CONDITION_EXPRESSION, $conditionalRule->getConditionType());
        self::assertEquals('AND(B1>=2000,B1<3000)', $conditions[0]);
        $numfmt = $conditionalRule->getStyle()->getNumberFormat()->getFormatCode();
        self::assertEquals('', $numfmt);
    }
}
