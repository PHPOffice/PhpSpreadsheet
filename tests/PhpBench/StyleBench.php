<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetPhpBench;

use PhpBench\Attributes as Bench;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

#[Bench\BeforeMethods('setUp')]
#[Bench\ParamProviders('provideDefinedNames')]
#[Bench\Groups(['style'])]
class StyleBench
{
    private Spreadsheet $spreadsheet;

    /**
     * @param array{names: int} $params
     */
    public function setUp(array $params): void
    {
        $this->spreadsheet = SheetFactory::create(1000, 10, definedNames: $params['names']);
    }

    /**
     * @return array<string, array{names: int}>
     */
    public function provideDefinedNames(): array
    {
        return [
            'no names' => ['names' => 0],
            '200 names' => ['names' => 200],
        ];
    }

    /**
     * @param array{names: int} $params
     */
    public function benchGetStylePerCell(array $params): void
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        for ($row = 1; $row <= 1000; ++$row) {
            $sheet->getStyle("A{$row}")->getFont()->setBold(true);
            $sheet->getStyle("B{$row}")->getNumberFormat()->setFormatCode('0.00');
        }
    }

    /**
     * @param array{names: int} $params
     */
    public function benchApplyFromArrayToRange(array $params): void
    {
        $this->spreadsheet->getActiveSheet()->getStyle('A1:J1000')->applyFromArray([
            'font' => ['italic' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFF00']],
        ]);
    }

    /**
     * @param array{names: int} $params
     */
    public function benchAutoSizeColumns(array $params): void
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        foreach (range('A', 'J') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        $sheet->calculateColumnWidths();
    }

    /**
     * @param array{names: int} $params
     */
    public function benchFormattedValue(array $params): void
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->getStyle('A1:J1000')->getNumberFormat()->setFormatCode('#,##0.00');
        for ($row = 1; $row <= 1000; ++$row) {
            $sheet->getCell("A{$row}")->getFormattedValue();
            $sheet->getCell("B{$row}")->getFormattedValue();
        }
    }
}
