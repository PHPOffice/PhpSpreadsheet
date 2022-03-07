<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class ConditionalTest extends AbstractFunctional
{
    /**
     * Test check if conditional style with type 'notContainsText' works on xlsx.
     */
    public function testConditionalNotContainsText(): void
    {
        $filename = 'tests/data/Reader/XLSX/conditionalFormatting3Test.xlsx';
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filename);
        $worksheet = $spreadsheet->getActiveSheet();
        $styles = $worksheet->getConditionalStyles('A1:A5');

        self::assertCount(1, $styles);

        /** @var Conditional $notContainsTextStyle */
        $notContainsTextStyle = $styles[0];
        self::assertEquals('A', $notContainsTextStyle->getText());
        self::assertEquals(Conditional::CONDITION_NOTCONTAINSTEXT, $notContainsTextStyle->getConditionType());
        self::assertEquals(Conditional::OPERATOR_NOTCONTAINS, $notContainsTextStyle->getOperatorType());
    }
}
