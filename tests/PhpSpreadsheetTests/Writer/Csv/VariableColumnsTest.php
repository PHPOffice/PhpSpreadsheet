<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Csv;

use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PHPUnit\Framework\TestCase;

class VariableColumnsTest extends TestCase
{
    public function testVariableColumns(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(
            [
                [1, 2, 3, 4],
                [1, 2],
                [1, 2, 3, 4, 5],
                [],
                [1],
                [1, 2, 3],
            ]
        );

        $filename = File::temporaryFilename();
        $writer = new Csv($spreadsheet);
        $writer->setVariableColumns(true);
        $writer->save($filename);

        $contents = (string) file_get_contents($filename);
        unlink($filename);
        $spreadsheet->disconnectWorksheets();

        $rows = explode(PHP_EOL, $contents);

        self::assertSame('"1","2","3","4"', $rows[0]);
        self::assertSame('"1","2"', $rows[1]);
        self::assertSame('"1","2","3","4","5"', $rows[2]);
        self::assertSame('', $rows[3]);
        self::assertSame('"1"', $rows[4]);
        self::assertSame('"1","2","3"', $rows[5]);
    }

    public function testFixedColumns(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(
            [
                [1, 2, 3, 4],
                [1, 2],
                [1, 2, 3, 4, 5],
                [],
                [1],
                [1, 2, 3],
            ]
        );

        $filename = File::temporaryFilename();
        $writer = new Csv($spreadsheet);
        self::assertFalse($writer->getVariableColumns());
        $writer->save($filename);

        $contents = (string) file_get_contents($filename);
        unlink($filename);
        $spreadsheet->disconnectWorksheets();

        $rows = explode(PHP_EOL, $contents);

        self::assertSame('"1","2","3","4",""', $rows[0]);
        self::assertSame('"1","2","","",""', $rows[1]);
        self::assertSame('"1","2","3","4","5"', $rows[2]);
        self::assertSame('"","","","",""', $rows[3]);
        self::assertSame('"1","","","",""', $rows[4]);
        self::assertSame('"1","2","3","",""', $rows[5]);
    }
}
