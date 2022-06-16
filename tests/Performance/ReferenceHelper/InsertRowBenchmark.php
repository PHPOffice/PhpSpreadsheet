<?php

namespace PhpOffice\PhpSpreadsheetPerformance\ReferenceHelper;

class InsertRowBenchmark extends AbstractInsertDelete
{
    public function __construct()
    {
        parent::__construct(256, 512);
    }

    /**
     * @Groups({"slow"})
     * @revs(4)
     * @Iterations(5)
     * @OutputTimeUnit("milliseconds")
     * @AfterMethods("tearDown")
     */
    public function benchmarkInsertRowsEarly(): void
    {
        $this->spreadsheet->getActiveSheet()->insertNewRowBefore(2, 4);
    }

    /**
     * @Groups({"slow"})
     * @revs(4)
     * @Iterations(7)
     * @OutputTimeUnit("milliseconds")
     * @AfterMethods("tearDown")
     */
    public function benchmarkInsertRowsMid(): void
    {
        $this->spreadsheet->getActiveSheet()->insertNewRowBefore(255, 4);
    }

    /**
     * @Groups({"slow"})
     * @revs(4)
     * @Iterations(11)
     * @OutputTimeUnit("milliseconds")
     * @AfterMethods("tearDown")
     */
    public function benchmarkInsertRowsLate(): void
    {
        $this->spreadsheet->getActiveSheet()->insertNewRowBefore(510, 4);
    }
}
