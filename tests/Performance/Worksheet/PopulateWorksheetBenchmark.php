<?php

namespace PhpOffice\PhpSpreadsheetPerformance\Worksheet;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PopulateWorksheetBenchmark
{
    /**
     * @var int
     */
    protected $columns;

    /**
     * @var int
     */
    protected $rows;

    /**
     * @var string
     */
    protected $columnMaxAddress;

    /**
     * @var Spreadsheet
     */
    protected $spreadsheet;

    /**
     * @var int[]
     */
    protected $sampleDataInteger;

    /**
     * @var string[]
     */
    protected $sampleDataString;

    /**
     * @var Worksheet
     */
    protected $worksheet;

    public function setUp(): void
    {
        $this->columns = 64;
        $this->rows = 1024;
        $this->columnMaxAddress = Coordinate::stringFromColumnIndex($this->columns);
        ++$this->columnMaxAddress;

        $this->spreadsheet = new Spreadsheet();
        $this->worksheet = $this->spreadsheet->getActiveSheet();

        $this->sampleDataInteger = range(1, $this->columns);
        $this->sampleDataString = array_fill(0, $this->columns, 'Hello World');
    }

    /**
     * @revs(5)
     * @Iterations(5)
     * @OutputTimeUnit("milliseconds")
     * @BeforeMethods("setUp")
     */
    public function benchmarkPopulateIntegerValuesByCell(): void
    {
        for ($row = 1; $row <= $this->rows; ++$row) {
            for ($column = 'A'; $column !== $this->columnMaxAddress; ++$column) {
                $this->worksheet->setCellValue("{$column}{$row}", 1);
            }
        }
    }

    /**
     * @revs(5)
     * @Iterations(5)
     * @OutputTimeUnit("milliseconds")
     * @BeforeMethods("setUp")
     */
    public function benchmarkPopulateStringValuesByCell(): void
    {
        for ($row = 1; $row <= $this->rows; ++$row) {
            for ($column = 'A'; $column !== $this->columnMaxAddress; ++$column) {
                $this->worksheet->setCellValue("{$column}{$row}", 'Hello World');
            }
        }
    }

    /**
     * @revs(5)
     * @Iterations(5)
     * @OutputTimeUnit("milliseconds")
     * @BeforeMethods("setUp")
     */
    public function benchmarkPopulateIntegerValuesFromArray(): void
    {
        for ($row = 1; $row <= $this->rows; ++$row) {
            $this->worksheet->fromArray($this->sampleDataInteger, null, "A{$row}", true);
        }
    }

    /**
     * @revs(5)
     * @Iterations(5)
     * @OutputTimeUnit("milliseconds")
     * @BeforeMethods("setUp")
     */
    public function benchmarkPopulateStringValuesFromArray(): void
    {
        for ($row = 1; $row <= $this->rows; ++$row) {
            $this->worksheet->fromArray($this->sampleDataString, null, "A{$row}", true);
        }
    }
}
