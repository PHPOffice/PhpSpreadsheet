<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Sparkline\Sparkline;
use PhpOffice\PhpSpreadsheet\Worksheet\Sparkline\SparklineGroup;
use PhpOffice\PhpSpreadsheet\Worksheet\Sparkline\SparklineType;
use PHPUnit\Framework\TestCase;

class SparklineTest extends TestCase
{
    public function testSparklineDefaults(): void
    {
        $sparkline = new Sparkline('G2', 'B2:F2');
        self::assertSame('G2', $sparkline->getLocation());
        self::assertSame('B2:F2', $sparkline->getDataRange());
        self::assertSame('G2', (string) $sparkline);
    }

    public function testLocationNormalisation(): void
    {
        $sparkline = new Sparkline('$g$2', 'Sheet1!B2:F2');
        self::assertSame('G2', $sparkline->getLocation());

        $sparkline->setLocation('Sheet1!H5');
        self::assertSame('H5', $sparkline->getLocation());
    }

    public function testLocationMustBeSingleCell(): void
    {
        $this->expectException(PhpSpreadsheetException::class);
        new Sparkline('G2:G5', 'B2:F2');
    }

    public function testGroupDefaults(): void
    {
        $group = new SparklineGroup();
        self::assertSame(SparklineType::Line, $group->getType());
        self::assertSame([], $group->getSparklines());
        self::assertFalse($group->getDisplayMarkers());
        self::assertSame('FF376092', $group->getColorSeries());
        self::assertSame(0.75, $group->getLineWeight());
    }

    public function testGroupSetters(): void
    {
        $group = new SparklineGroup();
        $group->setType(SparklineType::Column)
            ->setDisplayMarkers(true)
            ->setDisplayHigh(true)
            ->setDisplayLow(true)
            ->setLineWeight(1.5)
            ->setColorSeries('FF00B050')
            ->setColorMarkers(null)
            ->setManualMin(0.0)
            ->setManualMax(100.0)
            ->createSparkline('G2', 'B2:F2')
            ->createSparkline('G3', 'B3:F3');

        self::assertSame(SparklineType::Column, $group->getType());
        self::assertTrue($group->getDisplayMarkers());
        self::assertTrue($group->getDisplayHigh());
        self::assertTrue($group->getDisplayLow());
        self::assertSame(1.5, $group->getLineWeight());
        self::assertSame('FF00B050', $group->getColorSeries());
        self::assertNull($group->getColorMarkers());
        self::assertSame(0.0, $group->getManualMin());
        self::assertSame(100.0, $group->getManualMax());
        self::assertCount(2, $group->getSparklines());
    }

    public function testWinLossType(): void
    {
        $group = new SparklineGroup();
        $group->setType(SparklineType::WinLoss);
        self::assertSame('stacked', $group->getType()->value);
    }

    public function testAxisAndDisplayOptions(): void
    {
        $group = new SparklineGroup();
        $group->setMinAxisType(SparklineGroup::AXIS_CUSTOM)
            ->setMaxAxisType(SparklineGroup::AXIS_GROUP)
            ->setDisplayFirst(true)
            ->setDisplayLast(true)
            ->setDisplayXAxis(true)
            ->setDisplayHidden(true)
            ->setRightToLeft(true)
            ->setDisplayEmptyCellsAs(SparklineGroup::EMPTY_AS_SPAN);

        self::assertSame(SparklineGroup::AXIS_CUSTOM, $group->getMinAxisType());
        self::assertSame(SparklineGroup::AXIS_GROUP, $group->getMaxAxisType());
        self::assertTrue($group->getDisplayFirst());
        self::assertTrue($group->getDisplayLast());
        self::assertTrue($group->getDisplayXAxis());
        self::assertTrue($group->getDisplayHidden());
        self::assertTrue($group->getRightToLeft());
        self::assertSame(SparklineGroup::EMPTY_AS_SPAN, $group->getDisplayEmptyCellsAs());
    }

    public function testSetSparklinesReplacesCollection(): void
    {
        $group = new SparklineGroup();
        $group->createSparkline('G2', 'B2:F2');
        $group->setSparklines([
            new Sparkline('H2', 'B2:F2'),
            new Sparkline('H3', 'B3:F3'),
        ]);

        self::assertCount(2, $group->getSparklines());
        self::assertSame('H2', $group->getSparklines()[0]->getLocation());
        self::assertSame('H3', $group->getSparklines()[1]->getLocation());
    }

    public function testColorGettersAndSetters(): void
    {
        $group = new SparklineGroup();
        $group->setColorNegative('FF112233')
            ->setColorAxis('FF445566')
            ->setColorFirst('FF778899')
            ->setColorLast('FFAABBCC')
            ->setColorHigh('FFDDEEFF')
            ->setColorLow('FF010203');

        self::assertSame('FF112233', $group->getColorNegative());
        self::assertSame('FF445566', $group->getColorAxis());
        self::assertSame('FF778899', $group->getColorFirst());
        self::assertSame('FFAABBCC', $group->getColorLast());
        self::assertSame('FFDDEEFF', $group->getColorHigh());
        self::assertSame('FF010203', $group->getColorLow());
    }

    public function testWorksheetAddSparkline(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $group = $sheet->addSparkline(new Sparkline('G2', 'B2:F2'), SparklineType::Column);
        self::assertSame(SparklineType::Column, $group->getType());
        self::assertCount(1, $sheet->getSparklineGroupCollection());

        $sheet->addSparklineGroup(new SparklineGroup());
        self::assertCount(2, $sheet->getSparklineGroupCollection());

        $sheet->removeSparklineGroupCollection();
        self::assertCount(0, $sheet->getSparklineGroupCollection());

        $spreadsheet->disconnectWorksheets();
    }

    public function testWorksheetCloneCopiesSparklines(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->addSparkline(new Sparkline('G2', 'B2:F2'));

        $clone = clone $sheet;
        self::assertCount(1, $clone->getSparklineGroupCollection());
        // Mutating the clone must not affect the original.
        $clone->removeSparklineGroupCollection();
        self::assertCount(1, $sheet->getSparklineGroupCollection());
        self::assertCount(0, $clone->getSparklineGroupCollection());

        $spreadsheet->disconnectWorksheets();
    }
}
