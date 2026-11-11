<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Namespaces;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotCacheDefinition;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotField;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotFieldGroup;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotTable as WorksheetPivotTable;

/**
 * Generates the OOXML parts for a pivot table that was built in memory.
 *
 * The parts are written with refreshOnLoad set on the cache definition, so the
 * spreadsheet application computes the actual value cells (and expands the axis
 * items) when the workbook is opened. This keeps the generated XML small and
 * avoids having to replicate the aggregation engine.
 */
class PivotTable
{
    /**
     * Reserved OOXML field index for the virtual "Values" field, which carries
     * the data fields on an axis when more than one of them is present.
     */
    private const VALUES_FIELD_INDEX = -2;

    /**
     * Build the pivotTableDefinition part.
     *
     * @param int $cacheId the workbook-level cache id referenced by this table
     */
    public static function writeDefinition(WorksheetPivotTable $pivotTable, int $cacheId): string
    {
        $objWriter = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        $objWriter->startElement('pivotTableDefinition');
        $objWriter->writeAttribute('xmlns', Namespaces::MAIN);
        $objWriter->writeAttribute('name', $pivotTable->getName());
        $objWriter->writeAttribute('cacheId', (string) $cacheId);
        $objWriter->writeAttribute('applyNumberFormats', '0');
        $objWriter->writeAttribute('applyBorderFormats', '0');
        $objWriter->writeAttribute('applyFontFormats', '0');
        $objWriter->writeAttribute('applyPatternFormats', '0');
        $objWriter->writeAttribute('applyAlignmentFormats', '0');
        $objWriter->writeAttribute('applyWidthHeightFormats', '1');
        $objWriter->writeAttribute('dataCaption', 'Values');
        $objWriter->writeAttribute('updatedVersion', '6');
        $objWriter->writeAttribute('minRefreshableVersion', '3');
        $objWriter->writeAttribute('useAutoFormatting', '1');
        $objWriter->writeAttribute('itemPrintTitles', '1');
        $objWriter->writeAttribute('createdVersion', '6');
        $objWriter->writeAttribute('indent', '0');
        $objWriter->writeAttribute('outline', '1');
        $objWriter->writeAttribute('outlineData', '1');
        $objWriter->writeAttribute('multipleFieldFilters', '0');

        // location. The precise extent is recomputed on refresh; firstDataRow
        // leaves room for the column-field header row when one is present.
        $hasColumnFields = $pivotTable->getColumnFields() !== [];
        $objWriter->startElement('location');
        $objWriter->writeAttribute('ref', $pivotTable->getLocation());
        $objWriter->writeAttribute('firstHeaderRow', '1');
        $objWriter->writeAttribute('firstDataRow', $hasColumnFields ? '2' : '1');
        $objWriter->writeAttribute('firstDataCol', '1');
        $objWriter->endElement();

        self::writePivotFields($objWriter, $pivotTable);

        // Field indices per axis. More than one data field is laid out across
        // the column axis under the virtual "Values" field, which OOXML
        // addresses with the reserved index -2; without it Excel reports the
        // workbook as damaged and drops the pivot table.
        $rowIndices = self::axisFieldIndices($pivotTable->getRowFields());
        $columnIndices = self::axisFieldIndices($pivotTable->getColumnFields());
        // Counted from the <dataFields> entries, not the fields flagged as
        // data fields: one source column aggregated twice is two entries.
        $dataFieldCount = count($pivotTable->getDataFieldDefinitions());
        if ($dataFieldCount > 1) {
            $columnIndices[] = self::VALUES_FIELD_INDEX;
        }

        // The item list is mandatory whenever an axis declares fields: Excel
        // discards the whole pivot table when it is missing.
        self::writeAxisFields($objWriter, 'rowFields', $rowIndices);
        self::writeAxisItems($objWriter, 'rowItems', $rowIndices, 1);
        self::writeAxisFields($objWriter, 'colFields', $columnIndices);
        self::writeAxisItems($objWriter, 'colItems', $columnIndices, $dataFieldCount);
        self::writePageFields($objWriter, $pivotTable->getPageFields());
        self::writeDataFields($objWriter, $pivotTable);

        // Style
        $objWriter->startElement('pivotTableStyleInfo');
        $objWriter->writeAttribute('name', 'PivotStyleLight16');
        $objWriter->writeAttribute('showRowHeaders', '1');
        $objWriter->writeAttribute('showColHeaders', '1');
        $objWriter->writeAttribute('showRowStripes', '0');
        $objWriter->writeAttribute('showColStripes', '0');
        $objWriter->writeAttribute('showLastColumn', '1');
        $objWriter->endElement();

        $objWriter->endElement(); // pivotTableDefinition

        return $objWriter->getData();
    }

    /**
     * Build the pivotCacheDefinition part, referencing its records by rel id.
     */
    public static function writeCacheDefinition(WorksheetPivotTable $pivotTable): string
    {
        $cache = $pivotTable->getCacheDefinition();

        $objWriter = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        $objWriter->startElement('pivotCacheDefinition');
        $objWriter->writeAttribute('xmlns', Namespaces::MAIN);
        $objWriter->writeAttribute('xmlns:r', Namespaces::SCHEMA_OFFICE_DOCUMENT);
        $objWriter->writeAttribute('r:id', 'rId1');
        $objWriter->writeAttribute('refreshOnLoad', '1');
        $objWriter->writeAttribute('refreshedBy', 'PhpSpreadsheet');
        $objWriter->writeAttribute('createdVersion', '6');
        $objWriter->writeAttribute('refreshedVersion', '6');
        $objWriter->writeAttribute('minRefreshableVersion', '3');
        $objWriter->writeAttribute('recordCount', '0');

        // cacheSource
        $objWriter->startElement('cacheSource');
        $objWriter->writeAttribute('type', 'worksheet');
        $objWriter->startElement('worksheetSource');
        $objWriter->writeAttribute('ref', (string) $cache?->getSourceRange());
        $objWriter->writeAttribute('sheet', (string) $cache?->getSourceWorksheet());
        $objWriter->endElement();
        $objWriter->endElement();

        // cacheFields
        $cacheFields = $cache?->getCacheFields() ?? [];
        $objWriter->startElement('cacheFields');
        $objWriter->writeAttribute('count', (string) count($cacheFields));
        foreach ($pivotTable->getFields() as $field) {
            $fieldName = $field->getName();
            $group = $cache?->getFieldGroup($fieldName);
            $objWriter->startElement('cacheField');
            $objWriter->writeAttribute('name', $fieldName);
            $objWriter->writeAttribute('numFmtId', '0');

            if ($group !== null) {
                self::writeGroupedCacheField($objWriter, $group);
            } elseif ($field->isDataField()) {
                // Numeric value field: describe as containing numbers.
                $objWriter->startElement('sharedItems');
                $objWriter->writeAttribute('containsSemiMixedTypes', '0');
                $objWriter->writeAttribute('containsString', '0');
                $objWriter->writeAttribute('containsNumber', '1');
                $objWriter->endElement();
            } else {
                $items = $cache?->getSharedItems($fieldName) ?? [];
                $objWriter->startElement('sharedItems');
                $objWriter->writeAttribute('count', (string) count($items));
                foreach ($items as $item) {
                    $objWriter->startElement('s');
                    $objWriter->writeAttribute('v', $item);
                    $objWriter->endElement();
                }
                $objWriter->endElement();
            }

            $objWriter->endElement(); // cacheField
        }
        $objWriter->endElement(); // cacheFields

        $objWriter->endElement(); // pivotCacheDefinition

        return $objWriter->getData();
    }

    /**
     * Emit the <sharedItems> and <fieldGroup> for a grouped cache field.
     */
    private static function writeGroupedCacheField(XMLWriter $objWriter, PivotFieldGroup $group): void
    {
        if ($group->isDate()) {
            self::writeDateGroup($objWriter, $group);
        } else {
            self::writeNumericGroup($objWriter, $group);
        }
    }

    private static function writeNumericGroup(XMLWriter $objWriter, PivotFieldGroup $group): void
    {
        [$start, $end, $interval] = $group->effectiveNumericRange();

        // Source values are numeric.
        $objWriter->startElement('sharedItems');
        $objWriter->writeAttribute('containsSemiMixedTypes', '0');
        $objWriter->writeAttribute('containsString', '0');
        $objWriter->writeAttribute('containsNumber', '1');
        $objWriter->writeAttribute('minValue', self::num($start));
        $objWriter->writeAttribute('maxValue', self::num($end));
        $objWriter->endElement();

        $objWriter->startElement('fieldGroup');
        $objWriter->startElement('rangePr');
        $objWriter->writeAttribute('groupInterval', self::num($interval));
        $objWriter->writeAttribute('startNum', self::num($start));
        $objWriter->writeAttribute('endNum', self::num($end));
        $objWriter->endElement();

        // Group items: "<lower>-<upper>" buckets plus the sentinel bounds Excel
        // expects (values below the start and above the end).
        $buckets = $group->numericBuckets();
        $objWriter->startElement('groupItems');
        $objWriter->writeAttribute('count', (string) (count($buckets) + 2));
        self::writeGroupItem($objWriter, '<' . self::num($start));
        foreach ($buckets as $bucket) {
            self::writeGroupItem($objWriter, $bucket);
        }
        self::writeGroupItem($objWriter, '>' . self::num($end));
        $objWriter->endElement(); // groupItems

        $objWriter->endElement(); // fieldGroup
    }

    /**
     * Excel sentinel bounds for an auto-ranged date grouping. These match the
     * out-of-range group item labels ("<1/1/1900" and ">12/31/9999") that the
     * spreadsheet application expects for every date field group.
     */
    private const DATE_GROUP_MIN = '1900-01-01T00:00:00';
    private const DATE_GROUP_MAX = '9999-12-31T00:00:00';

    private static function writeDateGroup(XMLWriter $objWriter, PivotFieldGroup $group): void
    {
        // Excel groups by a single unit per field; use the first requested unit.
        $groupBy = $group->effectiveGroupBy();

        // A date field group is only valid when its bounds are present: the
        // sharedItems must carry minDate/maxDate and the rangePr must carry
        // startDate/endDate. When the caller did not supply a range, fall back
        // to Excel's sentinel bounds so the workbook is not reported as corrupt.
        $startDate = $group->getStartDate() ?? self::DATE_GROUP_MIN;
        $endDate = $group->getEndDate() ?? self::DATE_GROUP_MAX;

        $objWriter->startElement('sharedItems');
        $objWriter->writeAttribute('containsSemiMixedTypes', '0');
        $objWriter->writeAttribute('containsNonDate', '0');
        $objWriter->writeAttribute('containsDate', '1');
        $objWriter->writeAttribute('containsString', '0');
        $objWriter->writeAttribute('minDate', $startDate);
        $objWriter->writeAttribute('maxDate', $endDate);
        $objWriter->endElement();

        $objWriter->startElement('fieldGroup');
        $objWriter->startElement('rangePr');
        $objWriter->writeAttribute('groupBy', $groupBy);
        $objWriter->writeAttribute('startDate', $startDate);
        $objWriter->writeAttribute('endDate', $endDate);
        $objWriter->endElement();

        // Provide the standard group item labels for the chosen unit; the
        // spreadsheet application rebuilds the exact set on refresh.
        $items = $group->dateGroupItems();
        $objWriter->startElement('groupItems');
        $objWriter->writeAttribute('count', (string) count($items));
        foreach ($items as $item) {
            self::writeGroupItem($objWriter, $item);
        }
        $objWriter->endElement();

        $objWriter->endElement(); // fieldGroup
    }

    private static function writeGroupItem(XMLWriter $objWriter, string $value): void
    {
        $objWriter->startElement('s');
        $objWriter->writeAttribute('v', $value);
        $objWriter->endElement();
    }

    /**
     * Format a float without a trailing ".0" for whole numbers.
     */
    private static function num(float $value): string
    {
        if ($value === floor($value) && abs($value) < 1.0e15) {
            return (string) (int) $value;
        }

        return (string) $value;
    }

    /**
     * Build the (empty) pivotCacheRecords part. Records are regenerated by the
     * spreadsheet application on refresh.
     */
    public static function writeCacheRecords(): string
    {
        $objWriter = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        $objWriter->startElement('pivotCacheRecords');
        $objWriter->writeAttribute('xmlns', Namespaces::MAIN);
        $objWriter->writeAttribute('xmlns:r', Namespaces::SCHEMA_OFFICE_DOCUMENT);
        $objWriter->writeAttribute('count', '0');
        $objWriter->endElement();

        return $objWriter->getData();
    }

    /**
     * Rels for the pivot table part -> its cache definition.
     */
    public static function writeTableRelationships(int $cacheIndex): string
    {
        $objWriter = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');
        $objWriter->startElement('Relationships');
        $objWriter->writeAttribute('xmlns', Namespaces::RELATIONSHIPS);
        $objWriter->startElement('Relationship');
        $objWriter->writeAttribute('Id', 'rId1');
        $objWriter->writeAttribute('Type', Namespaces::RELATIONSHIPS_PIVOT_CACHE_DEFINITION);
        $objWriter->writeAttribute('Target', "../pivotCache/pivotCacheDefinition{$cacheIndex}.xml");
        $objWriter->endElement();
        $objWriter->endElement();

        return $objWriter->getData();
    }

    /**
     * Rels for the cache definition part -> its records.
     */
    public static function writeCacheRelationships(int $cacheIndex): string
    {
        $objWriter = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');
        $objWriter->startElement('Relationships');
        $objWriter->writeAttribute('xmlns', Namespaces::RELATIONSHIPS);
        $objWriter->startElement('Relationship');
        $objWriter->writeAttribute('Id', 'rId1');
        $objWriter->writeAttribute('Type', Namespaces::RELATIONSHIPS_PIVOT_CACHE_RECORDS);
        $objWriter->writeAttribute('Target', "pivotCacheRecords{$cacheIndex}.xml");
        $objWriter->endElement();
        $objWriter->endElement();

        return $objWriter->getData();
    }

    private static function writePivotFields(XMLWriter $objWriter, WorksheetPivotTable $pivotTable): void
    {
        $fields = $pivotTable->getFields();
        $cache = $pivotTable->getCacheDefinition();
        $objWriter->startElement('pivotFields');
        $objWriter->writeAttribute('count', (string) count($fields));

        foreach ($fields as $field) {
            $objWriter->startElement('pivotField');
            if ($field->isDataField()) {
                $objWriter->writeAttribute('dataField', '1');
                $objWriter->writeAttribute('showAll', '0');
            } elseif ($field->getAxis() !== PivotField::AXIS_NONE) {
                $objWriter->writeAttribute('axis', $field->getAxis());
                $objWriter->writeAttribute('showAll', '0');

                $itemCount = self::getFieldItemCount($cache, $field->getName());
                $objWriter->startElement('items');
                $objWriter->writeAttribute('count', (string) ($itemCount + 1));
                for ($i = 0; $i < $itemCount; ++$i) {
                    $objWriter->startElement('item');
                    $objWriter->writeAttribute('x', (string) $i);
                    $objWriter->endElement();
                }
                $objWriter->startElement('item');
                $objWriter->writeAttribute('t', 'default');
                $objWriter->endElement();
                $objWriter->endElement();
            } else {
                $objWriter->writeAttribute('showAll', '0');
            }
            $objWriter->endElement();
        }

        $objWriter->endElement();
    }

    private static function getFieldItemCount(?PivotCacheDefinition $cache, string $fieldName): int
    {
        if ($cache === null) {
            return 0;
        }

        $group = $cache->getFieldGroup($fieldName);
        if ($group !== null) {
            // The group owns the enumeration, so the <items> here and the
            // <groupItems> in the cache definition cannot drift apart.
            return $group->renderedItemCount();
        }

        return min(count($cache->getSharedItems($fieldName)), PivotFieldGroup::MAX_ITEMS);
    }

    /**
     * The field indices of an axis, in order.
     *
     * @param PivotField[] $fields
     *
     * @return int[]
     */
    private static function axisFieldIndices(array $fields): array
    {
        return array_map(
            static fn (PivotField $field): int => $field->getIndex(),
            $fields
        );
    }

    /**
     * @param int[] $fieldIndices
     */
    private static function writeAxisFields(XMLWriter $objWriter, string $element, array $fieldIndices): void
    {
        if ($fieldIndices === []) {
            return;
        }

        $objWriter->startElement($element);
        $objWriter->writeAttribute('count', (string) count($fieldIndices));
        foreach ($fieldIndices as $fieldIndex) {
            $objWriter->startElement('field');
            $objWriter->writeAttribute('x', (string) $fieldIndex);
            $objWriter->endElement();
        }
        $objWriter->endElement();
    }

    /**
     * Emit the <rowItems>/<colItems> list that accompanies an axis.
     *
     * The real item rows are rebuilt when the application refreshes the pivot
     * table, so only the minimum the schema accepts is written here. A grand
     * total carries no per-field item index, so it is a single <i t="grand">
     * with one <x/>, however many fields the axis declares.
     *
     * When the virtual "Values" field puts several data fields on the axis,
     * each of them needs its own entry ahead of that grand total, indexed by
     * its position in <dataFields>.
     *
     * @param int[] $fieldIndices
     * @param int $dataFieldCount number of data fields laid out on this axis
     */
    private static function writeAxisItems(XMLWriter $objWriter, string $element, array $fieldIndices, int $dataFieldCount): void
    {
        if ($fieldIndices === []) {
            return;
        }

        $valuesOnAxis = in_array(self::VALUES_FIELD_INDEX, $fieldIndices, true) && $dataFieldCount > 1;
        $entries = $valuesOnAxis ? $dataFieldCount : 1;

        $objWriter->startElement($element);
        $objWriter->writeAttribute('count', (string) $entries);

        if ($valuesOnAxis) {
            // One entry per data field: the first defaults to index 0, the
            // rest repeat the previous entry's fields (i="1") and name their
            // own data-field index.
            for ($i = 0; $i < $dataFieldCount; ++$i) {
                $objWriter->startElement('i');
                if ($i > 0) {
                    $objWriter->writeAttribute('i', '1');
                }
                $objWriter->startElement('x');
                if ($i > 0) {
                    $objWriter->writeAttribute('v', (string) $i);
                }
                $objWriter->endElement(); // x
                $objWriter->endElement(); // i
            }
        } else {
            $objWriter->startElement('i');
            $objWriter->writeAttribute('t', 'grand');
            $objWriter->startElement('x');
            $objWriter->endElement();
            $objWriter->endElement(); // i
        }

        $objWriter->endElement(); // rowItems/colItems
    }

    /**
     * @param PivotField[] $fields
     */
    private static function writePageFields(XMLWriter $objWriter, array $fields): void
    {
        if ($fields !== []) {
            $objWriter->startElement('pageFields');
            $objWriter->writeAttribute('count', (string) count($fields));
            foreach ($fields as $field) {
                $objWriter->startElement('pageField');
                $objWriter->writeAttribute('fld', (string) $field->getIndex());
                // No item selected -> "(All)"; the hierarchy attribute is required.
                $objWriter->writeAttribute('hier', '-1');
                $objWriter->endElement();
            }
            $objWriter->endElement();
        }
    }

    private static function writeDataFields(XMLWriter $objWriter, WorksheetPivotTable $pivotTable): void
    {
        // One entry per aggregation: the same fld may appear more than once.
        $dataFields = $pivotTable->getDataFieldDefinitions();
        if ($dataFields === []) {
            return;
        }

        $objWriter->startElement('dataFields');
        $objWriter->writeAttribute('count', (string) count($dataFields));
        foreach ($dataFields as $dataField) {
            $objWriter->startElement('dataField');
            $caption = $dataField->getCaption();
            if ($caption !== null && $caption !== '') {
                $objWriter->writeAttribute('name', $caption);
            }
            $objWriter->writeAttribute('fld', (string) $dataField->getFieldIndex());
            $subtotal = $dataField->getSubtotal();
            if ($subtotal !== null && $subtotal !== PivotField::SUBTOTAL_SUM) {
                $objWriter->writeAttribute('subtotal', $subtotal);
            }
            $objWriter->writeAttribute('baseField', '0');
            $objWriter->writeAttribute('baseItem', '0');
            $objWriter->endElement();
        }
        $objWriter->endElement();
    }
}
