<?php

namespace PhpOffice\PhpSpreadsheetPerformance\Writer\Ods;

use PhpOffice\PhpSpreadsheet\Writer\Ods;
use PhpOffice\PhpSpreadsheetPerformance\Writer\AbstractBasicWriter;

class OdsWriterBenchmark extends AbstractBasicWriter
{
    /**
     * @revs(5)
     * @Iterations(5)
     * @OutputTimeUnit("milliseconds")
     * @AfterMethods("tearDown")
     */
    public function benchmarkBasicOdsWriter(): void
    {
        $writer = new Ods($this->spreadsheet);
        $writer->save($this->fileName);
    }
}
