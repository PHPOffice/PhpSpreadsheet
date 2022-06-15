<?php

namespace PhpOffice\PhpSpreadsheetPerformance\ReferenceHelper;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

abstract class AbstractInsertDelete
{
    /**
     * @var Spreadsheet
     */
    protected $spreadsheet;

    /**
     * @var string
     */
    protected $fileName = 'php://temp';

    public function __construct(int $columns = 32, int $rows = 512)
    {
        $this->spreadsheet = new Spreadsheet();

        $worksheet = $this->spreadsheet->getActiveSheet();

        $this->buildWorksheet($worksheet, $rows, $columns);
    }

    private function buildWorksheet(Worksheet $worksheet, int $rows, int $columns): void
    {
        $sampleData = [range(1, $columns)];

        for ($row = 1; $row <= $rows; ++$row) {
            $worksheet->fromArray($sampleData, null, "A{$row}", true);
        }
    }

    public function tearDown(): void
    {
        if (file_exists($this->fileName)) {
            unlink($this->fileName);
        }
    }
}
