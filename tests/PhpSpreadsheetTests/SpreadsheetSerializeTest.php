<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class SpreadsheetSerializeTest extends TestCase
{
    private ?Spreadsheet $spreadsheet = null;

    protected function tearDown(): void
    {
        if ($this->spreadsheet !== null) {
            $this->spreadsheet->disconnectWorksheets();
            $this->spreadsheet = null;
        }
    }

    public function testSerialize(): void
    {
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue(10);

        $serialized = serialize($this->spreadsheet);
        $newSpreadsheet = unserialize($serialized);
        self::assertInstanceOf(Spreadsheet::class, $newSpreadsheet);
        self::assertNotSame($this->spreadsheet, $newSpreadsheet);
        $newSheet = $newSpreadsheet->getActiveSheet();
        self::assertSame(10, $newSheet->getCell('A1')->getValue());
        $newSpreadsheet->disconnectWorksheets();
    }

    public function testNotJsonEncodable(): void
    {
        $this->spreadsheet = new Spreadsheet();

        $this->expectException(SpreadsheetException::class);
        $this->expectExceptionMessage('Spreadsheet objects cannot be json encoded');
        json_encode($this->spreadsheet);
    }

    /**
     * These tests are a bit weird.
     * If prepareSerialize and readSerialize are run in the same
     *    process, the latter's assertions will always succeed.
     *    So to demonstrate that the
     *    problem is solved, they need to run in separate processes.
     *    But then they can't share the file name. So we need to send
     *    the file to a semi-hard-coded destination.
     */
    private static function getTempFileName(): string
    {
        $helper = new Sample();

        return $helper->getTemporaryFolder() . '/spreadsheet.serialize.test.txt';
    }

    public function testPrepareSerialize(): void
    {
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $this->spreadsheet->addNamedRange(new NamedRange('summedcells', $sheet, '$A$1:$A$5'));
        $sheet->setCellValue('A1', 1);
        $sheet->setCellValue('A2', 2);
        $sheet->setCellValue('A3', 3);
        $sheet->setCellValue('A4', 4);
        $sheet->setCellValue('A5', 5);
        $sheet->setCellValue('C1', '=SUM(summedcells)');
        $ser = serialize($this->spreadsheet);
        $this->spreadsheet->disconnectWorksheets();
        $outputFileName = self::getTempFileName();
        self::assertNotFalse(
            file_put_contents($outputFileName, $ser)
        );

        $inputFileName = self::getTempFileName();
        $ser = (string) file_get_contents($inputFileName);
        unlink($inputFileName);
        $spreadsheet = unserialize($ser);
        self::assertInstanceOf(Spreadsheet::class, $spreadsheet);
        $this->spreadsheet = $spreadsheet;
        $sheet = $this->spreadsheet->getActiveSheet();
        self::assertSame('=SUM(summedcells)', $sheet->getCell('C1')->getValue());
        self::assertSame(15, $sheet->getCell('C1')->getCalculatedValue());
    }
}
