<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Database;

class DAverageTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerDAverage
     *
     * @param mixed $expectedResult
     * @param int|string $field
     */
    public function testDAverage($expectedResult, array $database, $field, array $criteria): void
    {
        $this->runTestCase('DAVERAGE', $expectedResult, $database, $field, $criteria);
    }

    public function providerDAverage(): array
    {
        return [
            [
                12,
                $this->database1(),
                'Yield',
                [
                    ['Tree', 'Height'],
                    ['=Apple', '>10'],
                ],
            ],
            'numeric column, in this case referring to age' => [
                13,
                $this->database1(),
                3,
                $this->database1(),
            ],
            [
                268333.333333333333,
                $this->database2(),
                'Sales',
                [
                    ['Quarter', 'Sales Rep.'],
                    ['>1', 'Tina'],
                ],
            ],
            [
                372500,
                $this->database2(),
                'Sales',
                [
                    ['Quarter', 'Area'],
                    ['1', 'South'],
                ],
            ],
            'null field' => [
                '#VALUE!',
                $this->database1(),
                null,
                $this->database1(),
            ],
            'field unknown column' => [
                '#VALUE!',
                $this->database1(),
                'xyz',
                $this->database1(),
            ],
            'multiple criteria, omit equal sign' => [
                10.5,
                $this->database1(),
                'Yield',
                [
                    ['Tree', 'Height'],
                    ['=Apple', '>10'],
                    ['Pear'],
                ],
            ],
            'multiple criteria for same field' => [
                10,
                $this->database1(),
                'Yield',
                [
                    ['Tree', 'Height', 'Age', 'Height'],
                    ['=Apple', '>10', null, '<16'],
                ],
            ],
            /* Excel seems to return #NAME? when column number
               is too high or too low. This makes so little sense
               to me that I'm not going to bother coding that up,
               content to return #VALUE! as an invalid name would */
            'field column number too high' => [
                '#VALUE!',
                $this->database1(),
                99,
                $this->database1(),
            ],
            'field column number too low' => [
                '#VALUE!',
                $this->database1(),
                0,
                $this->database1(),
            ],
        ];
    }
}
