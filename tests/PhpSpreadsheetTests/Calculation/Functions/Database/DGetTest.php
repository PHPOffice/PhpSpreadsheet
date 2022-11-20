<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Database;

class DGetTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerDGet
     *
     * @param mixed $expectedResult
     * @param mixed $database
     * @param mixed $field
     * @param mixed $criteria
     */
    public function testDGet($expectedResult, $database, $field, $criteria): void
    {
        $this->runTestCase('DGET', $expectedResult, $database, $field, $criteria);
    }

    public function providerDGet(): array
    {
        return [
            [
                '#NUM!',
                $this->database1(),
                'Yield',
                [
                    ['Tree'],
                    ['=Apple'],
                    ['=Pear'],
                ],
            ],
            [
                10,
                $this->database1(),
                'Yield',
                [
                    ['Tree', 'Height', 'Height'],
                    ['=Apple', '>10', '<16'],
                    ['=Pear', '>12', null],
                ],
            ],
            [
                188000,
                $this->database2(),
                'Sales',
                [
                    ['Sales Rep.', 'Quarter'],
                    ['Tina', 4],
                ],
            ],
            [
                '#NUM!',
                $this->database2(),
                'Sales',
                [
                    ['Area', 'Quarter'],
                    ['South', 4],
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
