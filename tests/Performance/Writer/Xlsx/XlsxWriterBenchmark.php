<?php

namespace PhpOffice\PhpSpreadsheetPerformance\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheetPerformance\Writer\AbstractBasicWriter;

class XlsxWriterBenchmark extends AbstractBasicWriter
{
    /**
     * @revs(5)
     * @Iterations(5)
     * @OutputTimeUnit("milliseconds")
     * @AfterMethods("tearDown")
     */
    public function benchmarkBasicXlsxWriter(): void
    {
        $writer = new Xlsx($this->spreadsheet);
        $writer->save($this->fileName);
    }
}
