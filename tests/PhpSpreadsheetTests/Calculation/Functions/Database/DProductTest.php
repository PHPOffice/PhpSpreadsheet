<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Database;

use PhpOffice\PhpSpreadsheet\Calculation\Database\DProduct;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Helpers as DateTimeHelper;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class DProductTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

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
        $result = DProduct::evaluate($database, $field, $criteria);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-12);
    }

    private function database1(): array
    {
        return [
            ['Tree', 'Height', 'Age', 'Yield', 'Profit'],
            ['Apple', 18, 20, 14, 105],
            ['Pear', 12, 12, 10, 96],
            ['Cherry', 13, 14, 9, 105],
            ['Apple', 14, 15, 10, 75],
            ['Pear', 9, 8, 8, 77],
            ['Apple', 8, 9, 6, 45],
        ];
    }

    private function database2(): array
    {
        return [
            ['Name', 'Date', 'Test', 'Score'],
            ['Gary', DateTimeHelper::getDateValue('01-Jan-2017'), 'Test1', 4],
            ['Gary', DateTimeHelper::getDateValue('01-Jan-2017'), 'Test2', 4],
            ['Gary', DateTimeHelper::getDateValue('01-Jan-2017'), 'Test3', 3],
            ['Gary', DateTimeHelper::getDateValue('05-Jan-2017'), 'Test1', 3],
            ['Gary', DateTimeHelper::getDateValue('05-Jan-2017'), 'Test2', 4],
            ['Gary', DateTimeHelper::getDateValue('05-Jan-2017'), 'Test3', 3],
            ['Kev', DateTimeHelper::getDateValue('02-Jan-2017'), 'Test1', 2],
            ['Kev', DateTimeHelper::getDateValue('02-Jan-2017'), 'Test2', 3],
            ['Kev', DateTimeHelper::getDateValue('02-Jan-2017'), 'Test3', 5],
            ['Kev', DateTimeHelper::getDateValue('05-Jan-2017'), 'Test1', 3],
            ['Kev', DateTimeHelper::getDateValue('05-Jan-2017'), 'Test2', 2],
            ['Kev', DateTimeHelper::getDateValue('05-Jan-2017'), 'Test3', 5],
        ];
    }

    public function providerDProduct(): array
    {
        return [
            [
                800,
                $this->database1(),
                'Yield',
                [
                    ['Tree', 'Height', 'Height'],
                    ['=Apple', '>10', '<16'],
                    ['=Pear', null, null],
                ],
            ],
            [
                36,
                $this->database2(),
                'Score',
                [
                    ['Name', 'Date'],
                    ['Gary', '05-Jan-2017'],
                ],
            ],
            [
                8,
                $this->database2(),
                'Score',
                [
                    ['Test', 'Date'],
                    ['Test1', '<05-Jan-2017'],
                ],
            ],
            [
                null,
                $this->database1(),
                null,
                $this->database1(),
            ],
        ];
    }
}
