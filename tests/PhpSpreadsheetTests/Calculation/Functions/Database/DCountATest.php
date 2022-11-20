<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Database;

class DCountATest extends AllSetupTeardown
{
    /**
     * @dataProvider providerDCountA
     *
     * @param mixed $expectedResult
     * @param mixed $database
     * @param mixed $field
     * @param mixed $criteria
     */
    public function testDCountA($expectedResult, $database, $field, $criteria): void
    {
        $this->runTestCase('DCOUNTA', $expectedResult, $database, $field, $criteria);
    }

    public function providerDCountA(): array
    {
        return [
            [
                1,
                $this->database1(),
                'Profit',
                [
                    ['Tree', 'Height', 'Height'],
                    ['=Apple', '>10', '<16'],
                ],
            ],
            [
                2,
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
            [
                3,
                $this->database3(),
                'Score',
                [
                    ['Subject', 'Score'],
                    ['English', '>60%'],
                ],
            ],
            'invalid field name' => [
                '#VALUE!',
                $this->database3(),
                'Scorex',
                [
                    ['Subject', 'Score'],
                    ['English', '>60%'],
                ],
            ],
        ];
    }
}
