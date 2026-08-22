<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class Issue1425Test extends TestCase
{
    private static function setBackground(Worksheet $sheet, string $cells, string $color = 'ffffff00'): void
    {
        $sheet->getStyle($cells)
            ->getFill()
            ->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle($cells)
            ->getFill()
            ->getStartColor()
            ->setArgb($color);
    }

    /**
     * Not extending styles to all cells after insertNewColumnBefore.
     */
    public function testIssue4125(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Before');
        $sheet->getCell('A1')->setValue(1);
        $sheet->getCell('A2')->setValue(2);
        $sheet->getCell('B1')->setValue(3);
        $sheet->getCell('B2')->setValue(4);
        self::setBackground($sheet, 'A1:A2');
        self::setBackground($sheet, 'B1:B2', 'FF00FFFF');

        $sheet->insertNewColumnBefore('B', 1);

        $expected = [
            'A1' => 'ffffff00',
            'A2' => 'ffffff00',
            'B1' => 'ffffff00',
            'B2' => 'ffffff00',
            'C1' => 'FF00FFFF',
            'C2' => 'FF00FFFF',
        ];
        foreach ($expected as $key => $value) {
            $color = $sheet->getStyle($key)
                ->getFill()->getStartColor()->getArgb();
            self::assertSame($value, $color, "error in cell $key");
        }
        $spreadsheet->disconnectWorksheets();
    }
}
