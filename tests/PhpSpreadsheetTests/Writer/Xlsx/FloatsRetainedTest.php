<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as Reader;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as Writer;
use PHPUnit\Framework\TestCase;

class FloatsRetainedTest extends TestCase
{
    /**
     * @dataProvider providerIntyFloatsRetainedByWriter
     *
     * @param float|int $value
     */
    public function testIntyFloatsRetainedByWriter($value): void
    {
        $outputFilename = File::temporaryFilename();
        Settings::setLibXmlLoaderOptions(null);
        $sheet = new Spreadsheet();
        $sheet->getActiveSheet()->getCell('A1')->setValue($value);

        $writer = new Writer($sheet);
        $writer->save($outputFilename);

        $reader = new Reader();
        $sheet = $reader->load($outputFilename);
        unlink($outputFilename);

        self::assertSame($value, $sheet->getActiveSheet()->getCell('A1')->getValue());
    }

    public function providerIntyFloatsRetainedByWriter(): array
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
