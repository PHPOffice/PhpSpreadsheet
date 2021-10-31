<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

class SearchTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSEARCH
     *
     * @param mixed $expectedResult
     * @param mixed $findText
     * @param mixed $withinText
     * @param mixed $start
     */
    public function testSEARCH($expectedResult, $findText = 'omitted', $withinText = 'omitted', $start = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($findText === 'omitted') {
            $sheet->getCell('B1')->setValue('=SEARCH()');
        } elseif ($withinText === 'omitted') {
            $this->setCell('A1', $findText);
            $sheet->getCell('B1')->setValue('=SEARCH(A1)');
        } elseif ($start === 'omitted') {
            $this->setCell('A1', $findText);
            $this->setCell('A2', $withinText);
            $sheet->getCell('B1')->setValue('=SEARCH(A1, A2)');
        } else {
            $this->setCell('A1', $findText);
            $this->setCell('A2', $withinText);
            $this->setCell('A3', $start);
            $sheet->getCell('B1')->setValue('=SEARCH(A1, A2, A3)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerSEARCH(): array
    {
        return require 'tests/data/Calculation/TextData/SEARCH.php';
    }
}
