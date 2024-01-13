<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class ArrayFormulaPrefixTest extends AbstractFunctional
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test to ensure that xlfn prefix is being added to functions
     * included in an array formula, if appropriate.
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function testWriteArrayFormulaTextJoin(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        //Write data
        $worksheet->getCell('A1')->setValue('Some Text');
        $worksheet->getCell('A2')->setValue('Some More Text');
        $worksheet->getCell('A3')->setValue(14.56);
        $worksheet->getCell('A4')->setValue(17.24);
        $worksheet->getCell('A5')->setValue(9.4);
        $worksheet->getCell('A6')->setValue(5);

        //Write formula
        $cell = $worksheet->getCell('A7');
        $cell->setValueExplicit('=TEXTJOIN("",TRUE,IF(ISNUMBER(A1:A6), A1:A6,""))', DataType::TYPE_FORMULA);
        /** @var array<string, string> */
        $attrs = $cell->getFormulaAttributes();
        $attrs['t'] = 'array';
        $cell->setFormulaAttributes($attrs);

        $writer = new Xlsx($spreadsheet);
        $writerWorksheet = new Xlsx\Worksheet($writer);
        $data = $writerWorksheet->writeWorksheet($worksheet, []);

        $expected = <<<XML
            <f t="array" ref="A7" aca="1" ca="1">_xlfn.TEXTJOIN(&quot;&quot;,TRUE,IF(ISNUMBER(A1:A6), A1:A6,&quot;&quot;))</f>
            XML;
        self::assertStringContainsString($expected, $data);
    }

    /**
     * Certain functions do not have the xlfn prefix applied.  Test an array formula
     * that includes those functions to see if they are written properly.
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function testWriteArrayFormulaWithoutPrefix(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        //Write data
        $worksheet->getCell('A1')->setValue('orange');
        $worksheet->getCell('A2')->setValue('green');
        $worksheet->getCell('A3')->setValue('blue');
        $worksheet->getCell('A4')->setValue('yellow');
        $worksheet->getCell('A5')->setValue('pink');
        $worksheet->getCell('A6')->setValue('red');

        //Write formula
        $cell = $worksheet->getCell('A7');
        $cell->setValueExplicit('=SUM(LEN(A1:A6))', DataType::TYPE_FORMULA);
        /** @var array<string, string> */
        $attrs = $cell->getFormulaAttributes();
        $attrs['t'] = 'array';
        $cell->setFormulaAttributes($attrs);

        $writer = new Xlsx($spreadsheet);
        $writerWorksheet = new Xlsx\Worksheet($writer);
        $data = $writerWorksheet->writeWorksheet($worksheet, []);

        $expected = <<<XML
            <f t="array" ref="A7" aca="1" ca="1">SUM(LEN(A1:A6))</f>
            XML;
        self::assertStringContainsString($expected, $data);
    }
}
