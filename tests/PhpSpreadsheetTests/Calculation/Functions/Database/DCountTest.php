<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Database;

class DCountTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerDCount
     *
     * @param mixed $expectedResult
     * @param mixed $database
     * @param mixed $field
     * @param mixed $criteria
     */
    public function testDCount($expectedResult, $database, $field, $criteria): void
    {
        $this->runTestCase('DCOUNT', $expectedResult, $database, $field, $criteria);
    }

    private function database4(): array
    {
        return [
            ['Status', 'Value'],
            [false, 1],
            [true, 2],
            [true, 4],
            [false, 8],
            [true, 16],
            [false, 32],
            [false, 64],
            [false, 128],
        ];
    }

    public function providerDCount(): array
    {
        return [
            [
                1,
                $this->database1(),
                'Age',
                [
                    ['Tree', 'Height', 'Height'],
                    ['=Apple', '>10', '<16'],
                ],
            ],
            [
                1,
                $this->database3(),
                'Score',
                [
                    ['Subject', 'Gender'],
                    ['Science', 'Male'],
                ],
            ],
            [
                1,
                $this->database3(),
                'Score',
                [
                    ['Subject', 'Gender'],
                    ['Math', 'Female'],
                ],
            ],
            'omitted field name' => [
                '#VALUE!',
                $this->database3(),
                null,
                [
                    ['Subject', 'Score'],
                    ['English', '>63%'],
                ],
            ],
            [
                3,
                $this->database4(),
                'Value',
                [
                    ['Status'],
                    [true],
                ],
            ],
            [
                5,
                $this->database4(),
                'Value',
                [
                    ['Status'],
                    ['<>true'],
                ],
            ],
            'field column number okay' => [
                0,
                $this->database1(),
                1,
                $this->database1(),
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
