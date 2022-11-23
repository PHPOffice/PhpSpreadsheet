<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Database;

use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;

class DProductTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerDProduct
     *
     * @param mixed $expectedResult
     * @param mixed $database
     * @param mixed $field
     * @param mixed $criteria
     */
    public function testDProduct($expectedResult, $database, $field, $criteria): void
    {
        $this->runTestCase('DPRODUCT', $expectedResult, $database, $field, $criteria);
    }

    private function database5(): array
    {
        return [
            ['Name', 'Date', 'Test', 'Score'],
            ['Gary', DateTimeExcel\Helpers::getDateValue('01-Jan-2017'), 'Test1', 4],
            ['Gary', DateTimeExcel\Helpers::getDateValue('01-Jan-2017'), 'Test2', 4],
            ['Gary', DateTimeExcel\Helpers::getDateValue('01-Jan-2017'), 'Test3', 3],
            ['Gary', DateTimeExcel\Helpers::getDateValue('05-Jan-2017'), 'Test1', 3],
            ['Gary', DateTimeExcel\Helpers::getDateValue('05-Jan-2017'), 'Test2', 4],
            ['Gary', DateTimeExcel\Helpers::getDateValue('05-Jan-2017'), 'Test3', 3],
            ['Kev', DateTimeExcel\Helpers::getDateValue('02-Jan-2017'), 'Test1', 2],
            ['Kev', DateTimeExcel\Helpers::getDateValue('02-Jan-2017'), 'Test2', 3],
            ['Kev', DateTimeExcel\Helpers::getDateValue('02-Jan-2017'), 'Test3', 5],
            ['Kev', DateTimeExcel\Helpers::getDateValue('05-Jan-2017'), 'Test1', 3],
            ['Kev', DateTimeExcel\Helpers::getDateValue('05-Jan-2017'), 'Test2', 2],
            ['Kev', DateTimeExcel\Helpers::getDateValue('05-Jan-2017'), 'Test3', 5],
        ];
    }

    public function providerDProduct(): array
    {
        return [
            [
                800.0,
                $this->database1(),
                'Yield',
                [
                    ['Tree', 'Height', 'Height'],
                    ['=Apple', '>10', '<16'],
                    ['=Pear', null, null],
                ],
            ],
            [
                36.0,
                $this->database5(),
                'Score',
                [
                    ['Name', 'Date'],
                    ['Gary', '05-Jan-2017'],
                ],
            ],
            [
                8.0,
                $this->database5(),
                'Score',
                [
                    ['Test', 'Date'],
                    ['Test1', '<05-Jan-2017'],
                ],
            ],
            'omitted field name' => [
                '#VALUE!',
                $this->database1(),
                null,
                $this->database1(),
            ],
        ];
    }
}
