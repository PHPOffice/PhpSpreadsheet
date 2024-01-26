<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as Reader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as Writer;
use PHPUnit\Framework\TestCase;

class FloatsRetainedTest extends TestCase
{
    /**
     * @dataProvider providerIntyFloatsRetainedByWriter
     */
    public function testIntyFloatsRetainedByWriter(float|int $value): void
    {
        $outputFilename = File::temporaryFilename();
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->getCell('A1')->setValue($value);

        $writer = new Writer($spreadsheet);
        $writer->save($outputFilename);
        $spreadsheet->disconnectWorksheets();

        $reader = new Reader();
        $spreadsheet2 = $reader->load($outputFilename);
        unlink($outputFilename);

        self::assertSame($value, $spreadsheet2->getActiveSheet()->getCell('A1')->getValue());
        $spreadsheet2->disconnectWorksheets();
    }

    public static function providerIntyFloatsRetainedByWriter(): array
    {
        return [
            [-1.0],
            [-1],
            [0.0],
            [0],
            [1.0],
            [1],
            [1e-3],
            [1.3e-10],
            [1e10],
            [3.00000000000000000001],
            [99999999999999999],
            [99999999999999999.0],
            [999999999999999999999999999999999999999999],
            [999999999999999999999999999999999999999999.0],
        ];
    }
}
