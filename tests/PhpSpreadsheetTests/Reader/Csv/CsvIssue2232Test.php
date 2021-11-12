<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Csv;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\IValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PHPUnit\Framework\TestCase;

class CsvIssue2232Test extends TestCase
{
    /**
     * @var IValueBinder
     */
    private $valueBinder;

    protected function setUp(): void
    {
        $this->valueBinder = Cell::getValueBinder();
    }

    protected function tearDown(): void
    {
        Cell::setValueBinder($this->valueBinder);
    }

    /**
     * @dataProvider providerIssue2232
     *
     * @param mixed $b2Value
     * @param mixed $b3Value
     */
    public function testEncodings(bool $useStringBinder, ?bool $preserveBoolString, $b2Value, $b3Value): void
    {
        if ($useStringBinder) {
            $binder = new StringValueBinder();
            if (is_bool($preserveBoolString)) {
                $binder->setBooleanConversion($preserveBoolString);
            }
            Cell::setValueBinder($binder);
        }
        $reader = new Csv();
        $filename = 'tests/data/Reader/CSV/issue.2232.csv';
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame($b2Value, $sheet->getCell('B2')->getValue());
        self::assertSame($b3Value, $sheet->getCell('B3')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function providerIssue2232(): array
    {
        return [
            [false, false, false, true],
            [false, null, false, true],
            [false, true, false, true],
            [true, false, false, true],
            [true, null, 'FaLSe', 'tRUE'],
            [true, true, 'FaLSe', 'tRUE'],
        ];
    }
}
