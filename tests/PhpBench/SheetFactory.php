<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetPhpBench;

use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use RuntimeException;

final class SheetFactory
{
    /**
     * Rows of mixed text, integers and floats.
     *
     * @return list<list<float|int|string>>
     */
    public static function data(int $rows, int $cols): array
    {
        $data = [];
        for ($row = 1; $row <= $rows; ++$row) {
            $line = [];
            for ($col = 1; $col <= $cols; ++$col) {
                $line[] = match ($col % 3) {
                    0 => "Text {$row}-{$col}",
                    1 => $row * $col,
                    default => $row / $col,
                };
            }
            $data[] = $line;
        }

        return $data;
    }

    public static function create(int $rows, int $cols, bool $formulas = false, int $definedNames = 0): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(self::data($rows, $cols));
        if ($formulas) {
            $sumColumn = $cols + 1;
            for ($row = 1; $row <= $rows; ++$row) {
                $sheet->getCell([$sumColumn, $row])->setValue("=A{$row}+B{$row}*2");
            }
        }
        for ($i = 1; $i <= $definedNames; ++$i) {
            $spreadsheet->addNamedRange(new NamedRange("Name{$i}", $sheet, '$A$' . $i));
        }

        return $spreadsheet;
    }

    /**
     * Directory for input files that are written once and reused, so runs on different branches read the same files.
     */
    public static function fixtureDirectory(): string
    {
        $directory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'phpspreadsheet-bench';
        if (!is_dir($directory)) {
            mkdir($directory, 0o777, true);
        }

        return $directory;
    }

    public static function tempFile(string $extension): string
    {
        $file = tempnam(sys_get_temp_dir(), 'phpspreadsheet-bench-');
        if ($file === false) {
            throw new RuntimeException('Cannot create temporary file');
        }
        unlink($file);

        return "{$file}.{$extension}";
    }
}
