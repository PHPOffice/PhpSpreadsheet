<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class SubstituteTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSUBSTITUTE
     */
    public function testSUBSTITUTE(mixed $expectedResult, mixed $text = 'omitted', mixed $oldText = 'omitted', mixed $newText = 'omitted', mixed $instance = 'omitted'): void
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

    public static function providerSUBSTITUTE(): array
    {
        return require 'tests/data/Calculation/TextData/SUBSTITUTE.php';
    }

    /**
     * @dataProvider providerSubstituteArray
     */
    public function testSubstituteArray(array $expectedResult, string $oldText, string $fromText, string $toText): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=SUBSTITUTE({$oldText}, {$fromText}, {$toText})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerSubstituteArray(): array
    {
        return [
            'row vector' => [[['ElePHPant', 'EleFFant']], '"Elephant"', '"ph"', '{"PHP", "FF"}'],
        ];
    }
}
