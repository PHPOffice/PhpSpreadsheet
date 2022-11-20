<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Database;

class DSumTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerDSum
     *
     * @param mixed $expectedResult
     * @param mixed $database
     * @param mixed $field
     * @param mixed $criteria
     */
    public function testDSum($expectedResult, $database, $field, $criteria): void
    {
        $this->runTestCase('DSUM', $expectedResult, $database, $field, $criteria);
    }

    public function providerDSum(): array
    {
        return [
            [
                225,
                $this->database1(),
                'Profit',
                [
                    ['Tree'],
                    ['=Apple'],
                ],
            ],
            [
                247.8,
                $this->database1(),
                'Profit',
                [
                    ['Tree', 'Height', 'Height'],
                    ['=Apple', '>10', '<16'],
                    ['=Pear', null, null],
                ],
            ],
            [
                1210000,
                $this->database2(),
                'Sales',
                [
                    ['Quarter', 'Area'],
                    ['>2', 'North'],
                ],
            ],
            [
                710000,
                $this->database2(),
                'Sales',
                [
                    ['Quarter', 'Sales Rep.'],
                    ['3', 'C*'],
                ],
            ],
            [
                705000,
                $this->database2(),
                'Sales',
                [
                    ['Quarter', 'Sales Rep.'],
                    ['3', '<>C*'],
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
