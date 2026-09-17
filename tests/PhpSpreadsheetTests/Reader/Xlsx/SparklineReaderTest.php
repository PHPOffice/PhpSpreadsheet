<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Worksheet\Sparkline\SparklineType;
use PHPUnit\Framework\TestCase;

class SparklineReaderTest extends TestCase
{
    private const FILENAME = 'tests/data/Reader/XLSX/sparklineEdgeCases.xlsx';

    public function testReadEdgeCases(): void
    {
        $spreadsheet = (new XlsxReader())->load(self::FILENAME);
        $groups = $spreadsheet->getActiveSheet()->getSparklineGroupCollection()->getArrayCopy();

        // The fixture contains:
        //  - an ext with a non-sparkline uri (ignored)
        //  - a group with no <x14:sparklines> element (dropped as empty)
        //  - a group whose first sparkline has an empty sqref (skipped) plus a
        //    valid one, and no colour elements
        // so exactly one group with one sparkline should survive.
        self::assertCount(1, $groups);

        $group = $groups[0];
        self::assertSame(SparklineType::Column, $group->getType());
        self::assertCount(1, $group->getSparklines());
        self::assertSame('G3', $group->getSparklines()[0]->getLocation());
        self::assertSame('Sheet1!B3:F3', $group->getSparklines()[0]->getDataRange());

        // The group carried no colour elements, so those colours read as null.
        self::assertNull($group->getColorSeries());
        self::assertNull($group->getColorAxis());

        $spreadsheet->disconnectWorksheets();
    }

    public function testReadDataOnlySkipsSparklines(): void
    {
        $reader = new XlsxReader();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load(self::FILENAME);

        self::assertCount(0, $spreadsheet->getActiveSheet()->getSparklineGroupCollection());

        $spreadsheet->disconnectWorksheets();
    }
}
