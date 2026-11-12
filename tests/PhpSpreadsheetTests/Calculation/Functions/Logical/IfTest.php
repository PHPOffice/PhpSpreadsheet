<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

class IfTest extends AllSetupTeardown
{
    #[\PHPUnit\Framework\Attributes\DataProvider('providerIF')]
    public function testIF(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('IF', $expectedResult, ...$args);
    }

    public static function providerIF(): array
    {
        return require 'tests/data/Calculation/Logical/IF.php';
    }

    /** @return array<string, array{mixed, string}> */
    public static function providerArrayCondition(): array
    {
        return [
            'SUM of IF' => [15, '=SUM(IF(A1:A4>3,A1:A4,0))'],
            'COUNT of IF with empty string' => [2, '=COUNT(IF(A1:A4>3,A1:A4,""))'],
            'SUMPRODUCT of IF' => [15, '=SUMPRODUCT(IF(A1:A4>3,A1:A4,0))'],
            'SUM of IFS' => [15, '=SUM(IFS(A1:A4>3,A1:A4,TRUE,0))'],
            'IF returns array' => [[[0], [1], [1], [0]], '=IF(A1:A4>3,1,0)'],
            'IFS returns array' => [[[0], [1], [1], [0]], '=IFS(A1:A4>3,1,TRUE,0)'],
            'IFS without match' => [[['#N/A'], [5], [10], ['#N/A']], '=IFS(A1:A4>3,A1:A4)'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('providerArrayCondition')]
    public function testArrayCondition(mixed $expected, string $formula): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $spreadsheet->returnArrayAsArray();
        $sheet = $this->getSheet();
        $sheet->fromArray([[1], [5], [10], [2]], null, 'A1');
        $sheet->getCell('C1')->setValue($formula);
        $result = $sheet->getCell('C1')->getCalculatedValue();
        self::assertSame($expected, $result);
    }
}
