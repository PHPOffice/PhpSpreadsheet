<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\Ods;
use PHPUnit\Framework\TestCase;

class LongStringTest extends TestCase
{
    public function testLongString(): void
    {
        $infile = 'tests/data/Reader/Ods/long.string';
        $reader = new Ods();
        $spreadsheet = $reader->load($infile);
        $sheet = $spreadsheet->getActiveSheet();
        $value = $sheet->getCell('A1')->getValue();
        self::assertIsString($value);
        self::assertSame(DataType::MAX_STRING_LENGTH, mb_strlen($value));
        $spreadsheet->disconnectWorksheets();
    }
}
