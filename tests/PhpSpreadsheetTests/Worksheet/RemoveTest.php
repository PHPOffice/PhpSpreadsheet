<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PHPUnit\Framework\Attributes\DataProvider;
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

    /**
     * @param array<array<int, int>> $expectedArray
     */
    #[DataProvider('providerColumnEdgeCases')]
    public function testColumnEdgeCases(string $start, int $num, array $expectedArray, string $expectedHighestColumn): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        $sheet->removeColumn($start, $num);
        self::assertSame($expectedArray, $sheet->toArray(formatData: false));
        self::assertSame($expectedHighestColumn, $sheet->getHighestColumn());

        $spreadsheet->disconnectWorksheets();
    }

    /**
     * @return array<string, array{string, int, int[][], string}>
     */
    public static function providerColumnEdgeCases(): array
    {
        return [
            'remove positive cols' => ['E', 2, [[1, 2, 3, 4, 7, 8, 9, 10]], 'H'],
            'remove negative cols' => ['E', -2, [[1, 2, 3, 6, 7, 8, 9, 10]], 'H'],
            'remove zero cols' => ['E', 0, [[1, 2, 3, 4, 5, 6, 7, 8, 9, 10]], 'J'],
            'remove cols above highest' => ['T', 2, [[1, 2, 3, 4, 5, 6, 7, 8, 9, 10]], 'J'],
        ];
    }

    /**
     * @param array<int, list<int>> $expectedArray
     */
    #[DataProvider('providerRowEdgeCases')]
    public function testRowEdgeCases(int $start, int $num, array $expectedArray, int $expectedHighestRow): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([[1], [2], [3], [4], [5], [6], [7], [8], [9], [10]]);
        $sheet->removeRow($start, $num);
        self::assertSame($expectedArray, $sheet->toArray(formatData: false));
        self::assertSame($expectedHighestRow, $sheet->getHighestRow());

        $spreadsheet->disconnectWorksheets();
    }

    /**
     * @return array<string, array{int, int, array<int, list<int>>, int}>
     */
    public static function providerRowEdgeCases(): array
    {
        return [
            'remove positive rows' => [5, 2, [[1], [2], [3], [4], [7], [8], [9], [10]], 8],
            'remove negative rows' => [5, -2, [[1], [2], [3], [6], [7], [8], [9], [10]], 8],
            'remove zero rows' => [5, 0, [[1], [2], [3], [4], [5], [6], [7], [8], [9], [10]], 10],
            'remove rows above highest' => [20, 2, [[1], [2], [3], [4], [5], [6], [7], [8], [9], [10]], 10],
        ];
    }
}
