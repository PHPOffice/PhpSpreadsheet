<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class Issue4696Test extends TestCase
{
    private ?Spreadsheet $spreadsheet = null;

    protected function tearDown(): void
    {
        if ($this->spreadsheet !== null) {
            $this->spreadsheet->disconnectWorksheets();
            $this->spreadsheet = null;
        }
    }

    #[DataProvider('providerIsDateTime')]
    public function testTimeOnly(bool $expectedResult, string $expectedFormatted, int|float|string $value): void
    {
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        if (is_string($value) && $value[0] !== '=') {
            $sheet->getCell('A1')->setValueExplicit($value, DataType::TYPE_STRING);
        } else {
            $sheet->getCell('A1')->setValue($value);
        }
        $sheet->getStyle('A1')->getNumberFormat()
            ->setFormatCode('yyyy-mm-dd');
        self::assertSame(
            $expectedResult,
            Date::isDateTime($sheet->getCell('A1'))
        );
        self::assertSame(
            $expectedFormatted,
            $sheet->getCell('A1')->getFormattedValue()
        );
    }

    public static function providerIsDateTime(): array
    {
        return [
            'valid integer' => [true, '1903-12-31', 1461],
            'valid integer stored as string' => [true, '1904-01-01', '1462'],
            'valid integer stored as concatenated string' => [true, '1904-01-01', '="14"&"62"'],
            'valid float' => [true, '1903-12-31', 1461.5],
            'valid float stored as string' => [true, '1903-12-31', '1461.5'],
            'out-of-range integer' => [false, '7000989091802000122', 7000989091802000122],
            'out-of-range float' => [false, '7.000989091802E+18', 7000989091802000122.1],
            'out-of-range float stored as string' => [false, '7000989091802000122.1', '7000989091802000122.1'],
            'non-numeric' => [false, 'xyz', 'xyz'],
            'issue 917' => [false, '5e8630b8-603c-43fe-b038-6154a3f893ab', '5e8630b8-603c-43fe-b038-6154a3f893ab'],
        ];
    }
}
