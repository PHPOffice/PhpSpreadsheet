<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class BadParamExceptionTest extends TestCase
{
    public function testBadAddToXmlWorkbook(): void
    {
        $this->expectException(WriterException::class);
        $this->expectExceptionMessage('Invalid parameters passed.');
        $objWriter = new XMLWriter();
        $spreadsheet = new Spreadsheet();
        $writerXlsx = new XlsxWriter($spreadsheet);
        $writerXlsxWorkbook = new XlsxWriter\Workbook($writerXlsx);
        $reflectionMethod = new ReflectionMethod($writerXlsxWorkbook, 'writeSheet');
        $reflectionMethod->invokeArgs($writerXlsxWorkbook, [$objWriter, '']);
    }

    public function testBadAddToXmlRels(): void
    {
        $this->expectException(WriterException::class);
        $this->expectExceptionMessage('Invalid parameters passed.');
        $objWriter = new XMLWriter();
        $spreadsheet = new Spreadsheet();
        $writerXlsx = new XlsxWriter($spreadsheet);
        $writerXlsxRels = new XlsxWriter\Rels($writerXlsx);
        $reflectionMethod = new ReflectionMethod($writerXlsxRels, 'writeRelationship');
        $reflectionMethod->invokeArgs($writerXlsxRels, [$objWriter, '', '', '']);
    }
}
