<?php

namespace PhpOffice\PhpSpreadsheetPerformance\Collection;

class MaxRowColumnBenchmark extends AbstractCellCollection
{
    public function __construct()
    {
        parent::__construct(256, 512);
    }

    /**
     * @revs(10)
     * @Iterations(20)
     * @OutputTimeUnit("milliseconds")
     * @AfterMethods("tearDown")
     */
    public function benchmarkGetHighestRowNoArgument(): void
    {
        $this->spreadsheet->getActiveSheet()
            ->getCellCollection()->getHighestRow();
    }

    /**
     * @revs(10)
     * @Iterations(20)
     * @OutputTimeUnit("milliseconds")
     * @AfterMethods("tearDown")
     */
    public function benchmarkGetHighestRowWithArgument(): void
    {
        $this->spreadsheet->getActiveSheet()
            ->getCellCollection()->getHighestRow('EQ');
    }

    /**
     * @revs(10)
     * @Iterations(20)
     * @OutputTimeUnit("milliseconds")
     * @AfterMethods("tearDown")
     */
    public function benchmarkGetHighestColumnNoArgument(): void
    {
        $this->spreadsheet->getActiveSheet()
            ->getCellCollection()->getHighestColumn();
    }

    /**
     * @revs(10)
     * @Iterations(20)
     * @OutputTimeUnit("milliseconds")
     * @AfterMethods("tearDown")
     */
    public function benchmarkGetHighestColumnWithArgument(): void
    {
        $this->spreadsheet->getActiveSheet()
            ->getCellCollection()->getHighestColumn(256);
    }

    /**
     * @revs(10)
     * @Iterations(20)
     * @OutputTimeUnit("milliseconds")
     * @AfterMethods("tearDown")
     */
    public function benchmarkGetHighestRowAndColum(): void
    {
        $this->spreadsheet->getActiveSheet()
            ->getCellCollection()->getHighestRowAndColumn();
    }
}
