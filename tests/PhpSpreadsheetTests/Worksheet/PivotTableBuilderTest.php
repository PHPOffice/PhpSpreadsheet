<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotCacheDefinition;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotField;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotFieldGroup;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotTable;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotTableBuilder;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ZipArchive;

class PivotTableBuilderTest extends TestCase
{
    /** @var string[] */
    private array $tempFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->tempFiles as $tempFile) {
            @unlink($tempFile);
        }
        $this->tempFiles = [];
    }

    private function sampleSpreadsheet(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $data = $spreadsheet->getActiveSheet();
        $data->setTitle('Data');
        $data->fromArray([
            ['Region', 'Product', 'Amount'],
            ['East', 'Widget', 100],
            ['West', 'Widget', 150],
            ['East', 'Gadget', 200],
            ['West', 'Gadget', 250],
        ], null, 'A1');

        $pivotSheet = $spreadsheet->createSheet();
        $pivotSheet->setTitle('Pivot');

        return $spreadsheet;
    }

    private function groupingSpreadsheet(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $data = $spreadsheet->getActiveSheet();
        $data->setTitle('Data');
        $data->fromArray([
            ['Age', 'OrderDate', 'Region', 'Amount'],
            [23, '2024-01-15', 'East', 100],
            [37, '2024-06-20', 'West', 150],
            [45, '2025-02-10', 'East', 200],
            [51, '2025-11-05', 'West', 250],
        ], null, 'A1');

        $pivotSheet = $spreadsheet->createSheet();
        $pivotSheet->setTitle('Pivot');

        return $spreadsheet;
    }

    public function testAddPageFieldPlacesFieldOnPageAxis(): void
    {
        $spreadsheet = $this->groupingSpreadsheet();
        $builder = new PivotTableBuilder($spreadsheet->getSheetByNameOrThrow('Data'), 'A1:D5');
        $pivotTable = $builder
            ->addPageField('Region')
            ->addRowField('Age')
            ->addDataField('Amount', PivotField::SUBTOTAL_SUM)
            ->build($spreadsheet->getSheetByNameOrThrow('Pivot'), 'A4', 'Filtered');

        self::assertSame(['Region'], $this->fieldNames($pivotTable->getPageFields()));

        $outputFile = $this->save($spreadsheet);

        $zip = new ZipArchive();
        self::assertTrue($zip->open($outputFile) === true);
        $definition = (string) $zip->getFromName('xl/pivotTables/pivotTable1.xml');
        $zip->close();

        self::assertStringContainsString('<pageFields count="1">', $definition);
        self::assertStringContainsString('<pageField fld="2" hier="-1"/>', $definition);
        self::assertStringContainsString('axis="axisPage"', $definition);

        $reloaded = (new XlsxReader())->load($outputFile);
        $pivotTable = $reloaded->getSheetByNameOrThrow('Pivot')->getPivotTableByName('Filtered');
        self::assertNotNull($pivotTable);
        self::assertSame(['Region'], $this->fieldNames($pivotTable->getPageFields()));
        $reloaded->disconnectWorksheets();
    }

    public function testSetName(): void
    {
        $spreadsheet = $this->groupingSpreadsheet();
        $builder = new PivotTableBuilder($spreadsheet->getSheetByNameOrThrow('Data'), 'A1:D5');
        $pivotTable = $builder
            ->addPageField('Region')
            ->addRowField('Age')
            ->addDataField('Amount', PivotField::SUBTOTAL_SUM)
            ->build($spreadsheet->getSheetByNameOrThrow('Pivot'), 'A4', 'Filtered');
        $pivotTable->setName('FilteredRenamed');

        self::assertSame(['Region'], $this->fieldNames($pivotTable->getPageFields()));

        $outputFile = $this->save($spreadsheet);

        $zip = new ZipArchive();
        self::assertTrue($zip->open($outputFile) === true);
        $definition = (string) $zip->getFromName('xl/pivotTables/pivotTable1.xml');
        $zip->close();

        self::assertStringContainsString('<pageFields count="1">', $definition);
        self::assertStringContainsString('<pageField fld="2" hier="-1"/>', $definition);
        self::assertStringContainsString('axis="axisPage"', $definition);

        $reloaded = (new XlsxReader())->load($outputFile);
        $pivotTable = $reloaded->getSheetByNameOrThrow('Pivot')->getPivotTableByName('FilteredRenamed');
        self::assertNotNull($pivotTable);
        self::assertSame(['Region'], $this->fieldNames($pivotTable->getPageFields()));
        $reloaded->disconnectWorksheets();
    }

    public function testNumericRangeGroupingIsEmitted(): void
    {
        $spreadsheet = $this->groupingSpreadsheet();
        $builder = new PivotTableBuilder($spreadsheet->getSheetByNameOrThrow('Data'), 'A1:D5');
        $builder
            ->groupFieldByNumericRange('Age', 10.0, 20.0, 60.0)
            ->addRowField('Age')
            ->addDataField('Amount', PivotField::SUBTOTAL_SUM)
            ->build($spreadsheet->getSheetByNameOrThrow('Pivot'), 'A3', 'AgeGroups');

        $outputFile = $this->save($spreadsheet);

        $zip = new ZipArchive();
        self::assertTrue($zip->open($outputFile) === true);
        $cacheDefinition = (string) $zip->getFromName('xl/pivotCache/pivotCacheDefinition1.xml');
        $zip->close();

        self::assertStringContainsString('<rangePr groupInterval="10" startNum="20" endNum="60"/>', $cacheDefinition);
        self::assertStringContainsString('<s v="20-30"/>', $cacheDefinition);
        self::assertStringContainsString('<s v="&lt;20"/>', $cacheDefinition);
        self::assertStringContainsString('<s v="&gt;60"/>', $cacheDefinition);
    }

    public function testDateQuarterGroupingIsEmitted(): void
    {
        $spreadsheet = $this->groupingSpreadsheet();
        $builder = new PivotTableBuilder($spreadsheet->getSheetByNameOrThrow('Data'), 'A1:D5');
        $builder
            ->groupFieldByDate('OrderDate', PivotFieldGroup::GROUP_BY_QUARTERS)
            ->addRowField('OrderDate')
            ->addDataField('Amount', PivotField::SUBTOTAL_SUM)
            ->build($spreadsheet->getSheetByNameOrThrow('Pivot'), 'A3', 'ByQuarter');

        $outputFile = $this->save($spreadsheet);

        $zip = new ZipArchive();
        self::assertTrue($zip->open($outputFile) === true);
        $cacheDefinition = (string) $zip->getFromName('xl/pivotCache/pivotCacheDefinition1.xml');
        $zip->close();

        self::assertStringContainsString('containsDate="1"', $cacheDefinition);
        // A date field group must carry its bounds, otherwise Excel reports the
        // workbook as corrupt; fall back to the sentinel range when none given.
        self::assertStringContainsString('minDate="1900-01-01T00:00:00"', $cacheDefinition);
        self::assertStringContainsString('maxDate="9999-12-31T00:00:00"', $cacheDefinition);
        self::assertStringContainsString('groupBy="quarters"', $cacheDefinition);
        self::assertStringContainsString('startDate="1900-01-01T00:00:00"', $cacheDefinition);
        self::assertStringContainsString('endDate="9999-12-31T00:00:00"', $cacheDefinition);
        self::assertStringContainsString('<s v="Qtr1"/>', $cacheDefinition);
        self::assertStringContainsString('<s v="Qtr4"/>', $cacheDefinition);
    }

    public function testDateMonthGroupingIsEmitted(): void
    {
        $spreadsheet = $this->groupingSpreadsheet();
        $builder = new PivotTableBuilder($spreadsheet->getSheetByNameOrThrow('Data'), 'A1:D5');
        $builder
            ->groupFieldByDate('OrderDate', PivotFieldGroup::GROUP_BY_MONTHS)
            ->addRowField('OrderDate')
            ->addDataField('Amount', PivotField::SUBTOTAL_SUM)
            ->build($spreadsheet->getSheetByNameOrThrow('Pivot'), 'A3', 'ByMonth');

        $outputFile = $this->save($spreadsheet);

        $zip = new ZipArchive();
        self::assertTrue($zip->open($outputFile) === true);
        $cacheDefinition = (string) $zip->getFromName('xl/pivotCache/pivotCacheDefinition1.xml');
        $zip->close();

        self::assertStringContainsString('groupBy="months"', $cacheDefinition);
        self::assertStringContainsString('<s v="Jan"/>', $cacheDefinition);
        self::assertStringContainsString('<s v="Dec"/>', $cacheDefinition);
    }

    public function testDateYearGroupingIsEmitted(): void
    {
        $spreadsheet = $this->groupingSpreadsheet();
        $builder = new PivotTableBuilder($spreadsheet->getSheetByNameOrThrow('Data'), 'A1:D5');
        $builder
            ->groupFieldByDate('OrderDate', PivotFieldGroup::GROUP_BY_YEARS)
            ->addRowField('OrderDate')
            ->addDataField('Amount', PivotField::SUBTOTAL_SUM)
            ->build($spreadsheet->getSheetByNameOrThrow('Pivot'), 'A3', 'ByYear');

        $outputFile = $this->save($spreadsheet);

        $zip = new ZipArchive();
        self::assertTrue($zip->open($outputFile) === true);
        $cacheDefinition = (string) $zip->getFromName('xl/pivotCache/pivotCacheDefinition1.xml');
        $zip->close();

        self::assertStringContainsString('groupBy="years"', $cacheDefinition);
    }

    public function testNumericGroupingWithFractionalIntervalIsEmitted(): void
    {
        $spreadsheet = $this->groupingSpreadsheet();
        $builder = new PivotTableBuilder($spreadsheet->getSheetByNameOrThrow('Data'), 'A1:D5');
        $builder
            ->groupFieldByNumericRange('Amount', 2.5, 0.0, 5.0)
            ->addRowField('Amount')
            ->addDataField('Amount', PivotField::SUBTOTAL_SUM)
            ->build($spreadsheet->getSheetByNameOrThrow('Pivot'), 'A3', 'FractionGroups');

        $outputFile = $this->save($spreadsheet);

        $zip = new ZipArchive();
        self::assertTrue($zip->open($outputFile) === true);
        $cacheDefinition = (string) $zip->getFromName('xl/pivotCache/pivotCacheDefinition1.xml');
        $zip->close();

        // Fractional interval must keep its decimal (num() non-integer path).
        self::assertStringContainsString('groupInterval="2.5"', $cacheDefinition);
        self::assertStringContainsString('<s v="0-2.5"/>', $cacheDefinition);
    }

    public function testGroupingRejectsUnknownField(): void
    {
        $spreadsheet = $this->groupingSpreadsheet();
        $builder = new PivotTableBuilder($spreadsheet->getSheetByNameOrThrow('Data'), 'A1:D5');

        $this->expectException(PhpSpreadsheetException::class);
        $builder->groupFieldByNumericRange('Nonexistent', 10.0);
    }

    public function testBuildProducesGeneratedModel(): void
    {
        $spreadsheet = $this->sampleSpreadsheet();
        $data = $spreadsheet->getSheetByNameOrThrow('Data');
        $pivotSheet = $spreadsheet->getSheetByNameOrThrow('Pivot');

        $builder = new PivotTableBuilder($data, 'A1:C5');
        $pivotTable = $builder
            ->addRowField('Region')
            ->addColumnField('Product')
            ->addDataField('Amount', PivotField::SUBTOTAL_SUM)
            ->build($pivotSheet, 'A3', 'SalesPivot');

        self::assertTrue($pivotTable->isGenerated());
        self::assertSame('SalesPivot', $pivotTable->getName());
        self::assertSame(['Region'], $this->fieldNames($pivotTable->getRowFields()));
        self::assertSame(['Product'], $this->fieldNames($pivotTable->getColumnFields()));
        self::assertSame(['Amount'], $this->fieldNames($pivotTable->getDataFields()));
        self::assertSame('Sum of Amount', $pivotTable->getDataFields()[0]->getDataFieldCaption());

        $cache = $pivotTable->getCacheDefinition();
        self::assertNotNull($cache);
        self::assertSame('Data', $cache->getSourceWorksheet());
        self::assertSame('A1:C5', $cache->getSourceRange());
        self::assertSame(['East', 'West'], $cache->getSharedItems('Region'));
        self::assertSame(['Widget', 'Gadget'], $cache->getSharedItems('Product'));

        self::assertCount(1, $pivotSheet->getPivotTableCollection());
    }

    public function testBuildRequiresADataField(): void
    {
        $spreadsheet = $this->sampleSpreadsheet();
        $builder = new PivotTableBuilder($spreadsheet->getSheetByNameOrThrow('Data'), 'A1:C5');
        $builder->addRowField('Region');

        $this->expectException(PhpSpreadsheetException::class);
        $builder->build($spreadsheet->getSheetByNameOrThrow('Pivot'), 'A3');
    }

    public function testBuildRejectsUnknownField(): void
    {
        $spreadsheet = $this->sampleSpreadsheet();
        $builder = new PivotTableBuilder($spreadsheet->getSheetByNameOrThrow('Data'), 'A1:C5');

        $this->expectException(PhpSpreadsheetException::class);
        $builder->addRowField('Nonexistent');
    }

    public function testGeneratedPivotIsWrittenAndReadableAgain(): void
    {
        $spreadsheet = $this->sampleSpreadsheet();
        $builder = new PivotTableBuilder($spreadsheet->getSheetByNameOrThrow('Data'), 'A1:C5');
        $builder
            ->addRowField('Region')
            ->addColumnField('Product')
            ->addDataField('Amount', PivotField::SUBTOTAL_SUM)
            ->build($spreadsheet->getSheetByNameOrThrow('Pivot'), 'A3', 'SalesPivot');

        $outputFile = $this->save($spreadsheet);

        $parts = $this->zipEntryNames($outputFile);
        self::assertContains('xl/pivotTables/pivotTable1.xml', $parts);
        self::assertContains('xl/pivotCache/pivotCacheDefinition1.xml', $parts);
        self::assertContains('xl/pivotCache/pivotCacheRecords1.xml', $parts);

        $reloaded = (new XlsxReader())->load($outputFile);
        $pivotTable = $reloaded->getSheetByNameOrThrow('Pivot')->getPivotTableByName('SalesPivot');
        self::assertNotNull($pivotTable);
        $cache = $pivotTable->getCacheDefinition();
        self::assertNotNull($cache);
        self::assertSame('Data', $cache->getSourceWorksheet());
        self::assertSame('A1:C5', $cache->getSourceRange());
        self::assertSame(['Region'], $this->fieldNames($pivotTable->getRowFields()));
        self::assertSame(['Amount'], $this->fieldNames($pivotTable->getDataFields()));

        $reloaded->disconnectWorksheets();
    }

    public function testGeneratedPivotWiringIsConsistent(): void
    {
        $spreadsheet = $this->sampleSpreadsheet();
        $builder = new PivotTableBuilder($spreadsheet->getSheetByNameOrThrow('Data'), 'A1:C5');
        $builder
            ->addRowField('Region')
            ->addDataField('Amount', PivotField::SUBTOTAL_SUM)
            ->build($spreadsheet->getSheetByNameOrThrow('Pivot'), 'A3', 'SalesPivot');

        $outputFile = $this->save($spreadsheet);

        $zip = new ZipArchive();
        self::assertTrue($zip->open($outputFile) === true);
        $workbook = (string) $zip->getFromName('xl/workbook.xml');
        $workbookRels = (string) $zip->getFromName('xl/_rels/workbook.xml.rels');
        $contentTypes = (string) $zip->getFromName('[Content_Types].xml');
        $zip->close();

        self::assertStringContainsString('<pivotCaches>', $workbook);
        self::assertStringContainsString('r:id="rId_pivotCacheDef_1"', $workbook);
        self::assertStringContainsString('Id="rId_pivotCacheDef_1"', $workbookRels);
        self::assertStringContainsString('spreadsheetml.pivotTable+xml', $contentTypes);
        self::assertStringContainsString('spreadsheetml.pivotCacheDefinition+xml', $contentTypes);
        self::assertStringContainsString('spreadsheetml.pivotCacheRecords+xml', $contentTypes);
    }

    public function testAverageAggregationEmitsSubtotalAttribute(): void
    {
        $spreadsheet = $this->sampleSpreadsheet();
        $builder = new PivotTableBuilder($spreadsheet->getSheetByNameOrThrow('Data'), 'A1:C5');
        $builder
            ->addRowField('Region')
            ->addDataField('Amount', PivotField::SUBTOTAL_AVERAGE)
            ->build($spreadsheet->getSheetByNameOrThrow('Pivot'), 'A3', 'AvgPivot');

        $outputFile = $this->save($spreadsheet);

        $zip = new ZipArchive();
        self::assertTrue($zip->open($outputFile) === true);
        $definition = (string) $zip->getFromName('xl/pivotTables/pivotTable1.xml');
        $zip->close();

        self::assertStringContainsString('subtotal="average"', $definition);
        self::assertStringContainsString('name="Average of Amount"', $definition);

        // Reading the file back must recover the explicit subtotal attribute.
        $reloaded = (new XlsxReader())->load($outputFile);
        $pivotTable = $reloaded->getSheetByNameOrThrow('Pivot')->getPivotTableByName('AvgPivot');
        self::assertNotNull($pivotTable);
        $dataFields = $pivotTable->getDataFields();
        self::assertCount(1, $dataFields);
        self::assertSame(PivotField::SUBTOTAL_AVERAGE, $dataFields[0]->getSubtotal());
        $reloaded->disconnectWorksheets();
    }

    public function testPivotFieldItemsIncludeSharedItemIndices(): void
    {
        $spreadsheet = $this->sampleSpreadsheet();
        $builder = new PivotTableBuilder($spreadsheet->getSheetByNameOrThrow('Data'), 'A1:C5');
        $builder
            ->addRowField('Region')
            ->addColumnField('Product')
            ->addDataField('Amount', PivotField::SUBTOTAL_SUM)
            ->build($spreadsheet->getSheetByNameOrThrow('Pivot'), 'A3', 'SalesPivot');

        $outputFile = $this->save($spreadsheet);

        $zip = new ZipArchive();
        self::assertTrue($zip->open($outputFile) === true);
        $definition = (string) $zip->getFromName('xl/pivotTables/pivotTable1.xml');
        $zip->close();

        // Region has 2 distinct items (East, West): items count="3", item x="0", item x="1", item t="default"
        self::assertStringContainsString('<pivotField axis="axisRow" showAll="0"><items count="3"><item x="0"/><item x="1"/><item t="default"/></items></pivotField>', $definition);
        // Product has 2 distinct items (Widget, Gadget): items count="3", item x="0", item x="1", item t="default"
        self::assertStringContainsString('<pivotField axis="axisCol" showAll="0"><items count="3"><item x="0"/><item x="1"/><item t="default"/></items></pivotField>', $definition);
        // Amount is a data field: no items collection
        self::assertStringContainsString('<pivotField dataField="1" showAll="0"/>', $definition);
    }

    public function testPivotFieldItemsForGroupedFields(): void
    {
        $spreadsheet = $this->groupingSpreadsheet();
        $builder = new PivotTableBuilder($spreadsheet->getSheetByNameOrThrow('Data'), 'A1:D5');
        $builder
            ->groupFieldByNumericRange('Age', 10.0, 20.0, 60.0)
            ->addRowField('Age')
            ->groupFieldByDate('OrderDate', PivotFieldGroup::GROUP_BY_QUARTERS)
            ->addColumnField('OrderDate')
            ->addDataField('Amount', PivotField::SUBTOTAL_SUM)
            ->build($spreadsheet->getSheetByNameOrThrow('Pivot'), 'A3', 'GroupedPivot');

        $outputFile = $this->save($spreadsheet);

        $zip = new ZipArchive();
        self::assertTrue($zip->open($outputFile) === true);
        $definition = (string) $zip->getFromName('xl/pivotTables/pivotTable1.xml');
        $zip->close();

        // Age numeric group: 4 buckets (20-30, 30-40, 40-50, 50-60) + 2 bounds (<20, >60) = 6 group items -> items count="7" (x="0".."x="5" + t="default")
        self::assertStringContainsString('<pivotField axis="axisRow" showAll="0"><items count="7"><item x="0"/><item x="1"/><item x="2"/><item x="3"/><item x="4"/><item x="5"/><item t="default"/></items></pivotField>', $definition);
        // OrderDate quarters group: 6 items (<1/1/1900, Qtr1, Qtr2, Qtr3, Qtr4, >12/31/9999) -> items count="7" (x="0".."x="5" + t="default")
        self::assertStringContainsString('<pivotField axis="axisCol" showAll="0"><items count="7"><item x="0"/><item x="1"/><item x="2"/><item x="3"/><item x="4"/><item x="5"/><item t="default"/></items></pivotField>', $definition);
    }

    /**
     * The <items> in a pivot field and the <groupItems> in the cache definition
     * describe the same set of values. If the two counts drift apart, Excel
     * reports the workbook as corrupt, so assert the invariant directly rather
     * than hard-coding a count that could be updated in only one place.
     */
    #[DataProvider('numericGroupProvider')]
    public function testGroupItemCountsAgreeBetweenParts(float $interval, ?float $startNum, ?float $endNum): void
    {
        $spreadsheet = $this->groupingSpreadsheet();
        $builder = new PivotTableBuilder($spreadsheet->getSheetByNameOrThrow('Data'), 'A1:D5');
        $builder
            ->groupFieldByNumericRange('Age', $interval, $startNum, $endNum)
            ->addRowField('Age')
            ->addDataField('Amount', PivotField::SUBTOTAL_SUM)
            ->build($spreadsheet->getSheetByNameOrThrow('Pivot'), 'A3', 'GroupedPivot');

        $outputFile = $this->save($spreadsheet);

        $zip = new ZipArchive();
        self::assertTrue($zip->open($outputFile) === true);
        $definition = (string) $zip->getFromName('xl/pivotTables/pivotTable1.xml');
        $cache = (string) $zip->getFromName('xl/pivotCache/pivotCacheDefinition1.xml');
        $zip->close();

        self::assertSame(1, preg_match('#<groupItems count="(\d+)"#', $cache, $groupMatch));
        self::assertSame(1, preg_match('#<items count="(\d+)"#', $definition, $itemMatch));

        $groupItemCount = (int) $groupMatch[1];
        $pivotItemCount = (int) $itemMatch[1];

        // The pivot field carries one extra <item t="default"/>.
        self::assertSame(
            $groupItemCount + 1,
            $pivotItemCount,
            'pivotField items must match groupItems plus the default item'
        );

        // Every emitted index must be addressable in the cache's group items.
        self::assertSame(
            $groupItemCount,
            substr_count($definition, '<item x='),
            'each <item x="N"/> must reference a real group item'
        );
    }

    /**
     * @return array<string, array{0: float, 1: ?float, 2: ?float}>
     */
    public static function numericGroupProvider(): array
    {
        return [
            'whole multiples' => [10.0, 20.0, 60.0],
            'range not a multiple of interval' => [10.0, 20.0, 65.0],
            'fractional interval' => [0.1, 0.0, 1.0],
            'fractional interval, uneven' => [0.3, 0.0, 1.0],
            'negative start' => [5.0, -20.0, 20.0],
            'null bounds' => [10.0, null, null],
            'zero interval' => [0.0, 0.0, 100.0],
            'negative interval' => [-5.0, 0.0, 100.0],
            'inverted bounds' => [10.0, 60.0, 20.0],
        ];
    }

    /**
     * A fractional interval must not gain a spurious zero-width trailing bucket
     * from repeated floating-point addition: 0..1 by 0.1 is exactly 10 buckets.
     */
    public function testFractionalIntervalDoesNotDriftAnExtraBucket(): void
    {
        $spreadsheet = $this->groupingSpreadsheet();
        $builder = new PivotTableBuilder($spreadsheet->getSheetByNameOrThrow('Data'), 'A1:D5');
        $builder
            ->groupFieldByNumericRange('Age', 0.1, 0.0, 1.0)
            ->addRowField('Age')
            ->addDataField('Amount', PivotField::SUBTOTAL_SUM)
            ->build($spreadsheet->getSheetByNameOrThrow('Pivot'), 'A3', 'GroupedPivot');

        $outputFile = $this->save($spreadsheet);

        $zip = new ZipArchive();
        self::assertTrue($zip->open($outputFile) === true);
        $definition = (string) $zip->getFromName('xl/pivotTables/pivotTable1.xml');
        $cache = (string) $zip->getFromName('xl/pivotCache/pivotCacheDefinition1.xml');
        $zip->close();

        // 10 buckets + the "<0" and ">1" sentinel bounds.
        self::assertStringContainsString('<groupItems count="12">', $cache);
        self::assertStringContainsString('<items count="13">', $definition);
        // A degenerate "1-1" bucket is the signature of the accumulation bug.
        self::assertStringNotContainsString('<s v="1-1"/>', $cache);
    }

    /**
     * A field with no distinct values must still emit a well-formed, minimal
     * items collection rather than dangling indices.
     */
    public function testFieldWithNoSharedItemsEmitsOnlyDefaultItem(): void
    {
        $spreadsheet = new Spreadsheet();
        $data = $spreadsheet->getActiveSheet();
        $data->setTitle('Data');
        $data->fromArray(['Region', 'Amount'], null, 'A1');
        $data->fromArray([['', 1]], null, 'A2');
        $pivot = $spreadsheet->createSheet();
        $pivot->setTitle('Pivot');

        $builder = new PivotTableBuilder($data, 'A1:B2');
        $builder
            ->addRowField('Region')
            ->addDataField('Amount', PivotField::SUBTOTAL_SUM)
            ->build($pivot, 'A3', 'EmptyPivot');

        $outputFile = $this->save($spreadsheet);

        $zip = new ZipArchive();
        self::assertTrue($zip->open($outputFile) === true);
        $definition = (string) $zip->getFromName('xl/pivotTables/pivotTable1.xml');
        $zip->close();

        self::assertStringContainsString(
            '<pivotField axis="axisRow" showAll="0"><items count="1"><item t="default"/></items></pivotField>',
            $definition
        );
        self::assertStringNotContainsString('<item x=', $definition);
    }

    /**
     * An axis that declares fields must be accompanied by its item list.
     * Excel discards the whole pivot table when rowFields appears without
     * rowItems, even though the schema marks the element as optional.
     */
    public function testAxisFieldsAreAccompaniedByAxisItems(): void
    {
        $spreadsheet = $this->sampleSpreadsheet();
        $builder = new PivotTableBuilder($spreadsheet->getSheetByNameOrThrow('Data'), 'A1:C5');
        $builder
            ->addRowField('Region')
            ->addColumnField('Product')
            ->addDataField('Amount', PivotField::SUBTOTAL_SUM)
            ->build($spreadsheet->getSheetByNameOrThrow('Pivot'), 'A3', 'SalesPivot');

        $outputFile = $this->save($spreadsheet);

        $zip = new ZipArchive();
        self::assertTrue($zip->open($outputFile) === true);
        $definition = (string) $zip->getFromName('xl/pivotTables/pivotTable1.xml');
        $zip->close();

        self::assertStringContainsString('<rowItems count="1"><i t="grand"><x/></i></rowItems>', $definition);
        self::assertStringContainsString('<colItems count="1"><i t="grand"><x/></i></colItems>', $definition);

        // Schema order: rowFields, rowItems, colFields, colItems.
        $order = [];
        foreach (['rowFields', 'rowItems', 'colFields', 'colItems'] as $element) {
            $position = strpos($definition, '<' . $element . ' ');
            self::assertNotFalse($position, "$element is missing");
            $order[] = $position;
        }
        $sorted = $order;
        sort($sorted);
        self::assertSame($sorted, $order, 'axis elements must appear in schema order');
    }

    /**
     * An axis with no fields must not emit an empty item list.
     */
    public function testAxisItemsOmittedWhenAxisHasNoFields(): void
    {
        $spreadsheet = $this->sampleSpreadsheet();
        $builder = new PivotTableBuilder($spreadsheet->getSheetByNameOrThrow('Data'), 'A1:C5');
        $builder
            ->addRowField('Region')
            ->addDataField('Amount', PivotField::SUBTOTAL_SUM)
            ->build($spreadsheet->getSheetByNameOrThrow('Pivot'), 'A3', 'SalesPivot');

        $outputFile = $this->save($spreadsheet);

        $zip = new ZipArchive();
        self::assertTrue($zip->open($outputFile) === true);
        $definition = (string) $zip->getFromName('xl/pivotTables/pivotTable1.xml');
        $zip->close();

        self::assertStringContainsString('<rowItems', $definition);
        self::assertStringNotContainsString('<colFields', $definition);
        self::assertStringNotContainsString('<colItems', $definition);
    }

    /**
     * The declared location has to be able to hold the layout the rest of the
     * definition describes. A single-cell ref is not expanded on load: Excel
     * treats it as inconsistent and drops the pivot table.
     *
     * @param array<string, string> $placements
     */
    #[DataProvider('locationProvider')]
    public function testLocationSpansTheRenderedPivot(string $targetCell, array $placements, string $expected): void
    {
        $spreadsheet = $this->sampleSpreadsheet();
        $builder = new PivotTableBuilder($spreadsheet->getSheetByNameOrThrow('Data'), 'A1:C5');
        foreach ($placements as $fieldName => $axis) {
            if ($axis === 'row') {
                $builder->addRowField($fieldName);
            } else {
                $builder->addColumnField($fieldName);
            }
        }
        $builder
            ->addDataField('Amount', PivotField::SUBTOTAL_SUM)
            ->build($spreadsheet->getSheetByNameOrThrow('Pivot'), $targetCell, 'SalesPivot');

        $outputFile = $this->save($spreadsheet);

        $zip = new ZipArchive();
        self::assertTrue($zip->open($outputFile) === true);
        $definition = (string) $zip->getFromName('xl/pivotTables/pivotTable1.xml');
        $zip->close();

        self::assertStringContainsString('<location ref="' . $expected . '"', $definition);
        // A degenerate single-cell range is what Excel rejects.
        self::assertStringNotContainsString(
            '<location ref="' . $targetCell . ':' . $targetCell . '"',
            $definition
        );
    }

    /**
     * @return array<string, array{0: string, 1: array<string, string>, 2: string}>
     */
    public static function locationProvider(): array
    {
        // Region and Product each have 2 distinct values in the sample data.
        return [
            // header + col header, 2 row values, grand total = 5 rows;
            // row label + 2 product columns + grand total = 4 columns.
            'row and column fields' => ['A3', ['Region' => 'row', 'Product' => 'col'], 'A3:D7'],
            // header, 2 row values, grand total = 4 rows; label + 1 data = 2 columns.
            'row field only' => ['A3', ['Region' => 'row'], 'A3:B6'],
            'column field only' => ['A3', ['Product' => 'col'], 'A3:D5'],
            'offset target cell' => ['E10', ['Region' => 'row', 'Product' => 'col'], 'E10:H14'],
        ];
    }

    /**
     * Two or more data fields are laid out across the column axis under the
     * virtual "Values" field, which OOXML addresses with the reserved index
     * -2. Without it Excel reports the workbook as damaged and drops the
     * pivot table entirely.
     *
     * @see https://github.com/PHPOffice/PhpSpreadsheet/issues/5106
     */
    public function testMultipleDataFieldsEmitValuesFieldOnColumnAxis(): void
    {
        $spreadsheet = $this->sampleSpreadsheet();
        $builder = new PivotTableBuilder($spreadsheet->getSheetByNameOrThrow('Data'), 'A1:C5');
        $builder
            ->addRowField('Region')
            ->addDataField('Amount', PivotField::SUBTOTAL_SUM)
            ->addDataField('Product', PivotField::SUBTOTAL_COUNT)
            ->build($spreadsheet->getSheetByNameOrThrow('Pivot'), 'A3', 'TwoDataFields');

        $outputFile = $this->save($spreadsheet);

        $zip = new ZipArchive();
        self::assertTrue($zip->open($outputFile) === true);
        $definition = (string) $zip->getFromName('xl/pivotTables/pivotTable1.xml');
        $zip->close();

        self::assertStringContainsString('<colFields count="1"><field x="-2"/></colFields>', $definition);
        self::assertStringContainsString('<colItems count="2"><i><x/></i><i i="1"><x v="1"/></i></colItems>', $definition);
        // colFields/colItems must precede dataFields in schema order.
        self::assertLessThan(
            strpos($definition, '<dataFields'),
            (int) strpos($definition, '<colFields'),
            'colFields must precede dataFields'
        );
    }

    /**
     * A single data field needs no "Values" field: Excel places it implicitly.
     */
    public function testSingleDataFieldDoesNotEmitValuesField(): void
    {
        $spreadsheet = $this->sampleSpreadsheet();
        $builder = new PivotTableBuilder($spreadsheet->getSheetByNameOrThrow('Data'), 'A1:C5');
        $builder
            ->addRowField('Region')
            ->addDataField('Amount', PivotField::SUBTOTAL_SUM)
            ->build($spreadsheet->getSheetByNameOrThrow('Pivot'), 'A3', 'OneDataField');

        $outputFile = $this->save($spreadsheet);

        $zip = new ZipArchive();
        self::assertTrue($zip->open($outputFile) === true);
        $definition = (string) $zip->getFromName('xl/pivotTables/pivotTable1.xml');
        $zip->close();

        self::assertStringNotContainsString('x="-2"', $definition);
        self::assertStringNotContainsString('<colFields', $definition);
    }

    /**
     * A real column field and several data fields share the column axis: the
     * virtual "Values" field is appended after the real one.
     */
    public function testValuesFieldAppendedAfterRealColumnField(): void
    {
        $spreadsheet = $this->sampleSpreadsheet();
        $builder = new PivotTableBuilder($spreadsheet->getSheetByNameOrThrow('Data'), 'A1:C5');
        $builder
            ->addRowField('Region')
            ->addColumnField('Product')
            ->addDataField('Amount', PivotField::SUBTOTAL_SUM)
            ->addDataField('Amount', PivotField::SUBTOTAL_COUNT)
            ->build($spreadsheet->getSheetByNameOrThrow('Pivot'), 'A3', 'Mixed');

        $outputFile = $this->save($spreadsheet);

        $zip = new ZipArchive();
        self::assertTrue($zip->open($outputFile) === true);
        $definition = (string) $zip->getFromName('xl/pivotTables/pivotTable1.xml');
        $zip->close();

        self::assertStringContainsString(
            '<colFields count="2"><field x="1"/><field x="-2"/></colFields>',
            $definition
        );
    }

    /**
     * The same source column may be aggregated more than once. Each
     * aggregation is its own <dataFields> row referring to the same fld, while
     * <pivotFields> still carries exactly one entry per source column.
     *
     * @see https://github.com/PHPOffice/PhpSpreadsheet/issues/5106
     */
    public function testRepeatedDataFieldOnSameColumnKeepsEveryAggregation(): void
    {
        $spreadsheet = $this->sampleSpreadsheet();
        $builder = new PivotTableBuilder($spreadsheet->getSheetByNameOrThrow('Data'), 'A1:C5');
        $pivotTable = $builder
            ->addRowField('Region')
            ->addDataField('Amount', PivotField::SUBTOTAL_SUM)
            ->addDataField('Amount', PivotField::SUBTOTAL_AVERAGE)
            ->build($spreadsheet->getSheetByNameOrThrow('Pivot'), 'A3', 'TwoAggregations');

        self::assertCount(2, $pivotTable->getDataFieldDefinitions());
        // One pivot field per source column, regardless of aggregation count.
        self::assertCount(3, $pivotTable->getFields());

        $outputFile = $this->save($spreadsheet);

        $zip = new ZipArchive();
        self::assertTrue($zip->open($outputFile) === true);
        $definition = (string) $zip->getFromName('xl/pivotTables/pivotTable1.xml');
        $cache = (string) $zip->getFromName('xl/pivotCache/pivotCacheDefinition1.xml');
        $zip->close();

        self::assertStringContainsString('<dataFields count="2">', $definition);
        self::assertStringContainsString('<dataField name="Sum of Amount" fld="2"', $definition);
        self::assertStringContainsString('<dataField name="Average of Amount" fld="2" subtotal="average"', $definition);
        // Two data fields need the virtual "Values" field on the column axis.
        self::assertStringContainsString('<colFields count="1"><field x="-2"/></colFields>', $definition);
        // The source column is still described once in each per-column list.
        self::assertStringContainsString('<pivotFields count="3">', $definition);
        self::assertStringContainsString('<cacheFields count="3">', $cache);
    }

    /**
     * A repeated data field widens the rendered range: each data field gets
     * its own column.
     */
    public function testRepeatedDataFieldWidensLocation(): void
    {
        $spreadsheet = $this->sampleSpreadsheet();
        $builder = new PivotTableBuilder($spreadsheet->getSheetByNameOrThrow('Data'), 'A1:C5');
        $pivotTable = $builder
            ->addRowField('Region')
            ->addDataField('Amount', PivotField::SUBTOTAL_SUM)
            ->addDataField('Amount', PivotField::SUBTOTAL_AVERAGE)
            ->build($spreadsheet->getSheetByNameOrThrow('Pivot'), 'A3', 'TwoAggregations');

        // 1 row label + 2 data columns; header + 2 regions + grand total.
        self::assertSame('A3:C6', $pivotTable->getLocation());
    }

    /**
     * Re-placing a field on a row/column/page axis replaces the earlier axis
     * rather than accumulating, so a field never sits on two axes at once.
     */
    public function testReplacingAnAxisPlacementDoesNotAccumulate(): void
    {
        $spreadsheet = $this->sampleSpreadsheet();
        $builder = new PivotTableBuilder($spreadsheet->getSheetByNameOrThrow('Data'), 'A1:C5');
        $pivotTable = $builder
            ->addRowField('Region')
            ->addColumnField('Region')
            ->addDataField('Amount', PivotField::SUBTOTAL_SUM)
            ->build($spreadsheet->getSheetByNameOrThrow('Pivot'), 'A3', 'MovedField');

        self::assertSame([], $this->fieldNames($pivotTable->getRowFields()));
        self::assertSame(['Region'], $this->fieldNames($pivotTable->getColumnFields()));
    }

    /**
     * The declared range must stay inside the sheet. A target cell near the
     * right edge used to push the computed last column past XFD, which made
     * Coordinate::stringFromColumnIndex() throw instead of saving.
     */
    public function testLocationIsClampedToTheLastColumn(): void
    {
        $spreadsheet = $this->sampleSpreadsheet();
        $builder = new PivotTableBuilder($spreadsheet->getSheetByNameOrThrow('Data'), 'A1:C5');
        $pivotTable = $builder
            ->addRowField('Region')
            ->addColumnField('Product')
            ->addDataField('Amount', PivotField::SUBTOTAL_SUM)
            ->build($spreadsheet->getSheetByNameOrThrow('Pivot'), 'XFC1', 'EdgePivot');

        self::assertSame('XFC1:XFD5', $pivotTable->getLocation());
    }

    /**
     * The same applies to the bottom edge: a ref past row 1048576 is invalid.
     */
    public function testLocationIsClampedToTheLastRow(): void
    {
        $spreadsheet = $this->sampleSpreadsheet();
        $builder = new PivotTableBuilder($spreadsheet->getSheetByNameOrThrow('Data'), 'A1:C5');
        $pivotTable = $builder
            ->addRowField('Region')
            ->addColumnField('Product')
            ->addDataField('Amount', PivotField::SUBTOTAL_SUM)
            ->build($spreadsheet->getSheetByNameOrThrow('Pivot'), 'A1048575', 'BottomPivot');

        self::assertSame('A1048575:D1048576', $pivotTable->getLocation());
    }

    /**
     * A lowercase or absolute target cell must still produce a canonical ref:
     * the range start used to be echoed back verbatim while the end was
     * rebuilt from a normalized column index.
     */
    public function testLocationIsNormalised(): void
    {
        $spreadsheet = $this->sampleSpreadsheet();
        $builder = new PivotTableBuilder($spreadsheet->getSheetByNameOrThrow('Data'), 'A1:C5');
        $pivotTable = $builder
            ->addRowField('Region')
            ->addDataField('Amount', PivotField::SUBTOTAL_SUM)
            ->build($spreadsheet->getSheetByNameOrThrow('Pivot'), '$b$3', 'CasePivot');

        self::assertSame('B3:C6', $pivotTable->getLocation());
    }

    /**
     * Each column-field value repeats once per data field, and so does the
     * grand total, so the width has to scale with the data-field count.
     */
    public function testLocationWidthAccountsForDataFieldsUnderColumnFields(): void
    {
        $spreadsheet = new Spreadsheet();
        $data = $spreadsheet->getActiveSheet();
        $data->setTitle('Data');
        $data->fromArray([
            ['Region', 'Product', 'Amount', 'Qty'],
            ['East', 'Widget', 100, 1],
            ['West', 'Widget', 150, 2],
            ['East', 'Gadget', 200, 3],
            ['West', 'Gadget', 250, 4],
        ], null, 'A1');
        $pivotSheet = $spreadsheet->createSheet();
        $pivotSheet->setTitle('Pivot');

        $builder = new PivotTableBuilder($data, 'A1:D5');
        $pivotTable = $builder
            ->addRowField('Region')
            ->addColumnField('Product')
            ->addDataField('Amount', PivotField::SUBTOTAL_SUM)
            ->addDataField('Qty', PivotField::SUBTOTAL_SUM)
            ->build($pivotSheet, 'A3', 'WidePivot');

        // 1 row label + (2 products + 1 grand total) x 2 data fields = 7 columns.
        self::assertSame('A3:G7', $pivotTable->getLocation());
    }

    /**
     * A grouped field renders one row per group bucket, which bears no
     * relation to how many distinct values the source column holds. The
     * location has to follow the bucket count, not the raw value count.
     */
    public function testLocationFollowsGroupedItemCount(): void
    {
        $spreadsheet = $this->groupingSpreadsheet();
        $builder = new PivotTableBuilder($spreadsheet->getSheetByNameOrThrow('Data'), 'A1:D5');
        $pivotTable = $builder
            ->groupFieldByNumericRange('Age', 10.0, 0.0, 100.0)
            ->addRowField('Age')
            ->addDataField('Amount', PivotField::SUBTOTAL_SUM)
            ->build($spreadsheet->getSheetByNameOrThrow('Pivot'), 'A3', 'GroupedPivot');

        // 10 buckets + 2 out-of-range bounds = 12 rows, + header + grand total.
        self::assertSame('A3:B16', $pivotTable->getLocation());

        $outputFile = $this->save($spreadsheet);
        $zip = new ZipArchive();
        self::assertTrue($zip->open($outputFile) === true);
        $definition = (string) $zip->getFromName('xl/pivotTables/pivotTable1.xml');
        $zip->close();

        // The declared body must be able to hold every item the field declares.
        self::assertStringContainsString('<items count="13">', $definition);
    }

    /**
     * The item count the pivot field declares, the group items in the cache
     * and the rendered location all derive from one enumeration, so a group
     * that would exceed Excel's ceiling stays within it including the two
     * out-of-range bounds.
     */
    public function testGroupItemCountStaysWithinExcelLimit(): void
    {
        $group = PivotFieldGroup::numeric(1.0, 0.0, 1.0e9);

        self::assertSame(PivotFieldGroup::MAX_ITEMS, $group->renderedItemCount());
    }

    /**
     * A pivot table assembled directly against the model, without going
     * through PivotTableBuilder, registers no data-field definitions, so the
     * writer works from the list derived from the fields themselves. The
     * generated part must still be correct, including the virtual "Values"
     * field that two data fields require.
     */
    public function testWriterUsesDerivedDataFieldDefinitions(): void
    {
        $spreadsheet = new Spreadsheet();
        $data = $spreadsheet->getActiveSheet();
        $data->setTitle('Data');
        $data->fromArray([
            ['Region', 'Amount', 'Quantity'],
            ['East', 100, 1],
            ['West', 150, 2],
        ], null, 'A1');
        $pivotSheet = $spreadsheet->createSheet();
        $pivotSheet->setTitle('Pivot');

        $cache = new PivotCacheDefinition(1);
        $cache->setSourceWorksheet('Data');
        $cache->setSourceRange('A1:C3');
        $cache->setCacheFields(['Region', 'Amount', 'Quantity']);
        $cache->setSharedItems('Region', ['East', 'West']);

        $pivotTable = new PivotTable('Handmade');
        $pivotTable->setGenerated(true);
        $pivotTable->setCacheDefinition($cache);
        $pivotTable->setLocation('A3:C7');

        $region = new PivotField(0, 'Region');
        $region->setAxis(PivotField::AXIS_ROW);
        $pivotTable->addField($region);

        $amount = new PivotField(1, 'Amount');
        $amount->setDataField(true);
        $amount->setSubtotal(PivotField::SUBTOTAL_SUM);
        $amount->setDataFieldCaption('Sum of Amount');
        $pivotTable->addField($amount);

        $quantity = new PivotField(2, 'Quantity');
        $quantity->setDataField(true);
        $quantity->setSubtotal(PivotField::SUBTOTAL_COUNT);
        $quantity->setDataFieldCaption('Count of Quantity');
        $pivotTable->addField($quantity);

        // Nothing registered an explicit list.
        $pivotSheet->addPivotTable($pivotTable);

        $outputFile = $this->save($spreadsheet);

        $zip = new ZipArchive();
        self::assertTrue($zip->open($outputFile) === true);
        $definition = (string) $zip->getFromName('xl/pivotTables/pivotTable1.xml');
        $zip->close();

        self::assertStringContainsString('<dataFields count="2">', $definition);
        self::assertStringContainsString('<dataField name="Sum of Amount" fld="1"', $definition);
        self::assertStringContainsString('<dataField name="Count of Quantity" fld="2" subtotal="count"', $definition);
        // Two data fields still need the virtual "Values" field on the column axis.
        self::assertStringContainsString('<colFields count="1"><field x="-2"/></colFields>', $definition);
        self::assertStringContainsString('<colItems count="2"><i><x/></i><i i="1"><x v="1"/></i></colItems>', $definition);
    }

    /**
     * @param PivotField[] $fields
     *
     * @return string[]
     */
    private function fieldNames(array $fields): array
    {
        return array_map(static fn (PivotField $field): string => $field->getName(), $fields);
    }

    private function save(Spreadsheet $spreadsheet): string
    {
        $outputFile = tempnam(sys_get_temp_dir(), 'pivotbuild') . '.xlsx';
        $this->tempFiles[] = $outputFile;
        (new XlsxWriter($spreadsheet))->save($outputFile);

        return $outputFile;
    }

    /**
     * @return string[]
     */
    private function zipEntryNames(string $file): array
    {
        $zip = new ZipArchive();
        self::assertTrue($zip->open($file) === true);
        $names = [];
        for ($i = 0; $i < $zip->numFiles; ++$i) {
            $names[] = (string) $zip->getNameIndex($i);
        }
        $zip->close();

        return $names;
    }
}
