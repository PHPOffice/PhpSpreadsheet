<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\TestCase;

class Issue2450Test extends TestCase
{
    public function testIssue2450(): void
    {
        // Style specified as GENERAL rather than General.
        $filename = 'tests/data/Reader/XLSX/issue.2450.xlsx';
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        $birthYears = [
            'C2' => $sheet->getCell('C2')->getFormattedValue(),
            'C3' => $sheet->getCell('C3')->getFormattedValue(),
            'C4' => $sheet->getCell('C4')->getFormattedValue(),
        ];
        self::assertSame(
            [
                'C2' => '1932',
                'C3' => '1964',
                'C4' => '1988',
            ],
            $birthYears
        );

        $spreadsheet->disconnectWorksheets();
    }
}
