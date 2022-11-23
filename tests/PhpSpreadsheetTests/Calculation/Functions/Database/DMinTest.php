<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Database;

class DMinTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerDMin
     *
     * @param mixed $expectedResult
     * @param mixed $database
     * @param mixed $field
     * @param mixed $criteria
     */
    public function testDMin($expectedResult, $database, $field, $criteria): void
    {
        $this->runTestCase('DMIN', $expectedResult, $database, $field, $criteria);
    }

    public function providerDMin(): array
    {
        return [
            [
                75,
                $this->database1(),
                'Profit',
                [
                    ['Tree', 'Height', 'Height'],
                    ['=Apple', '>10', '<16'],
                    ['=Pear', '>12', null],
                ],
            ],
            [
                0.48,
                $this->database3(),
                'Score',
                [
                    ['Subject', 'Age'],
                    ['Science', '>8'],
                ],
            ],
            [
                0.55,
                $this->database3(),
                'Score',
                [
                    ['Subject', 'Gender'],
                    ['Math', 'Male'],
                ],
            ],
            'omitted field name' => [
                '#VALUE!',
                $this->database1(),
                null,
                $this->database1(),
            ],
            'field column number okay' => [
                8,
                $this->database1(),
                2,
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
