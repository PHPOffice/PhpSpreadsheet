<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotField;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotTable;
use PHPUnit\Framework\TestCase;

class PivotTableReaderTest extends TestCase
{
    private const FILENAME = 'tests/data/Reader/XLSX/PivotTableSimple.xlsx';

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
}
