<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Namespaces;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotCacheDefinition;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotField;
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
        self::writeAxisFields($objWriter, 'rowFields', $pivotTable->getRowFields());
        self::writeAxisFields($objWriter, 'colFields', $pivotTable->getColumnFields());
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
            $objWriter->startElement('cacheField');
            $objWriter->writeAttribute('name', $fieldName);
            $objWriter->writeAttribute('numFmtId', '0');

            if ($field->isDataField()) {
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
                // items are rebuilt on refresh; emit the default placeholder.
                $objWriter->startElement('items');
                $objWriter->writeAttribute('count', '1');
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

    /**
     * @param PivotField[] $fields
     */
    private static function writeAxisFields(XMLWriter $objWriter, string $element, array $fields): void
    {
        if ($fields === []) {
            return;
        }

        $objWriter->startElement($element);
        $objWriter->writeAttribute('count', (string) count($fields));
        foreach ($fields as $field) {
            $objWriter->startElement('field');
            $objWriter->writeAttribute('x', (string) $field->getIndex());
            $objWriter->endElement();
        }
        $objWriter->endElement();
    }

    private static function writeDataFields(XMLWriter $objWriter, WorksheetPivotTable $pivotTable): void
    {
        $dataFields = $pivotTable->getDataFields();
        if ($dataFields === []) {
            return;
        }

        $objWriter->startElement('dataFields');
        $objWriter->writeAttribute('count', (string) count($dataFields));
        foreach ($dataFields as $field) {
            $objWriter->startElement('dataField');
            $caption = $field->getDataFieldCaption();
            if ($caption !== null && $caption !== '') {
                $objWriter->writeAttribute('name', $caption);
            }
            $objWriter->writeAttribute('fld', (string) $field->getIndex());
            $subtotal = $field->getSubtotal();
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
