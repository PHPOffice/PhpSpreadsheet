<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class DataType2Test extends TestCase
{
    public function testSetDataType(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue(28.1);
        self::assertSame(28.1, $sheet->getCell('A1')->getValue());
        self::assertSame('28.1', (string) $sheet->getCell('A1'));
        $sheet->getCell('A1')->setDataType(DataType::TYPE_STRING);
        self::assertSame('28.1', $sheet->getCell('A1')->getValue());
        $sheet->getCell('A1')->setDataType(DataType::TYPE_NUMERIC);
        self::assertSame(28.1, $sheet->getCell('A1')->getValue());
        $sheet->getCell('A1')->setDataType(DataType::TYPE_STRING2);
        self::assertSame('28.1', $sheet->getCell('A1')->getValue());
        $sheet->getCell('A1')->setDataType(DataType::TYPE_INLINE);
        self::assertSame('28.1', $sheet->getCell('A1')->getValue());
        $sheet->getCell('A1')->setDataType(DataType::TYPE_BOOL);
        self::assertTrue($sheet->getCell('A1')->getValue());
        $sheet->getCell('A1')->setDataType(DataType::TYPE_NUMERIC);
        self::assertSame(1, $sheet->getCell('A1')->getValue());

        $sheet->getCell('A2')->setValue('X');

        try {
            $sheet->getCell('A2')->setDataType(DataType::TYPE_NUMERIC);
        } catch (PhpSpreadsheetException $e) {
            self::assertSame('Invalid numeric value for datatype Numeric', $e->getMessage());
        }

        $spreadsheet->disconnectWorksheets();
    }
}
