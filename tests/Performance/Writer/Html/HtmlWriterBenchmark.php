<?php

namespace PhpOffice\PhpSpreadsheetPerformance\Writer\Html;

use PhpOffice\PhpSpreadsheet\Writer\Html;
use PhpOffice\PhpSpreadsheetPerformance\Writer\AbstractBasicWriter;

class HtmlWriterBenchmark extends AbstractBasicWriter
{
    /**
     * @var string
     */
    protected $fileName = 'performanceTestWrite.html';

    /**
     * @revs(5)
     * @Iterations(5)
     * @OutputTimeUnit("milliseconds")
     * @AfterMethods("tearDown")
     */
    public function benchmarkBasicHtmlWriter(): void
    {
        $writer = new Html($this->spreadsheet);
        $writer->save($this->fileName);
    }
}
