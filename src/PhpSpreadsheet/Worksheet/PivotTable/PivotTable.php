<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet\PivotTable;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Stringable;

/**
 * Read-only representation of a pivot table.
 *
 * This models the metadata of a pivot table that already exists in a loaded
 * spreadsheet: its name, where it is placed on the sheet, the cache it draws
 * its data from, and the fields on each axis. It does not (yet) recalculate or
 * render the pivot, and it does not modify the underlying file - existing pivot
 * tables are still written back verbatim from the preserved source XML.
 *
 * @see \PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotCacheDefinition
 * @see \PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotField
 */
class PivotTable implements Stringable
{
    /**
     * Pivot table name (the "name" attribute of the pivotTableDefinition).
     */
    private string $name;

    /**
     * The worksheet this pivot table is placed on.
     */
    private ?Worksheet $worksheet = null;

    /**
     * Target range on the worksheet occupied by the rendered pivot table,
     * taken from location@ref, e.g. "A3:D20".
     */
    private string $location = '';

    /**
     * The cache definition this pivot table draws its data from.
     */
    private ?PivotCacheDefinition $cacheDefinition = null;

    /**
     * The pivot fields, in field-index order.
     *
     * @var PivotField[]
     */
    private array $fields = [];

    /**
     * True when this pivot table was built in memory (rather than loaded from a
     * file) and therefore must have its OOXML parts generated on save. Loaded
     * pivot tables keep their original XML and are written back verbatim.
     */
    private bool $generated = false;

    public function __construct(string $name = '')
    {
        $this->name = $name;
    }

    public function isGenerated(): bool
    {
        return $this->generated;
    }

    public function setGenerated(bool $generated): self
    {
        $this->generated = $generated;

        return $this;
    }

    /**
     * Code to execute when this pivot table is unset().
     */
    public function __destruct()
    {
        $this->worksheet = null;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getWorksheet(): ?Worksheet
    {
        return $this->worksheet;
    }

    public function setWorksheet(?Worksheet $worksheet): self
    {
        $this->worksheet = $worksheet;

        return $this;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getCacheDefinition(): ?PivotCacheDefinition
    {
        return $this->cacheDefinition;
    }

    public function setCacheDefinition(?PivotCacheDefinition $cacheDefinition): self
    {
        $this->cacheDefinition = $cacheDefinition;

        return $this;
    }

    /**
     * @return PivotField[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function addField(PivotField $field): self
    {
        $this->fields[] = $field;

        return $this;
    }

    /**
     * Return only the fields placed on the row axis, in order.
     *
     * @return PivotField[]
     */
    public function getRowFields(): array
    {
        return $this->getFieldsOnAxis(PivotField::AXIS_ROW);
    }

    /**
     * Return only the fields placed on the column axis, in order.
     *
     * @return PivotField[]
     */
    public function getColumnFields(): array
    {
        return $this->getFieldsOnAxis(PivotField::AXIS_COLUMN);
    }

    /**
     * Return only the fields placed on the page/filter axis, in order.
     *
     * @return PivotField[]
     */
    public function getPageFields(): array
    {
        return $this->getFieldsOnAxis(PivotField::AXIS_PAGE);
    }

    /**
     * Return only the value/data fields, in order.
     *
     * @return PivotField[]
     */
    public function getDataFields(): array
    {
        return array_values(
            array_filter(
                $this->fields,
                static fn (PivotField $field): bool => $field->isDataField()
            )
        );
    }

    /**
     * @return PivotField[]
     */
    private function getFieldsOnAxis(string $axis): array
    {
        return array_values(
            array_filter(
                $this->fields,
                static fn (PivotField $field): bool => $field->getAxis() === $axis
            )
        );
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
