<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotCacheDefinition;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotField;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotTable;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use SimpleXMLElement;

/**
 * Reads a pivot table definition (and its associated cache definition) from
 * the Xlsx parts into the read-only PivotTable object model.
 *
 * This only extracts metadata (name, location, source and field layout); the
 * raw pivot XML parts continue to be preserved verbatim for write-back, so
 * loading a pivot table here never changes what is written out.
 */
class PivotTableReader
{
    private Worksheet $worksheet;

    private SimpleXMLElement $pivotTableXml;

    private ?SimpleXMLElement $cacheDefinitionXml;

    public function __construct(
        Worksheet $worksheet,
        SimpleXMLElement $pivotTableXml,
        ?SimpleXMLElement $cacheDefinitionXml = null
    ) {
        $this->worksheet = $worksheet;
        $this->pivotTableXml = $pivotTableXml;
        $this->cacheDefinitionXml = $cacheDefinitionXml;
    }

    /**
     * Parse the pivot table definition and add it to the worksheet.
     */
    public function load(): void
    {
        $attributes = $this->pivotTableXml->attributes();

        $pivotTable = new PivotTable((string) ($attributes['name'] ?? ''));

        if (isset($this->pivotTableXml->location)) {
            $locationAttributes = $this->pivotTableXml->location->attributes();
            $pivotTable->setLocation((string) ($locationAttributes['ref'] ?? ''));
        }

        $cacheDefinition = $this->readCacheDefinition(
            isset($attributes['cacheId']) ? (int) $attributes['cacheId'] : null
        );
        $pivotTable->setCacheDefinition($cacheDefinition);

        if (isset($this->pivotTableXml->pivotFields)) {
            $this->readFields($pivotTable, $cacheDefinition);
        }

        $this->worksheet->addPivotTable($pivotTable);
    }

    /**
     * Build the cache definition (source range + field names) if we have it.
     */
    private function readCacheDefinition(?int $cacheId): PivotCacheDefinition
    {
        $cacheDefinition = new PivotCacheDefinition($cacheId);

        if ($this->cacheDefinitionXml !== null) {
            $source = $this->cacheDefinitionXml->cacheSource;
            if (isset($source->worksheetSource)) {
                $sourceAttributes = $source->worksheetSource->attributes();
                if (isset($sourceAttributes['sheet'])) {
                    $cacheDefinition->setSourceWorksheet((string) $sourceAttributes['sheet']);
                }
                if (isset($sourceAttributes['ref'])) {
                    $cacheDefinition->setSourceRange((string) $sourceAttributes['ref']);
                }
            }

            if (isset($this->cacheDefinitionXml->cacheFields)) {
                foreach ($this->cacheDefinitionXml->cacheFields->cacheField as $cacheField) {
                    $fieldAttributes = $cacheField->attributes();
                    $cacheDefinition->addCacheField((string) ($fieldAttributes['name'] ?? ''));
                }
            }
        }

        return $cacheDefinition;
    }

    /**
     * Read the pivotFields and the axis/data placement sections into fields.
     */
    private function readFields(PivotTable $pivotTable, PivotCacheDefinition $cacheDefinition): void
    {
        $index = 0;
        /** @var PivotField[] $fields */
        $fields = [];
        foreach ($this->pivotTableXml->pivotFields->pivotField as $pivotFieldXml) {
            $field = new PivotField($index, (string) ($cacheDefinition->getCacheFieldName($index) ?? ''));

            $fieldAttributes = $pivotFieldXml->attributes();
            if (isset($fieldAttributes['axis'])) {
                $field->setAxis((string) $fieldAttributes['axis']);
            }

            $fields[$index] = $field;
            $pivotTable->addField($field);
            ++$index;
        }

        $this->markDataFields($fields);
    }

    /**
     * Flag the fields referenced by <dataFields> as value fields, and record
     * their aggregation function.
     *
     * @param PivotField[] $fields keyed by field index
     */
    private function markDataFields(array $fields): void
    {
        if (isset($this->pivotTableXml->dataFields)) {
            foreach ($this->pivotTableXml->dataFields->dataField as $dataFieldXml) {
                $dataAttributes = $dataFieldXml->attributes();
                if (isset($dataAttributes['fld'])) {
                    $fieldIndex = (int) $dataAttributes['fld'];
                    if (isset($fields[$fieldIndex])) {
                        $fields[$fieldIndex]->setDataField(true);
                        // "sum" is the default subtotal when the attribute is absent.
                        $fields[$fieldIndex]->setSubtotal(
                            isset($dataAttributes['subtotal']) ? (string) $dataAttributes['subtotal'] : 'sum'
                        );
                    }
                }
            }
        }
    }
}
