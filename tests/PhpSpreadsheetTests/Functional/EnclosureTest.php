<?php

namespace PhpOffice\PhpSpreadsheetTests\Functional;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class EnclosureTest extends AbstractFunctional
{
    public function providerFormats()
    {
        return [
            ['Html'],
            ['Xls'],
            ['Xlsx'],
            ['Ods'],
            ['Csv'],
        ];
    }

    /**
     * @dataProvider providerFormats
     *
     * @param string $format
     */
    public function testEnclosure($format)
    {
        $value = '<img alt="" src="http://example.com/image.jpg" />';

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->getCell('A1')->setValue($value);

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format);

        $actual = $reloadedSpreadsheet->getActiveSheet()->getCell('A1')->getCalculatedValue();
        self::assertSame($value, $actual, 'should be able to write and read strings with multiples quotes');
    }
}
