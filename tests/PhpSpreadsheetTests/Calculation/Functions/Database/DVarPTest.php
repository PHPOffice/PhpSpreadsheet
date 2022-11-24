<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Database;

class DVarPTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerDVarP
     *
     * @param mixed $expectedResult
     * @param mixed $database
     * @param mixed $field
     * @param mixed $criteria
     */
    public function testDVarP($expectedResult, $database, $field, $criteria): void
    {
        $this->runTestCase('DVARP', $expectedResult, $database, $field, $criteria);
    }

    public function providerDVarP(): array
    {
        return [
            [
                7.04,
                $this->database1(),
                'Yield',
                [
                    ['Tree'],
                    ['=Apple'],
                    ['=Pear'],
                ],
            ],
            [
                0.025622222222,
                $this->database3FilledIn(),
                'Score',
                [
                    ['Subject', 'Gender'],
                    ['Math', 'Male'],
                ],
            ],
            [
                0.011622222222,
                $this->database3FilledIn(),
                'Score',
                [
                    ['Subject', 'Age'],
                    ['Science', '>8'],
                ],
            ],
            'Omitted field name' => [
                '#VALUE!',
                $this->database1(),
                null,
                $this->database1(),
            ],
        ];
    }
}
