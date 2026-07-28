<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotCacheDefinition;
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
}
