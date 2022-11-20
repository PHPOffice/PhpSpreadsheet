<?php

namespace PhpOffice\PhpSpreadsheetPerformance\ReferenceHelper;

class InsertColumnBenchmark extends AbstractInsertDelete
{
    public function __construct()
    {
        parent::__construct(78, 2048);
    }

    /**
     * @Groups({"slow"})
     * @revs(4)
     * @Iterations(4)
     * @OutputTimeUnit("milliseconds")
     * @AfterMethods("tearDown")
     */
    public function benchmarkInsertColumnsEarly(): void
    {
        $this->spreadsheet->getActiveSheet()->insertNewColumnBefore('C', 2);
    }

    /**
     * @Groups({"slow"})
     * @revs(4)
     * @Iterations(4)
     * @OutputTimeUnit("milliseconds")
     * @AfterMethods("tearDown")
     */
    public function benchmarkInsertColumnsMid(): void
    {
        $this->spreadsheet->getActiveSheet()->insertNewColumnBefore('AB', 2);
    }

    /**
     * @Groups({"slow"})
     * @revs(4)
     * @Iterations(7)
     * @OutputTimeUnit("milliseconds")
     * @AfterMethods("tearDown")
     */
    public function benchmarkInsertColumnsLate(): void
    {
        $this->spreadsheet->getActiveSheet()->insertNewColumnBefore('BX', 2);
    }
}
