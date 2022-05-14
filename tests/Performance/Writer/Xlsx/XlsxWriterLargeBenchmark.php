<?php

namespace PhpOffice\PhpSpreadsheetPerformance\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheetPerformance\Writer\AbstractBasicWriter;

class XlsxWriterLargeBenchmark extends AbstractBasicWriter
{
    public function __construct()
    {
        parent::__construct(128, 8192);
    }

    /**
     * @var string
     */
    protected $fileName = 'performanceTestWrite.xlsx';

    /**
     * @Groups({"slow"})
     * @revs(3)
     * @Iterations(5)
     * @OutputTimeUnit("seconds")
     * @AfterMethods("tearDown")
     */
    public function benchmarkLargeXlsxWriter(): void
    {
        $writer = new Xlsx($this->spreadsheet);
        $writer->save($this->fileName);
    }
}
