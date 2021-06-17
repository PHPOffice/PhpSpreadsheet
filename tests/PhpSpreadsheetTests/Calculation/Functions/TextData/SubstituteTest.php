<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

class SubstituteTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSUBSTITUTE
     *
     * @param mixed $expectedResult
     * @param mixed $text
     * @param mixed $oldText
     * @param mixed $newText
     * @param mixed $instance
     */
    public function testSUBSTITUTE($expectedResult, $text = 'omitted', $oldText = 'omitted', $newText = 'omitted', $instance = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($text === 'omitted') {
            $sheet->getCell('B1')->setValue('=SUBSTITUTE()');
        } elseif ($oldText === 'omitted') {
            $this->setCell('A1', $text);
            $sheet->getCell('B1')->setValue('=SUBSTITUTE(A1)');
        } elseif ($newText === 'omitted') {
            $this->setCell('A1', $text);
            $this->setCell('A2', $oldText);
            $sheet->getCell('B1')->setValue('=SUBSTITUTE(A1, A2)');
        } elseif ($instance === 'omitted') {
            $this->setCell('A1', $text);
            $this->setCell('A2', $oldText);
            $this->setCell('A3', $newText);
            $sheet->getCell('B1')->setValue('=SUBSTITUTE(A1, A2, A3)');
        } else {
            $this->setCell('A1', $text);
            $this->setCell('A2', $oldText);
            $this->setCell('A3', $newText);
            $this->setCell('A4', $instance);
            $sheet->getCell('B1')->setValue('=SUBSTITUTE(A1, A2, A3, A4)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerSUBSTITUTE(): array
    {
        return require 'tests/data/Calculation/TextData/SUBSTITUTE.php';
    }
}
