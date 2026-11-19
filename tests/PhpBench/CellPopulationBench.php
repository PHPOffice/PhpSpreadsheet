<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetPhpBench;

use PhpBench\Attributes as Bench;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

#[Bench\BeforeMethods('setUp')]
#[Bench\Groups(['population'])]
class CellPopulationBench
{
    private const ROWS = 500;

    private const COLS = 40;

    private Worksheet $sheet;

    /** @var list<list<float|int|string>> */
    private array $data;

    public function setUp(): void
    {
        $this->sheet = (new Spreadsheet())->getActiveSheet();
        $this->data = SheetFactory::data(self::ROWS, self::COLS);
    }

    public function benchSetValue(): void
    {
        foreach ($this->data as $rowIndex => $line) {
            foreach ($line as $colIndex => $value) {
                $this->sheet->getCell([$colIndex + 1, $rowIndex + 1])->setValue($value);
            }
        }
    }

    public function benchSetValueExplicit(): void
    {
        foreach ($this->data as $rowIndex => $line) {
            foreach ($line as $colIndex => $value) {
                $this->sheet->getCell([$colIndex + 1, $rowIndex + 1])->setValueExplicit((string) $value, DataType::TYPE_STRING);
            }
        }
    }

    public function benchFromArray(): void
    {
        $this->sheet->fromArray($this->data);
    }
}
