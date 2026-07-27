<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Sparkline\Sparkline;
use PhpOffice\PhpSpreadsheet\Worksheet\Sparkline\SparklineGroup;
use PhpOffice\PhpSpreadsheet\Worksheet\Sparkline\SparklineType;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;

class SparklineTest extends TestCase
{
    private function buildSpreadsheet(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(
            [
                [1, 2, 3, 4, 5],
                [5, 4, 3, 2, 1],
                [-2, 3, -1, 4, -3],
            ],
            null,
            'B2'
        );

        $sheet->addSparkline(new Sparkline('G2', 'Sheet1!B2:F2'));

        $column = new SparklineGroup();
        $column->setType(SparklineType::Column)
            ->setDisplayMarkers(true)
            ->setDisplayHigh(true)
            ->setColorSeries('FF00B050')
            ->createSparkline('G3', 'Sheet1!B3:F3');
        $sheet->addSparklineGroup($column);

        $winLoss = new SparklineGroup();
        $winLoss->setType(SparklineType::WinLoss)
            ->setDisplayNegative(true)
            ->createSparkline('G4', 'Sheet1!B4:F4');
        $sheet->addSparklineGroup($winLoss);

        return $spreadsheet;
    }

    public function testWriteSparklineXml(): void
    {
        $spreadsheet = $this->buildSpreadsheet();
        $outfile = File::temporaryFilename();
        (new XlsxWriter($spreadsheet))->save($outfile);
        $spreadsheet->disconnectWorksheets();

        $data = (string) file_get_contents('zip://' . $outfile . '#xl/worksheets/sheet1.xml');
        unlink($outfile);

        self::assertStringContainsString('<x14:sparklineGroups', $data);
        self::assertStringContainsString('uri="{05C60535-1F16-4fd2-B633-F4F36F0B64E0}"', $data);
        self::assertStringContainsString('type="column"', $data);
        self::assertStringContainsString('type="stacked"', $data);
        self::assertStringContainsString('markers="1"', $data);
        self::assertStringContainsString('<x14:colorSeries rgb="FF00B050"/>', $data);
        self::assertStringContainsString('<xm:f>Sheet1!B2:F2</xm:f>', $data);
        self::assertStringContainsString('<xm:sqref>G2</xm:sqref>', $data);
    }

    public function testRoundTrip(): void
    {
        $spreadsheet = $this->buildSpreadsheet();
        $outfile = File::temporaryFilename();
        (new XlsxWriter($spreadsheet))->save($outfile);
        $spreadsheet->disconnectWorksheets();

        $reloaded = (new XlsxReader())->load($outfile);
        unlink($outfile);

        $groups = $reloaded->getActiveSheet()->getSparklineGroupCollection()->getArrayCopy();
        self::assertCount(3, $groups);

        self::assertSame(SparklineType::Line, $groups[0]->getType());
        self::assertSame('G2', $groups[0]->getSparklines()[0]->getLocation());
        self::assertSame('Sheet1!B2:F2', $groups[0]->getSparklines()[0]->getDataRange());

        self::assertSame(SparklineType::Column, $groups[1]->getType());
        self::assertTrue($groups[1]->getDisplayMarkers());
        self::assertTrue($groups[1]->getDisplayHigh());
        self::assertSame('FF00B050', $groups[1]->getColorSeries());

        self::assertSame(SparklineType::WinLoss, $groups[2]->getType());
        self::assertTrue($groups[2]->getDisplayNegative());
        self::assertSame('G4', $groups[2]->getSparklines()[0]->getLocation());

        $reloaded->disconnectWorksheets();
    }

    public function testNoSparklinesProducesNoExtLst(): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->getCell('A1')->setValue(1);
        $outfile = File::temporaryFilename();
        (new XlsxWriter($spreadsheet))->save($outfile);
        $spreadsheet->disconnectWorksheets();

        $data = (string) file_get_contents('zip://' . $outfile . '#xl/worksheets/sheet1.xml');
        unlink($outfile);

        self::assertStringNotContainsString('sparklineGroups', $data);
    }
}
