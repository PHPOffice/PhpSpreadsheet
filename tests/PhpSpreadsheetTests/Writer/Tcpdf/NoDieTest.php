<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Tcpdf;

use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\TcpdfNoDie;
use PHPUnit\Framework\Attributes;

class NoDieTest extends \PHPUnit\Framework\TestCase
{
    private Spreadsheet $spreadsheet;

    protected function setUp(): void
    {
        $this->spreadsheet = new Spreadsheet();
    }

    protected function tearDown(): void
    {
        unset($this->spreadsheet);
    }

    // Separate processes because of global defined names
    #[Attributes\RunInSeparateProcess]
    #[Attributes\PreserveGlobalState(false)]
    public function testExceptionRatherThanDie(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Could not include font definition file');
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'cell');
        $writer = new TcpdfNoDie($this->spreadsheet);
        $writer->setFont('xyz');
        $writer->save('php://memory');
    }
}
