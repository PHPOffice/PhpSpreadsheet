<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
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

    public function testAxisAndManualLimitsRoundTrip(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([[1, 2, 3, 4, 5]], null, 'B2');

        $group = new SparklineGroup();
        $group->setType(SparklineType::Line)
            ->setMinAxisType(SparklineGroup::AXIS_CUSTOM)
            ->setMaxAxisType(SparklineGroup::AXIS_CUSTOM)
            ->setManualMin(-10.0)
            ->setManualMax(25.5)
            ->setLineWeight(2.25)
            ->setDisplayEmptyCellsAs(SparklineGroup::EMPTY_AS_ZERO)
            ->createSparkline('G2', 'Sheet1!B2:F2');
        $sheet->addSparklineGroup($group);

        $outfile = File::temporaryFilename();
        (new XlsxWriter($spreadsheet))->save($outfile);
        $spreadsheet->disconnectWorksheets();

        $data = (string) file_get_contents('zip://' . $outfile . '#xl/worksheets/sheet1.xml');
        self::assertStringContainsString('minAxisType="custom"', $data);
        self::assertStringContainsString('maxAxisType="custom"', $data);
        self::assertStringContainsString('manualMin="-10"', $data);
        self::assertStringContainsString('manualMax="25.5"', $data);
        // displayEmptyCellsAs="zero" is the writer default and must be omitted.
        self::assertStringNotContainsString('displayEmptyCellsAs', $data);

        $reloaded = (new XlsxReader())->load($outfile);
        unlink($outfile);

        $groups = $reloaded->getActiveSheet()->getSparklineGroupCollection()->getArrayCopy();
        self::assertCount(1, $groups);
        $reloadedGroup = $groups[0];
        self::assertSame(SparklineGroup::AXIS_CUSTOM, $reloadedGroup->getMinAxisType());
        self::assertSame(SparklineGroup::AXIS_CUSTOM, $reloadedGroup->getMaxAxisType());
        self::assertSame(-10.0, $reloadedGroup->getManualMin());
        self::assertSame(25.5, $reloadedGroup->getManualMax());
        self::assertSame(2.25, $reloadedGroup->getLineWeight());

        $reloaded->disconnectWorksheets();
    }

    public function testReaderIgnoresNonSparklineExt(): void
    {
        // A worksheet whose only extLst entry is a data-validation ext (a
        // different uri) must yield no sparkline groups.
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue(1);
        $validation = $sheet->getCell('A1')->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST)
            ->setFormula1('"a,b,c"')
            ->setShowDropDown(true);

        $outfile = File::temporaryFilename();
        (new XlsxWriter($spreadsheet))->save($outfile);
        $spreadsheet->disconnectWorksheets();

        $reloaded = (new XlsxReader())->load($outfile);
        unlink($outfile);

        self::assertCount(0, $reloaded->getActiveSheet()->getSparklineGroupCollection());
        $reloaded->disconnectWorksheets();
    }
}
