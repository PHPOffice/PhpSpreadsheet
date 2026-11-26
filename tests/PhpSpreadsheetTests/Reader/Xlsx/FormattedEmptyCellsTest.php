<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Namespaces;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;
use ZipArchive;

class FormattedEmptyCellsTest extends TestCase
{
    private const ROWS = '<row r="1"><c r="A1" s="1"><v>1</v></c><c r="B1" s="1"/></row>'
        . '<row r="2" ht="30" customHeight="1"><c r="A2" s="1"/><c r="B2" s="1" t="n"/></row>';

    private string $tempfile = '';

    private ?Spreadsheet $spreadsheet = null;

    protected function tearDown(): void
    {
        $this->spreadsheet?->disconnectWorksheets();
        $this->spreadsheet = null;
        if ($this->tempfile !== '') {
            unlink($this->tempfile);
            $this->tempfile = '';
        }
    }

    public function testFormattedEmptyCellsAreNotLoaded(): void
    {
        $sheet = $this->load(self::ROWS, IReader::IGNORE_EMPTY_CELLS);

        self::assertSame(['A1'], $sheet->getCellCollection()->getCoordinates());
        self::assertSame(1, $sheet->getCell('A1')->getValue());
        self::assertSame(1, $sheet->getCell('A1')->getXfIndex());
        self::assertSame(30.0, $sheet->getRowDimension(2)->getRowHeight());
    }

    public function testFormattedEmptyCellsAreLoadedByDefault(): void
    {
        $sheet = $this->load(self::ROWS);

        self::assertSame(['A1', 'B1', 'A2', 'B2'], $sheet->getCellCollection()->getCoordinates());
        self::assertSame(1, $sheet->getCell('B2')->getXfIndex());
    }

    public function testCellWithoutReferenceKeepsItsColumn(): void
    {
        $sheet = $this->load('<row r="1"><c r="A1" s="1"/><c><v>5</v></c></row>', IReader::IGNORE_EMPTY_CELLS);

        self::assertSame(['B1'], $sheet->getCellCollection()->getCoordinates());
        self::assertSame(5, $sheet->getCell('B1')->getValue());
    }

    public function testEmptyInlineStringIsStillLoaded(): void
    {
        $sheet = $this->load('<row r="1"><c r="A1" s="1"/><c r="B1" t="inlineStr"/></row>', IReader::IGNORE_EMPTY_CELLS);

        self::assertSame(['B1'], $sheet->getCellCollection()->getCoordinates());
    }

    public function testRowWithOnlyEmptyCellsKeepsItsAttributesWhenIgnoringRowsWithNoCells(): void
    {
        $sheet = $this->load(self::ROWS, IReader::IGNORE_EMPTY_CELLS | IReader::IGNORE_ROWS_WITH_NO_CELLS);

        self::assertSame(30.0, $sheet->getRowDimension(2)->getRowHeight());
    }

    public function testDoctypeSplitByEmptyCellIsRejected(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Detected use of ENTITY');

        $this->load(self::ROWS, IReader::IGNORE_EMPTY_CELLS, '<!DOC<c r="A1"/>TYPE worksheet [<!ENT<c r="A1"/>ITY e "e">]>');
    }

    private function load(string $sheetData, int $flags = 0, string $doctype = ''): Worksheet
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $this->tempfile = File::temporaryFilename();
        (new XlsxWriter($spreadsheet))->save($this->tempfile);
        $spreadsheet->disconnectWorksheets();

        $zip = new ZipArchive();
        $zip->open($this->tempfile);
        $zip->addFromString(
            'xl/worksheets/sheet1.xml',
            $doctype . '<worksheet xmlns="' . Namespaces::MAIN . '"><sheetData>' . $sheetData . '</sheetData></worksheet>'
        );
        $zip->close();

        $this->spreadsheet = (new XlsxReader())->load($this->tempfile, $flags);

        return $this->spreadsheet->getActiveSheet();
    }
}
