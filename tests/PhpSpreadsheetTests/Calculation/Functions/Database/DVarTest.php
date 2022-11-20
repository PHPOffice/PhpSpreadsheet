<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Database;

class DVarTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerDVar
     *
     * @param mixed $expectedResult
     * @param mixed $database
     * @param mixed $field
     * @param mixed $criteria
     */
    public function testDVar($expectedResult, $database, $field, $criteria): void
    {
        $this->runTestCase('DVAR', $expectedResult, $database, $field, $criteria);
    }

    public function providerDVar(): array
    {
        return [
            [
                8.8,
                $this->database1(),
                'Yield',
                [
                    ['Tree'],
                    ['=Apple'],
                    ['=Pear'],
                ],
            ],
            [
                0.038433333333,
                $this->database3FilledIn(),
                'Score',
                [
                    ['Subject', 'Gender'],
                    ['Math', 'Male'],
                ],
            ],
            [
                0.017433333333,
                $this->database3FilledIn(),
                'Score',
                [
                    ['Subject', 'Age'],
                    ['Science', '>8'],
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
