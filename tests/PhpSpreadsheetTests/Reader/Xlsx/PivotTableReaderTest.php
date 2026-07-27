<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotField;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotTable;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;
use ZipArchive;

class PivotTableReaderTest extends TestCase
{
    private const FILENAME = 'tests/data/Reader/XLSX/PivotTableSimple.xlsx';

    /** @var string[] */
    private array $tempFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->tempFiles as $tempFile) {
            @unlink($tempFile);
        }
        $this->tempFiles = [];
    }

    public function testPivotTableIsReadIntoModel(): void
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load(self::FILENAME);

        $pivotSheet = $spreadsheet->getSheetByName('Pivot');
        self::assertNotNull($pivotSheet);

        $pivotTables = $pivotSheet->getPivotTableCollection();
        self::assertCount(1, $pivotTables);

        $pivotTable = $pivotTables[0];
        self::assertInstanceOf(PivotTable::class, $pivotTable);
        self::assertSame('PivotTable1', $pivotTable->getName());
        self::assertSame('A3:D9', $pivotTable->getLocation());
        self::assertSame($pivotSheet, $pivotTable->getWorksheet());

        $spreadsheet->disconnectWorksheets();
    }

    public function testPivotCacheDefinitionIsRead(): void
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load(self::FILENAME);

        $pivotTable = $spreadsheet->getSheetByName('Pivot')->getPivotTableByName('PivotTable1');
        self::assertNotNull($pivotTable);

        $cache = $pivotTable->getCacheDefinition();
        self::assertNotNull($cache);
        self::assertSame(1, $cache->getCacheId());
        self::assertSame('Data', $cache->getSourceWorksheet());
        self::assertSame('A1:C5', $cache->getSourceRange());
        self::assertSame(['Region', 'Product', 'Amount'], $cache->getCacheFields());
        self::assertSame('Product', $cache->getCacheFieldName(1));
        self::assertNull($cache->getCacheFieldName(99));

        $spreadsheet->disconnectWorksheets();
    }

    public function testPivotFieldAxisAndDataPlacement(): void
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load(self::FILENAME);

        $pivotTable = $spreadsheet->getSheetByName('Pivot')->getPivotTableByName('PivotTable1');
        self::assertNotNull($pivotTable);

        $fields = $pivotTable->getFields();
        self::assertCount(3, $fields);

        // Region -> row axis
        self::assertSame('Region', $fields[0]->getName());
        self::assertSame(PivotField::AXIS_ROW, $fields[0]->getAxis());
        self::assertFalse($fields[0]->isDataField());

        // Product -> column axis
        self::assertSame('Product', $fields[1]->getName());
        self::assertSame(PivotField::AXIS_COLUMN, $fields[1]->getAxis());
        self::assertFalse($fields[1]->isDataField());

        // Amount -> data/value field, summed
        self::assertSame('Amount', $fields[2]->getName());
        self::assertSame(PivotField::AXIS_NONE, $fields[2]->getAxis());
        self::assertTrue($fields[2]->isDataField());
        self::assertSame('sum', $fields[2]->getSubtotal());

        $spreadsheet->disconnectWorksheets();
    }

    public function testAxisConvenienceAccessors(): void
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load(self::FILENAME);

        $pivotTable = $spreadsheet->getSheetByName('Pivot')->getPivotTableByName('PivotTable1');
        self::assertNotNull($pivotTable);

        $names = static fn (array $fields): array => array_map(
            static fn (PivotField $field): string => $field->getName(),
            $fields
        );

        self::assertSame(['Region'], $names($pivotTable->getRowFields()));
        self::assertSame(['Product'], $names($pivotTable->getColumnFields()));
        self::assertSame([], $names($pivotTable->getPageFields()));
        self::assertSame(['Amount'], $names($pivotTable->getDataFields()));

        self::assertSame(['PivotTable1'], $spreadsheet->getSheetByName('Pivot')->getPivotTableNames());

        $spreadsheet->disconnectWorksheets();
    }

    public function testSheetWithoutPivotTableHasEmptyCollection(): void
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load(self::FILENAME);

        $dataSheet = $spreadsheet->getSheetByName('Data');
        self::assertNotNull($dataSheet);
        self::assertCount(0, $dataSheet->getPivotTableCollection());
        self::assertNull($dataSheet->getPivotTableByName('PivotTable1'));

        $spreadsheet->disconnectWorksheets();
    }

    public function testReadDataOnlySkipsPivotTables(): void
    {
        $reader = new Xlsx();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load(self::FILENAME);

        $pivotSheet = $spreadsheet->getSheetByName('Pivot');
        self::assertNotNull($pivotSheet);
        self::assertCount(0, $pivotSheet->getPivotTableCollection());

        $spreadsheet->disconnectWorksheets();
    }

    public function testPivotPartsPreservedOnRoundTrip(): void
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load(self::FILENAME);
        $outputFile = $this->save($spreadsheet);
        $spreadsheet->disconnectWorksheets();

        $parts = $this->zipEntryNames($outputFile);

        self::assertContains('xl/pivotTables/pivotTable1.xml', $parts);
        self::assertContains('xl/pivotTables/_rels/pivotTable1.xml.rels', $parts);
        self::assertContains('xl/pivotCache/pivotCacheDefinition1.xml', $parts);
        self::assertContains('xl/pivotCache/_rels/pivotCacheDefinition1.xml.rels', $parts);
        self::assertContains('xl/pivotCache/pivotCacheRecords1.xml', $parts);
    }

    public function testRoundTripWiringIsConsistent(): void
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load(self::FILENAME);
        $outputFile = $this->save($spreadsheet);
        $spreadsheet->disconnectWorksheets();

        $zip = new ZipArchive();
        self::assertTrue($zip->open($outputFile) === true);

        $workbook = (string) $zip->getFromName('xl/workbook.xml');
        $workbookRels = (string) $zip->getFromName('xl/_rels/workbook.xml.rels');
        $contentTypes = (string) $zip->getFromName('[Content_Types].xml');
        $zip->close();

        // The pivotCache r:id in workbook.xml must match a relationship id.
        self::assertStringContainsString('<pivotCaches>', $workbook);
        self::assertStringContainsString('r:id="rId_pivotCacheDef_1"', $workbook);
        self::assertStringContainsString('Id="rId_pivotCacheDef_1"', $workbookRels);
        self::assertStringContainsString('pivotCache/pivotCacheDefinition1.xml', $workbookRels);

        // Content types for all three pivot parts must be declared.
        self::assertStringContainsString('spreadsheetml.pivotTable+xml', $contentTypes);
        self::assertStringContainsString('spreadsheetml.pivotCacheDefinition+xml', $contentTypes);
        self::assertStringContainsString('spreadsheetml.pivotCacheRecords+xml', $contentTypes);
    }

    public function testPivotModelSurvivesRoundTrip(): void
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load(self::FILENAME);
        $outputFile = $this->save($spreadsheet);
        $spreadsheet->disconnectWorksheets();

        $reloaded = (new Xlsx())->load($outputFile);
        $pivotTable = $reloaded->getSheetByName('Pivot')->getPivotTableByName('PivotTable1');

        self::assertNotNull($pivotTable);
        self::assertSame('A3:D9', $pivotTable->getLocation());
        self::assertSame('Data', $pivotTable->getCacheDefinition()->getSourceWorksheet());
        self::assertSame('A1:C5', $pivotTable->getCacheDefinition()->getSourceRange());
        self::assertSame(['Region', 'Product', 'Amount'], $pivotTable->getCacheDefinition()->getCacheFields());

        $reloaded->disconnectWorksheets();
    }

    private function save(\PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet): string
    {
        $outputFile = tempnam(sys_get_temp_dir(), 'pivot') . '.xlsx';
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
