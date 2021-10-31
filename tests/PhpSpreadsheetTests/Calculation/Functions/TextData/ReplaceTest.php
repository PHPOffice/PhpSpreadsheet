<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

class ReplaceTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerREPLACE
     *
     * @param mixed $expectedResult
     * @param mixed $oldText
     * @param mixed $start
     * @param mixed $count
     * @param mixed $newText
     */
    public function testREPLACE($expectedResult, $oldText = 'omitted', $start = 'omitted', $count = 'omitted', $newText = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($oldText === 'omitted') {
            $sheet->getCell('B1')->setValue('=REPLACE()');
        } elseif ($start === 'omitted') {
            $this->setCell('A1', $oldText);
            $sheet->getCell('B1')->setValue('=REPLACE(A1)');
        } elseif ($count === 'omitted') {
            $this->setCell('A1', $oldText);
            $this->setCell('A2', $start);
            $sheet->getCell('B1')->setValue('=REPLACE(A1, A2)');
        } elseif ($newText === 'omitted') {
            $this->setCell('A1', $oldText);
            $this->setCell('A2', $start);
            $this->setCell('A3', $count);
            $sheet->getCell('B1')->setValue('=REPLACE(A1, A2, A3)');
        } else {
            $this->setCell('A1', $oldText);
            $this->setCell('A2', $start);
            $this->setCell('A3', $count);
            $this->setCell('A4', $newText);
            $sheet->getCell('B1')->setValue('=REPLACE(A1, A2, A3, A4)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerREPLACE(): array
    {
        return require 'tests/data/Calculation/TextData/REPLACE.php';
    }
}
