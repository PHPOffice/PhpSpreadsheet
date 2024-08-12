<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Issue1482Test extends \PHPUnit\Framework\TestCase
{
    private static string $testbook = 'tests/data/Reader/XLSX/issue.1482.xlsx';

    public function testPreliminaries(): void
    {
        $file = 'zip://';
        $file .= self::$testbook;
        $file .= '#xl/worksheets/sheet.xml';
        $data = file_get_contents($file);
        // confirm that file contains expected namespaced xml tag
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            self::assertStringContainsString('<x:drawing r:id="rId1" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" />', $data);
        }
    }

    public function testLoadXlsx(): void
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load(self::$testbook);
        $sheet = $spreadsheet->getSheet(0);
        $expectedColWidths = [
            0.95,
            7.45,
            0.6,
            4.4,
            5.91,
            3.92,
            7.71,
            2.12,
            2.56,
            6.94,
            9.6,
            1.82,
            3.01,
            8.63,
            10.37,
            2.09,
        ];
        $columnDimensions = $sheet->getColumnDimensions();
        $actualColWidths = [];
        foreach ($columnDimensions as $columnDimension) {
            $actualColWidths[] = $columnDimension->getWidth();
        }
        self::assertSame($expectedColWidths, $actualColWidths);
        $expectedRowHeights = [
            14.0,
            7.0,
            14.0,
            80.0,
            234.0,
            63.0,
            13.0,
            19.0,
            18.0,
            18.0,
            18.0,
            18.0,
            18.0,
            18.0,
            18.0,
            18.0,
        ];
        $rowHeights = $sheet->getRowDimensions();
        $actualRowHeights = [];
        foreach ($rowHeights as $rowDimension) {
            $actualRowHeights[] = $rowDimension->getRowHeight();
        }
        self::assertSame($expectedRowHeights, $actualRowHeights);
        $expectedMergeCells = [
            'B1:E1',
            'F1:G1',
            'J1:L1',
            'M1:N1',
            'B3:E3',
            'F3:G3',
            'B5:O5',
            'A7:C7',
            'D7:M7',
            'A8:B8',
            'C8:D8',
            'E8:F8',
            'G8:H8',
            'I8:J8',
            'L8:P8',
            'A9:B9',
            'C9:D9',
            'E9:F9',
            'G9:H9',
            'I9:J9',
            'L9:P9',
            'A10:B10',
            'C10:D10',
            'E10:F10',
            'G10:H10',
            'I10:J10',
            'L10:P10',
            'A11:B11',
            'C11:D11',
            'E11:F11',
            'G11:H11',
            'I11:J11',
            'L11:P11',
            'A12:B12',
            'C12:D12',
            'E12:F12',
            'G12:H12',
            'I12:J12',
            'L12:P12',
            'A13:B13',
            'C13:D13',
            'E13:F13',
            'G13:H13',
            'I13:J13',
            'L13:P13',
            'A14:B14',
            'C14:D14',
            'E14:F14',
            'G14:H14',
            'I14:J14',
            'L14:P14',
            'A15:B15',
            'C15:D15',
            'E15:F15',
            'G15:H15',
            'I15:J15',
            'L15:P15',
            'A16:B16',
            'C16:D16',
            'E16:F16',
            'G16:H16',
            'I16:J16',
            'L16:P16',
        ];
        self::assertSame($expectedMergeCells, array_keys($sheet->getMergeCells()));

        $drawingCollection = $sheet->getDrawingCollection();
        self::assertCount(1, $drawingCollection);
        $drawing = $drawingCollection[0];
        if ($drawing === null) {
            self::fail('Unexpected null instead of drawing');
        } else {
            self::assertSame('B5', $drawing->getCoordinates());
            self::assertSame('', $drawing->getCoordinates2());
            self::assertFalse($drawing->getResizeProportional());
            self::assertSame(600, $drawing->getWidth());
            self::assertSame(312, $drawing->getHeight());
        }
        // no name, no description, no type
        $spreadsheet->disconnectWorksheets();
    }
}
