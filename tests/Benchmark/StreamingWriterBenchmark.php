<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetBenchmarks;

use DateTimeImmutable;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Streaming\StreamingWriter;

class StreamingWriterBenchmark
{
    /** @return array<int, mixed> */
    public static function buildRow(int $row): array
    {
        return [
            'Name ' . $row,
            $row,
            $row * 1.5,
            $row % 2 === 0,
            new DateTimeImmutable('2026-01-01 00:00:00'),
            'Description for row ' . $row,
            $row * 3.25,
            $row % 3 === 0,
        ];
    }

    public static function writeStandard(int $rows, string $file): void
    {
        $spreadsheet = new Spreadsheet();

        try {
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Data');
            for ($row = 1; $row <= $rows; ++$row) {
                $values = self::buildRow($row);
                $values[4] = Date::PHPToExcel($values[4], $spreadsheet->getExcelCalendar());
                // Strict comparison preserves false values instead of treating them as null.
                $sheet->fromArray($values, null, 'A' . $row, true);
            }
            $sheet->getStyle('E1:E' . $rows)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DATETIME_BETTER);
            (new Xlsx($spreadsheet))->save($file);
        } finally {
            $spreadsheet->disconnectWorksheets();
        }
    }

    public static function writeStreaming(int $rows, string $file): void
    {
        $writer = new StreamingWriter($file);

        try {
            $sheet = $writer->startSheet('Data');
            for ($row = 1; $row <= $rows; ++$row) {
                $sheet->appendRow(self::buildRow($row));
            }
            $writer->close();
        } finally {
            $writer->abort();
        }
    }
}
