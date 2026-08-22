<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as Reader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as Writer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FloatsRetainedTest extends TestCase
{
    #[DataProvider('providerIntyFloatsRetainedByWriter')]
    public function testIntyFloatsRetainedByWriter(float|int $value, mixed $expected = null): void
    {
        if ($expected === null) {
            $expected = $value;
        }
        $outputFilename = File::temporaryFilename();
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()
            ->getCell('A1')->setValue($value);

        $writer = new Writer($spreadsheet);
        $writer->save($outputFilename);
        $spreadsheet->disconnectWorksheets();

        $reader = new Reader();
        $spreadsheet2 = $reader->load($outputFilename);
        unlink($outputFilename);

        self::assertSame(
            $expected,
            $spreadsheet2->getActiveSheet()
                ->getCell('A1')->getValue()
        );
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
            'int but too much precision for Excel' => [99_999_999_999_999_999, '99999999999999999'],
            [99_999_999_999_999_999.0],
            'int > PHP_INT_MAX so stored as float' => [999_999_999_999_999_999_999_999_999_999_999_999_999_999],
            [999_999_999_999_999_999_999_999_999_999_999_999_999_999.0],
        ];
    }
}
