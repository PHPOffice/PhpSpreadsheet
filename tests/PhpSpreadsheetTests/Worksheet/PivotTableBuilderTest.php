<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotField;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotFieldGroup;
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
