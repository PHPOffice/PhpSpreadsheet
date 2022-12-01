<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Database;

class DStDevTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerDStDev
     *
     * @param mixed $expectedResult
     * @param mixed $database
     * @param mixed $field
     * @param mixed $criteria
     */
    public function testDStDev($expectedResult, $database, $field, $criteria): void
    {
        $this->runTestCase('DSTDEV', $expectedResult, $database, $field, $criteria);
    }

    public function providerDStDev(): array
    {
        return [
            [
                2.966479394838,
                $this->database1(),
                'Yield',
                [
                    ['Tree'],
                    ['=Apple'],
                    ['=Pear'],
                ],
            ],
            [
                0.104403065089,
                $this->database3FilledIn(),
                'Score',
                [
                    ['Subject', 'Gender'],
                    ['English', 'Male'],
                ],
            ],
            [
                0.196723155729,
                $this->database3FilledIn(),
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
