<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotCacheDefinition;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotDataField;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotField;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotFieldGroup;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotTable;
use PHPUnit\Framework\TestCase;

class PivotTableModelTest extends TestCase
{
    public function testNumericFieldGroupAccessors(): void
    {
        $group = PivotFieldGroup::numeric(10.0, 0.0, 100.0);

        self::assertSame(PivotFieldGroup::TYPE_NUMERIC, $group->getType());
        self::assertTrue($group->isNumeric());
        self::assertFalse($group->isDate());
        self::assertSame(10.0, $group->getInterval());
        self::assertSame(0.0, $group->getStartNum());
        self::assertSame(100.0, $group->getEndNum());
        self::assertSame([], $group->getGroupBy());
        self::assertNull($group->getStartDate());
        self::assertNull($group->getEndDate());
    }

    public function testNumericFieldGroupDefaults(): void
    {
        $group = PivotFieldGroup::numeric(5.0);

        self::assertSame(5.0, $group->getInterval());
        self::assertNull($group->getStartNum());
        self::assertNull($group->getEndNum());
    }

    public function testDateFieldGroupWithSingleUnit(): void
    {
        $group = PivotFieldGroup::date(PivotFieldGroup::GROUP_BY_MONTHS);

        self::assertSame(PivotFieldGroup::TYPE_DATE, $group->getType());
        self::assertTrue($group->isDate());
        self::assertFalse($group->isNumeric());
        self::assertSame([PivotFieldGroup::GROUP_BY_MONTHS], $group->getGroupBy());
        self::assertNull($group->getStartDate());
        self::assertNull($group->getEndDate());
    }

    public function testDateFieldGroupWithMultipleUnitsAndBounds(): void
    {
        $group = PivotFieldGroup::date(
            [PivotFieldGroup::GROUP_BY_YEARS, PivotFieldGroup::GROUP_BY_QUARTERS],
            '2024-01-01',
            '2025-12-31'
        );

        self::assertSame(
            [PivotFieldGroup::GROUP_BY_YEARS, PivotFieldGroup::GROUP_BY_QUARTERS],
            $group->getGroupBy()
        );
        self::assertSame('2024-01-01', $group->getStartDate());
        self::assertSame('2025-12-31', $group->getEndDate());
    }

    public function testCacheDefinitionFieldGroup(): void
    {
        $cache = new PivotCacheDefinition(1);
        self::assertNull($cache->getFieldGroup('Age'));

        $group = PivotFieldGroup::numeric(10.0);
        self::assertSame($cache, $cache->setFieldGroup('Age', $group));
        self::assertSame($group, $cache->getFieldGroup('Age'));
    }

    public function testPivotTableToStringReturnsName(): void
    {
        $pivotTable = new PivotTable('MyPivot');

        self::assertSame('MyPivot', (string) $pivotTable);
        self::assertSame('MyPivot', $pivotTable->__toString());
    }

    public function testWorksheetPivotTableCollection(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        self::assertCount(0, $worksheet->getPivotTableCollection());
        self::assertSame([], $worksheet->getPivotTableNames());
        self::assertNull($worksheet->getPivotTableByName('Missing'));

        $pivotTable = new PivotTable('First');
        self::assertSame($worksheet, $worksheet->addPivotTable($pivotTable));

        // addPivotTable attaches the worksheet to the pivot table.
        self::assertSame($worksheet, $pivotTable->getWorksheet());

        // getPivotTables() is an alias of getPivotTableCollection().
        self::assertCount(1, $worksheet->getPivotTables());
        self::assertSame(['First'], $worksheet->getPivotTableNames());
        self::assertSame($pivotTable, $worksheet->getPivotTableByName('First'));
        // Name lookup is case-insensitive.
        self::assertSame($pivotTable, $worksheet->getPivotTableByName('FIRST'));

        self::assertSame($worksheet, $worksheet->removePivotTableCollection());
        self::assertCount(0, $worksheet->getPivotTableCollection());

        $spreadsheet->disconnectWorksheets();
    }

    public function testPivotFieldSetters(): void
    {
        $field = new PivotField(2, 'Amount');

        self::assertSame(2, $field->getIndex());
        self::assertSame('Amount', $field->getName());

        $field->setName('Total')
            ->setAxis(PivotField::AXIS_VALUES)
            ->setDataField(true)
            ->setSubtotal('count')
            ->setDataFieldCaption('Count of Amount');

        self::assertSame('Total', $field->getName());
        self::assertSame(PivotField::AXIS_VALUES, $field->getAxis());
        self::assertTrue($field->isDataField());
        self::assertSame('count', $field->getSubtotal());
        self::assertSame('Count of Amount', $field->getDataFieldCaption());
    }

    /**
     * A pivot table assembled by hand never calls addDataFieldDefinition(),
     * so getDataFieldDefinitions() has to derive the list from the fields
     * flagged as data fields. This is the path taken by any caller building
     * the model directly rather than through PivotTableBuilder or the reader.
     */
    public function testDataFieldDefinitionsDerivedFromFieldsWhenNoneRegistered(): void
    {
        $pivotTable = new PivotTable('Manual');

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

        $definitions = $pivotTable->getDataFieldDefinitions();

        self::assertCount(2, $definitions);

        self::assertSame(1, $definitions[0]->getFieldIndex());
        self::assertSame('Amount', $definitions[0]->getName());
        self::assertSame(PivotField::SUBTOTAL_SUM, $definitions[0]->getSubtotal());
        self::assertSame('Sum of Amount', $definitions[0]->getCaption());

        self::assertSame(2, $definitions[1]->getFieldIndex());
        self::assertSame('Quantity', $definitions[1]->getName());
        self::assertSame(PivotField::SUBTOTAL_COUNT, $definitions[1]->getSubtotal());
        self::assertSame('Count of Quantity', $definitions[1]->getCaption());
    }

    /**
     * The derived list reflects only the fields flagged as data fields, and
     * is empty when there are none.
     */
    public function testDataFieldDefinitionsDerivedListExcludesNonDataFields(): void
    {
        $pivotTable = new PivotTable('Manual');

        $region = new PivotField(0, 'Region');
        $region->setAxis(PivotField::AXIS_ROW);
        $pivotTable->addField($region);

        self::assertSame([], $pivotTable->getDataFieldDefinitions());
    }

    /**
     * An explicitly registered list wins over the derived one, so a source
     * column aggregated twice keeps both entries instead of collapsing to the
     * single pivot field that represents that column.
     */
    public function testRegisteredDataFieldDefinitionsTakePrecedence(): void
    {
        $pivotTable = new PivotTable('Explicit');

        $amount = new PivotField(1, 'Amount');
        $amount->setDataField(true);
        $amount->setSubtotal(PivotField::SUBTOTAL_SUM);
        $amount->setDataFieldCaption('Sum of Amount');
        $pivotTable->addField($amount);

        // One pivot field, two aggregations of it.
        self::assertCount(1, $pivotTable->getDataFieldDefinitions());

        $pivotTable
            ->addDataFieldDefinition(new PivotDataField(1, 'Amount', PivotField::SUBTOTAL_SUM, 'Sum of Amount'))
            ->addDataFieldDefinition(new PivotDataField(1, 'Amount', PivotField::SUBTOTAL_AVERAGE, 'Average of Amount'));

        $definitions = $pivotTable->getDataFieldDefinitions();
        self::assertCount(2, $definitions);
        self::assertSame(PivotField::SUBTOTAL_SUM, $definitions[0]->getSubtotal());
        self::assertSame(PivotField::SUBTOTAL_AVERAGE, $definitions[1]->getSubtotal());
        // Both refer to the same source column.
        self::assertSame(1, $definitions[0]->getFieldIndex());
        self::assertSame(1, $definitions[1]->getFieldIndex());
    }

    public function testPivotDataFieldSetters(): void
    {
        $dataField = new PivotDataField(3, 'Amount');

        self::assertSame(3, $dataField->getFieldIndex());
        self::assertSame('Amount', $dataField->getName());
        self::assertNull($dataField->getSubtotal());
        self::assertNull($dataField->getCaption());

        $dataField->setName('Total')
            ->setSubtotal(PivotField::SUBTOTAL_AVERAGE)
            ->setCaption('Average of Amount');

        self::assertSame('Total', $dataField->getName());
        self::assertSame(PivotField::SUBTOTAL_AVERAGE, $dataField->getSubtotal());
        self::assertSame('Average of Amount', $dataField->getCaption());
    }
}
