<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class ConditionalTest extends AbstractFunctional
{

    /**
     * Test check if conditional style with type 'notContainsText' works on xlsx
     */
    public function testConditionalNotContainsText(): void
    {
        $filename = 'tests/data/Reader/XLSX/conditionalFormatting3Test.xlsx';
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filename);
        $worksheet = $spreadsheet->getActiveSheet();
        $styles = $worksheet->getConditionalStyles('A1:A5');

        $this->assertCount(1, $styles);

        /** @var $notContainsTextStyle Conditional */
        $notContainsTextStyle = $styles[0];
        $this->assertEquals('A', $notContainsTextStyle->getText());
        $this->assertEquals(Conditional::CONDITION_NOTCONTAINSTEXT, $notContainsTextStyle->getConditionType());
        $this->assertEquals(Conditional::OPERATOR_NOTCONTAINS, $notContainsTextStyle->getOperatorType());
    }
}
