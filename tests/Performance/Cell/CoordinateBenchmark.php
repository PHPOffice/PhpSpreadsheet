<?php

namespace PhpOffice\PhpSpreadsheetPerformance\Cell;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class CoordinateBenchmark
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

    public function setUp(): void
    {
        $this->columns = 16384; // Xlsx Max Column Index
        $this->rows = 16;
        $this->columnMaxAddress = Coordinate::stringFromColumnIndex($this->columns);
        ++$this->columnMaxAddress;
    }

    /**
     * @Warmup(2)
     * @revs(10)
     * @Iterations(20)
     * @OutputTimeUnit("milliseconds")
     * @BeforeMethods("setUp")
     */
    public function benchmarkColumnIndexFromString(): void
    {
        for ($column = 'A'; $column !== $this->columnMaxAddress; ++$column) {
            $index = Coordinate::columnIndexFromString($column);
        }
    }

    /**
     * @Warmup(2)
     * @revs(10)
     * @Iterations(20)
     * @OutputTimeUnit("milliseconds")
     * @BeforeMethods("setUp")
     */
    public function benchmarkStringFromColumnIndex(): void
    {
        for ($column = 1; $column <= $this->columns; ++$column) {
            $index = Coordinate::stringFromColumnIndex($column);
        }
    }

    /**
     * @Warmup(2)
     * @revs(5)
     * @Iterations(10)
     * @OutputTimeUnit("milliseconds")
     * @BeforeMethods("setUp")
     */
    public function benchmarkCoordinateFromString(): void
    {
        for ($column = 'A'; $column !== $this->columnMaxAddress; ++$column) {
            for ($row = 1; $row <= $this->rows; ++$row) {
                $coordinateArray = Coordinate::coordinateFromString("{$column}{$row}");
            }
        }
    }

    /**
     * @Warmup(2)
     * @revs(5)
     * @Iterations(10)
     * @OutputTimeUnit("milliseconds")
     * @BeforeMethods("setUp")
     */
    public function benchmarkIndexesFromString(): void
    {
        for ($column = 'A'; $column !== $this->columnMaxAddress; ++$column) {
            for ($row = 1; $row <= $this->rows; ++$row) {
                $coordinateArray = Coordinate::indexesFromString("{$column}{$row}");
            }
        }
    }
}
