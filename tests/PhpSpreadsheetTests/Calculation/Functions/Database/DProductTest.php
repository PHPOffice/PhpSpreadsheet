<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Database;

use PhpOffice\PhpSpreadsheet\Calculation\Database\DProduct;
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
     */
    public function testDProduct($expectedResult, $database, $field, $criteria)
    {
        $result = DProduct::evaluate($database, $field, $criteria);
        self::assertSame($expectedResult, $result);
    }

    protected function database()
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

    public function providerDProduct()
    {
        return [
            [
                800,
                $this->database(),
                'Yield',
                [
                    ['Tree', 'Height', 'Height'],
                    ['=Apple', '>10', '<16'],
                    ['=Pear', null, null]
                ],
            ],
        ];
    }
}
