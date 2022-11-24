<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Database;

class DStDevPTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerDStDevP
     *
     * @param mixed $expectedResult
     * @param mixed $database
     * @param mixed $field
     * @param mixed $criteria
     */
    public function testDStDevP($expectedResult, $database, $field, $criteria): void
    {
        $this->runTestCase('DSTDEVP', $expectedResult, $database, $field, $criteria);
    }

    public function providerDStDevP(): array
    {
        return [
            [
                2.653299832284,
                $this->database1(),
                'Yield',
                [
                    ['Tree'],
                    ['=Apple'],
                    ['=Pear'],
                ],
            ],
            [
                0.085244745684,
                $this->database3FilledIn(),
                'Score',
                [
                    ['Subject', 'Gender'],
                    ['English', 'Male'],
                ],
            ],
            [
                0.160623784042,
                $this->database3FilledIn(),
                'Score',
                [
                    ['Subject', 'Age'],
                    ['Math', '>8'],
                ],
            ],
            [
                0.01,
                $this->database3(),
                'Score',
                [
                    ['Subject', 'Gender'],
                    ['English', 'Male'],
                ],
            ],
            [
                0,
                $this->database3(),
                'Score',
                [
                    ['Subject', 'Age'],
                    ['Math', '>8'],
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
