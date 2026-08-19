<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet\PivotTable;

/**
 * Read-only representation of a pivot cache definition.
 *
 * The pivot cache describes where a pivot table's data comes from (the source
 * worksheet and range) and the list of fields available from that source. A
 * single cache can be shared by more than one pivot table.
 *
 * @see PivotTable
 */
class PivotCacheDefinition
{
    /**
     * The cache id, as registered in the workbook <pivotCaches> element.
     */
    private ?int $cacheId;

    /**
     * Name of the worksheet the source data lives on (worksheet source only).
     */
    private ?string $sourceWorksheet = null;

    /**
     * Source data range on that worksheet, e.g. "A1:D50" (worksheet source only).
     */
    private ?string $sourceRange = null;

    /**
     * Names of the cache fields, in declaration order. The index into this
     * array is the field index referenced by the pivot table.
     *
     * @var string[]
     */
    private array $cacheFields = [];

    /**
     * Distinct values ("shared items") per field name, used when generating a
     * cache definition for a newly created pivot table.
     *
     * @var array<string, string[]>
     */
    private array $sharedItems = [];

    /**
     * Grouping configuration per field name, when a field is grouped.
     *
     * @var array<string, PivotFieldGroup>
     */
    private array $fieldGroups = [];

    public function __construct(?int $cacheId = null)
    {
        $this->cacheId = $cacheId;
    }

    public function getCacheId(): ?int
    {
        return $this->cacheId;
    }

    public function setCacheId(?int $cacheId): self
    {
        $this->cacheId = $cacheId;

        return $this;
    }

    public function getSourceWorksheet(): ?string
    {
        return $this->sourceWorksheet;
    }

    public function setSourceWorksheet(?string $sourceWorksheet): self
    {
        $this->sourceWorksheet = $sourceWorksheet;

        return $this;
    }

    public function getSourceRange(): ?string
    {
        return $this->sourceRange;
    }

    public function setSourceRange(?string $sourceRange): self
    {
        $this->sourceRange = $sourceRange;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getCacheFields(): array
    {
        return $this->cacheFields;
    }

    public function addCacheField(string $name): self
    {
        $this->cacheFields[] = $name;

        return $this;
    }

    /**
     * @param string[] $cacheFields
     */
    public function setCacheFields(array $cacheFields): self
    {
        $this->cacheFields = array_values($cacheFields);

        return $this;
    }

    /**
     * Return the name of the cache field at the given zero-based index, or null.
     */
    public function getCacheFieldName(int $index): ?string
    {
        return $this->cacheFields[$index] ?? null;
    }

    /**
     * Set the distinct values recorded for a field (used when generating a new
     * cache definition).
     *
     * @param string[] $items
     */
    public function setSharedItems(string $fieldName, array $items): self
    {
        $this->sharedItems[$fieldName] = array_values($items);

        return $this;
    }

    /**
     * @return string[]
     */
    public function getSharedItems(string $fieldName): array
    {
        return $this->sharedItems[$fieldName] ?? [];
    }

    public function setFieldGroup(string $fieldName, PivotFieldGroup $group): self
    {
        $this->fieldGroups[$fieldName] = $group;

        return $this;
    }

    public function getFieldGroup(string $fieldName): ?PivotFieldGroup
    {
        return $this->fieldGroups[$fieldName] ?? null;
    }
}
