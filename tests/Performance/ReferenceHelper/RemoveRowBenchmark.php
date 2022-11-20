<?php

namespace PhpOffice\PhpSpreadsheetPerformance\ReferenceHelper;

class RemoveRowBenchmark extends AbstractInsertDelete
{
    public function __construct()
    {
        parent::__construct(256, 512);
    }

    /**
     * @Groups({"slow"})
     * @revs(4)
     * @Iterations(4)
     * @OutputTimeUnit("milliseconds")
     * @AfterMethods("tearDown")
     */
    public function benchmarkRemoveRowsEarly(): void
    {
        $this->spreadsheet->getActiveSheet()->removeRow(2, 4);
    }

    /**
     * @Groups({"slow"})
     * @revs(4)
     * @Iterations(4)
     * @OutputTimeUnit("milliseconds")
     * @AfterMethods("tearDown")
     */
    public function benchmarkRemoveRowsMid(): void
    {
        $this->spreadsheet->getActiveSheet()->removeRow(255, 4);
    }

    /**
     * @Groups({"slow"})
     * @revs(4)
     * @Iterations(7)
     * @OutputTimeUnit("milliseconds")
     * @AfterMethods("tearDown")
     */
    public function benchmarkRemoveRowsLate(): void
    {
        $this->spreadsheet->getActiveSheet()->removeRow(510, 4);
    }
}
