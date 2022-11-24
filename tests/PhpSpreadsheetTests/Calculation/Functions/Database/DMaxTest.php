<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Database;

class DMaxTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerDMax
     *
     * @param mixed $expectedResult
     * @param mixed $database
     * @param mixed $field
     * @param mixed $criteria
     */
    public function testDMax($expectedResult, $database, $field, $criteria): void
    {
        $this->runTestCase('DMAX', $expectedResult, $database, $field, $criteria);
    }

    public function providerDMax(): array
    {
        return [
            [
                96,
                $this->database1(),
                'Profit',
                [
                    ['Tree', 'Height', 'Height'],
                    ['=Apple', '>10', '<16'],
                    ['=Pear', null, null],
                ],
            ],
            [
                340000,
                $this->database2(),
                'Sales',
                [
                    ['Quarter', 'Area'],
                    [2, 'North'],
                ],
            ],
            [
                460000,
                $this->database2(),
                'Sales',
                [
                    ['Sales Rep.', 'Quarter'],
                    ['Carol', '>1'],
                ],
            ],
            'omitted field name' => [
                '#VALUE!',
                $this->database1(),
                null,
                $this->database1(),
            ],
            'field column number okay' => [
                18,
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
