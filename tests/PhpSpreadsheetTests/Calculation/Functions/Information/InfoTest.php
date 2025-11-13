<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Information;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class InfoTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('providerINFO')]
    public function testINFO(mixed $expectedResult, string $typeText): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getCell('A1')->setValue('=INFO("' . $typeText . '")');
        $result = $sheet->getCell('A1')->getCalculatedValue();

        self::assertEquals($expectedResult, $result);
    }

    public static function providerINFO(): array
    {
        return require 'tests/data/Calculation/Information/INFO.php';
    }

    public function testINFONumfileWithThreeSheets(): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->createSheet();
        $spreadsheet->createSheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getCell('A1')->setValue('=INFO("numfile")');
        $result = $sheet->getCell('A1')->getCalculatedValue();

        self::assertEquals(3, $result);
    }
}
