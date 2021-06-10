<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

class TextTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerTEXT
     *
     * @param mixed $expectedResult
     * @param mixed $value
     * @param mixed $format
     */
    public function testTEXT($expectedResult, $value = 'omitted', $format = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($value === 'omitted') {
            $sheet->getCell('B1')->setValue('=TEXT()');
        } elseif ($format === 'omitted') {
            $this->setCell('A1', $value);
            $sheet->getCell('B1')->setValue('=TEXT(A1)');
        } else {
            $this->setCell('A1', $value);
            $this->setCell('A2', $format);
            $sheet->getCell('B1')->setValue('=TEXT(A1, A2)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerTEXT(): array
    {
        return require 'tests/data/Calculation/TextData/TEXT.php';
    }
}
