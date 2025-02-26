<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PHPUnit\Framework\TestCase;

class RemoveTest extends TestCase
{
    public function testRemoveRow(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $fillColors = [
            'FFFF0000',
            'FF00FF00',
            'FF0000FF',
        ];
        $rowHeights = [-1.0, -1.0, 1.2, 1.3, 1.4, 1.5, -1.0, -1.0, -1.0];
        for ($row = 1; $row < 10; ++$row) {
            $sheet->getCell("B$row")
                ->getStyle()
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->setStartColor(new Color($fillColors[$row % 3]));
            $sheet->getCell("B$row")->setValue("X$row");
            $height = $rowHeights[$row - 1];
            if ($height > 0) {
                $sheet->getRowDimension($row)->setRowHeight($height);
            }
        }
        //$mapRow = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        $sheet->removeRow(4, 2);
        $mapRow = [1, 2, 3, 6, 7, 8, 9];
        $rowCount = count($mapRow);
        for ($row = 1; $row <= $rowCount; ++$row) {
            $mappedRow = $mapRow[$row - 1];
            self::assertSame("X$mappedRow", $sheet->getCell("B$row")->getValue(), "Row value $row mapped to $mappedRow");
            self::assertSame($fillColors[$mappedRow % 3], $sheet->getCell("B$row")->getStyle()->getFill()->getStartColor()->getArgb(), "Row fill color $row mapped to $mappedRow");
            self::assertSame($rowHeights[$mappedRow - 1], $sheet->getRowDimension($row)->getRowHeight(), "Row height $row mapped to $mappedRow");
        }

        $spreadsheet->disconnectWorksheets();
    }

    public function testRemoveColumn(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $fillColors = [
            'FFFF0000',
            'FF00FF00',
            'FF0000FF',
        ];
        $colWidths = [-1, -1, 1.2, 1.3, 1.4, 1.5, -1, -1, -1];
        for ($colNumber = 1; $colNumber < 10; ++$colNumber) {
            $col = Coordinate::stringFromColumnIndex($colNumber);
            $sheet->getCell("{$col}1")
                ->getStyle()
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->setStartColor(new Color($fillColors[$colNumber % 3]));
            $sheet->getCell("{$col}1")->setValue("100$col");
            $width = $colWidths[$colNumber - 1];
            if ($width > 0) {
                $sheet->getColumnDimension($col)->setWidth($width);
            }
        }
        //$mapCol = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        $sheet->removeColumn('D', 2);
        $mapCol = [1, 2, 3, 6, 7, 8, 9];
        $colCount = count($mapCol);
        for ($colNumber = 1; $colNumber < $colCount; ++$colNumber) {
            $col = Coordinate::stringFromColumnIndex($colNumber);
            $mappedCol = $mapCol[$colNumber - 1];
            $mappedColString = Coordinate::stringFromColumnIndex($mappedCol);
            self::assertSame("100$mappedColString", $sheet->getCell("{$col}1")->getValue(), "Column value $colNumber mapped to $mappedCol");
            self::assertSame($fillColors[$mappedCol % 3], $sheet->getCell("{$col}1")->getStyle()->getFill()->getStartColor()->getArgb(), "Col fill color $col mapped to $mappedColString");
            self::assertEquals($colWidths[$mappedCol - 1], $sheet->getColumnDimension($col)->getWidth(), "Col width $col mapped to $mappedColString");
        }

        $spreadsheet->disconnectWorksheets();
    }
}
