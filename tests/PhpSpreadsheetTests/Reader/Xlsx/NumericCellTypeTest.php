<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\IValueBinder;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as Reader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as Writer;
use PHPUnit\Framework\TestCase;

class NumericCellTypeTest extends TestCase
{
    private IValueBinder $oldBinder;

    private string $filename = '';

    protected function setUp(): void
    {
        $this->oldBinder = Cell::getValueBinder();

        $binder = new class () implements IValueBinder {
            public function bindValue(Cell $cell, mixed $value): bool
            {
                if (is_float($value) || is_int($value)) {
                    $type = DataType::TYPE_NUMERIC;
                } elseif (is_string($value)) {
                    $type = DataType::TYPE_STRING;
                } else {
                    return false;
                }

                $cell->setValueExplicit($value, $type);

                return true;
            }
        };

        Cell::setValueBinder($binder);
    }

    protected function tearDown(): void
    {
        Cell::setValueBinder($this->oldBinder);
        if ($this->filename !== '') {
            unlink($this->filename);
            $this->filename = '';
        }
    }

    public function testCellShouldHaveNumericTypeAttribute(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $array = [
            ['1.0'],
            [1.0],
            ['-1.0'],
            [-1.0],
            ['0'],
            [0],
            ['0.0'],
            [0.0],
            ['1e1'],
            [1e1],
        ];
        $sheet->fromArray($array, null, 'A1', true);

        $this->filename = File::temporaryFilename();
        $writer = new Writer($spreadsheet);
        $writer->save($this->filename);
        $spreadsheet->disconnectWorksheets();

        $reader = new Reader();
        $spreadsheet2 = $reader->load($this->filename);

        $sheet2 = $spreadsheet2->getActiveSheet();
        self::assertSame($array, $sheet2->toArray(null, false, false));
        $spreadsheet2->disconnectWorksheets();
    }
}
