<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\IValueBinder;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as Reader;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as Writer;
use PHPUnit\Framework\TestCase;

class NumericCellTypeTest extends TestCase
{
    protected $oldBinder;

    protected function setUp()
    {
        Settings::setLibXmlLoaderOptions(null);
        $this->oldBinder = Cell::getValueBinder();

        $binder = new class() implements IValueBinder {
            public function bindValue(Cell $cell, $value)
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

    protected function tearDown()
    {
        Cell::setValueBinder($this->oldBinder);
    }

    /**
     * @dataProvider providerCellShouldHaveNumericTypeAttribute
     *
     * @param float|int|string $value
     */
    public function testCellShouldHaveNumericTypeAttribute($value)
    {
        $outputFilename = tempnam(File::sysGetTempDir(), 'phpspreadsheet-test');

        $sheet = new Spreadsheet();
        $sheet->getActiveSheet()->getCell('A1')->setValue($value);

        $writer = new Writer($sheet);
        $writer->save($outputFilename);

        $reader = new Reader();
        $sheet = $reader->load($outputFilename);

        $this->assertSame($value, $sheet->getActiveSheet()->getCell('A1')->getValue());
    }

    public function providerCellShouldHaveNumericTypeAttribute()
    {
        return [
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
    }
}
