<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotField;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotTableBuilder;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
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

    public function testBuildProducesGeneratedModel(): void
    {
        $spreadsheet = $this->sampleSpreadsheet();
        $data = $spreadsheet->getSheetByName('Data');
        $pivotSheet = $spreadsheet->getSheetByName('Pivot');

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
        $builder = new PivotTableBuilder($spreadsheet->getSheetByName('Data'), 'A1:C5');
        $builder->addRowField('Region');

        $this->expectException(PhpSpreadsheetException::class);
        $builder->build($spreadsheet->getSheetByName('Pivot'), 'A3');
    }

    public function testBuildRejectsUnknownField(): void
    {
        $spreadsheet = $this->sampleSpreadsheet();
        $builder = new PivotTableBuilder($spreadsheet->getSheetByName('Data'), 'A1:C5');

        $this->expectException(PhpSpreadsheetException::class);
        $builder->addRowField('Nonexistent');
    }

    public function testGeneratedPivotIsWrittenAndReadableAgain(): void
    {
        $spreadsheet = $this->sampleSpreadsheet();
        $builder = new PivotTableBuilder($spreadsheet->getSheetByName('Data'), 'A1:C5');
        $builder
            ->addRowField('Region')
            ->addColumnField('Product')
            ->addDataField('Amount', PivotField::SUBTOTAL_SUM)
            ->build($spreadsheet->getSheetByName('Pivot'), 'A3', 'SalesPivot');

        $outputFile = $this->save($spreadsheet);

        $parts = $this->zipEntryNames($outputFile);
        self::assertContains('xl/pivotTables/pivotTable1.xml', $parts);
        self::assertContains('xl/pivotCache/pivotCacheDefinition1.xml', $parts);
        self::assertContains('xl/pivotCache/pivotCacheRecords1.xml', $parts);

        $reloaded = (new XlsxReader())->load($outputFile);
        $pivotTable = $reloaded->getSheetByName('Pivot')->getPivotTableByName('SalesPivot');
        self::assertNotNull($pivotTable);
        self::assertSame('Data', $pivotTable->getCacheDefinition()->getSourceWorksheet());
        self::assertSame('A1:C5', $pivotTable->getCacheDefinition()->getSourceRange());
        self::assertSame(['Region'], $this->fieldNames($pivotTable->getRowFields()));
        self::assertSame(['Amount'], $this->fieldNames($pivotTable->getDataFields()));

        $reloaded->disconnectWorksheets();
    }

    public function testGeneratedPivotWiringIsConsistent(): void
    {
        $spreadsheet = $this->sampleSpreadsheet();
        $builder = new PivotTableBuilder($spreadsheet->getSheetByName('Data'), 'A1:C5');
        $builder
            ->addRowField('Region')
            ->addDataField('Amount', PivotField::SUBTOTAL_SUM)
            ->build($spreadsheet->getSheetByName('Pivot'), 'A3', 'SalesPivot');

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
        $builder = new PivotTableBuilder($spreadsheet->getSheetByName('Data'), 'A1:C5');
        $builder
            ->addRowField('Region')
            ->addDataField('Amount', PivotField::SUBTOTAL_AVERAGE)
            ->build($spreadsheet->getSheetByName('Pivot'), 'A3', 'AvgPivot');

        $outputFile = $this->save($spreadsheet);

        $zip = new ZipArchive();
        self::assertTrue($zip->open($outputFile) === true);
        $definition = (string) $zip->getFromName('xl/pivotTables/pivotTable1.xml');
        $zip->close();

        self::assertStringContainsString('subtotal="average"', $definition);
        self::assertStringContainsString('name="Average of Amount"', $definition);
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
