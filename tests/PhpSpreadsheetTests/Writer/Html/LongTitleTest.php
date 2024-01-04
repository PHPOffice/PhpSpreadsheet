<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class LongTitleTest extends AbstractFunctional
{
    /**
     * @dataProvider providerTitles
     */
    public function testLongTitle(string $expected, string $title): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet
            ->getProperties()
            ->setTitle($title);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 1);

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Html');
        $spreadsheet->disconnectWorksheets();
        self::assertSame($expected, $reloadedSpreadsheet->getActiveSheet()->getTitle());
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public static function providerTitles(): array
    {
        return [
            ['Worksheet', 'This title is just a bit too long'],
            ['Worksheet', 'Invalid * character'],
            ['Legitimate Title', 'Legitimate Title'],
        ];
    }
}
