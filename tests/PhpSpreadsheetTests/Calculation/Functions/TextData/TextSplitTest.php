<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TextSplitTest extends AllSetupTeardown
{
    private function setDelimiterArgument(array $argument, string $column): string
    {
        return '{' . $column . implode(',' . $column, range(1, count($argument))) . '}';
    }

    /**
     * @param array|string $argument
     */
    private function setDelimiterValues(Worksheet $worksheet, string $column, $argument): void
    {
        if (is_array($argument)) {
            foreach ($argument as $index => $value) {
                ++$index;
                $worksheet->getCell("{$column}{$index}")->setValue($value);
            }
        } else {
            $worksheet->getCell("{$column}1")->setValue($argument);
        }
    }

    /**
     * @dataProvider providerTEXTSPLIT
     */
    public function testTextSplit(array $expectedResult, array $arguments): void
    {
        $text = $arguments[0];
        $columnDelimiter = $arguments[1];
        $rowDelimiter = $arguments[2];

        $args = 'A1';
        $args .= (is_array($columnDelimiter)) ? ', ' . $this->setDelimiterArgument($columnDelimiter, 'B') : ', B1';
        $args .= (is_array($rowDelimiter)) ? ', ' . $this->setDelimiterArgument($rowDelimiter, 'C') : ', C1';
        $args .= (isset($arguments[3])) ? ", {$arguments[3]}" : ',';
        $args .= (isset($arguments[4])) ? ", {$arguments[4]}" : ',';
        $args .= (isset($arguments[5])) ? ", {$arguments[5]}" : ',';

        $worksheet = $this->getSheet();
        $worksheet->getCell('A1')->setValue($text);
        $this->setDelimiterValues($worksheet, 'B', $columnDelimiter);
        if (!empty($rowDelimiter)) {
            $this->setDelimiterValues($worksheet, 'C', $rowDelimiter);
        }
        $worksheet->getCell('H1')->setValue("=TEXTSPLIT({$args})");

        $result = Calculation::getInstance($this->getSpreadsheet())->calculateCellValue($worksheet->getCell('H1'));
        self::assertSame($expectedResult, $result);
    }

    public static function providerTEXTSPLIT(): array
    {
        return require 'tests/data/Calculation/TextData/TEXTSPLIT.php';
    }
}
