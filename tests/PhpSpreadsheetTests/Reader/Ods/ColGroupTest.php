<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Helper\Dimension;
use PhpOffice\PhpSpreadsheet\Reader\Ods as OdsReader;
use PHPUnit\Framework\TestCase;

/**
 * @TODO There is some fuzziness in the conversions to and from column widths
 */
class ColGroupTest extends TestCase
{
    private const ODS_COLGROUP_FILE = 'tests/data/Reader/Ods/colgroup.ods';
    private const ODS_COLHEADER_FILE = 'tests/data/Reader/Ods/colheader.ods';

    public function testColGroup(): void
    {
        $reader = new OdsReader();
        $reader->setReadEmptyCells(false);
        $spreadsheet = $reader->load(self::ODS_COLGROUP_FILE);
        self::assertSame('Arial', $spreadsheet->getDefaultStyle()->getFont()->getName());
        $sheet = $spreadsheet->getActiveSheet();
        $expected = [
            'A' => 2.778,
            'B' => 2.99,
            'C' => 2.91,
            'D' => 2.858,
            'E' => 1.746,
            'F' => 1.746,
            'G' => 1.746,
            'H' => 1.746,
        ];
        foreach ($expected as $col => $width) {
            self::assertEqualsWithDelta(
                $width,
                $sheet->getColumnDimension($col)
                    ->getWidth(Dimension::UOM_CENTIMETERS),
                1.0E-3,
                "mismatch for column $col"
            );
        }
        $spreadsheet->disconnectWorksheets();
    }

    public function testColHeader(): void
    {
        $reader = new OdsReader();
        $reader->setReadEmptyCells(false);
        $spreadsheet = $reader->load(self::ODS_COLHEADER_FILE);
        self::assertSame('Liberation Sans', $spreadsheet->getDefaultStyle()->getFont()->getName());
        $sheet = $spreadsheet->getActiveSheet();
        $expected = [
            'A' => 2.858,
        ];
        foreach ($expected as $col => $width) {
            self::assertEqualsWithDelta(
                $width,
                $sheet->getColumnDimension($col)
                    ->getWidth(Dimension::UOM_CENTIMETERS),
                1.0E-3,
                "mismatch for column $col"
            );
        }
        $spreadsheet->disconnectWorksheets();
    }
}
