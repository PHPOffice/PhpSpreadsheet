<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class ConditionalTest extends AbstractFunctional
{
    /**
     * @var int
     */
    private $prevValue;

    protected function setUp(): void
    {
        $this->prevValue = Settings::getLibXmlLoaderOptions();

        // Disable validating XML with the DTD
        Settings::setLibXmlLoaderOptions($this->prevValue & ~LIBXML_DTDVALID & ~LIBXML_DTDATTR & ~LIBXML_DTDLOAD);
    }

    protected function tearDown(): void
    {
        Settings::setLibXmlLoaderOptions($this->prevValue);
    }

    /**
     * Test check if conditional style with type 'notContainsText' works on xlsx.
     */
    public function testConditionalNotContainsText(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        $condition = new Conditional();
        $condition->setConditionType(Conditional::CONDITION_NOTCONTAINSTEXT);
        $condition->setOperatorType(Conditional::OPERATOR_NOTCONTAINS);
        $condition->setText('C');
        $condition->getStyle()->applyFromArray([
            'fill' => [
                'color' => ['argb' => 'FFFFC000'],
                'fillType' => Fill::FILL_SOLID,
            ],
        ]);
        $worksheet->setConditionalStyles('A1:A5', [$condition]);

        $writer = new Xlsx($spreadsheet);
        $writerWorksheet = new Xlsx\Worksheet($writer);
        $data = $writerWorksheet->writeWorksheet($worksheet, []);
        $needle = <<<xml
<conditionalFormatting sqref="A1:A5"><cfRule type="notContainsText" dxfId="" priority="1" operator="notContains" text="C"><formula>ISERROR(SEARCH(&quot;C&quot;,A1:A5))</formula></cfRule></conditionalFormatting>
xml;
        self::assertStringContainsString($needle, $data);
    }
}
