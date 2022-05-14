<?php

namespace PhpOffice\PhpSpreadsheetPerformance\Writer\Xls;

use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheetPerformance\Writer\AbstractBasicWriter;

class XlxWriterBenchmark extends AbstractBasicWriter
{
    /**
     * @var string
     */
    protected $fileName = 'performanceTestWrite.xls';

    /**
     * @revs(5)
     * @Iterations(5)
     * @OutputTimeUnit("milliseconds")
     * @AfterMethods("tearDown")
     */
    public function benchmarkBasicXlsWriter(): void
    {
        $writer = new Xls($this->spreadsheet);
        $writer->save($this->fileName);
    }
}
