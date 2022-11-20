<?php

namespace PhpOffice\PhpSpreadsheetPerformance\ReferenceHelper;

class RemoveColumnBenchmark extends AbstractInsertDelete
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
    public function benchmarkRemoveColumnsEarly(): void
    {
        $this->spreadsheet->getActiveSheet()->removeColumn('C', 2);
    }

    /**
     * @Groups({"slow"})
     * @revs(4)
     * @Iterations(4)
     * @OutputTimeUnit("milliseconds")
     * @AfterMethods("tearDown")
     */
    public function benchmarkRemoveColumnsMid(): void
    {
        $this->spreadsheet->getActiveSheet()->removeColumn('AB', 2);
    }

    /**
     * @Groups({"slow"})
     * @revs(4)
     * @Iterations(7)
     * @OutputTimeUnit("milliseconds")
     * @AfterMethods("tearDown")
     */
    public function benchmarkRemoveColumnsLate(): void
    {
        $this->spreadsheet->getActiveSheet()->removeColumn('BX', 2);
    }
}
