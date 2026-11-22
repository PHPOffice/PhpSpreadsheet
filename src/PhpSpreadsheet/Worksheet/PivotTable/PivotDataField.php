<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet\PivotTable;

/**
 * One entry in a pivot table's <dataFields> list.
 *
 * A data field is a source column plus the aggregation applied to it. The same
 * source column may appear more than once with different aggregations (for
 * example both the sum and the average of "Amount"), so a data field is its own
 * object rather than a flag on PivotField: there is exactly one PivotField per
 * source column, but there can be several data fields referring to it.
 *
 * @see PivotTable
 * @see PivotField
 */
class PivotDataField
{
    /**
     * Zero-based index of the source (cache) field this aggregates, written as
     * the dataField@fld attribute.
     */
    private int $fieldIndex;

    /**
     * Name of the source field, for convenience when inspecting the model.
     */
    private string $name;

    /**
     * Aggregation function, one of the PivotField::SUBTOTAL_* constants.
     */
    private ?string $subtotal;

    /**
     * Display caption, e.g. "Sum of Amount".
     */
    private ?string $caption;

    public function __construct(int $fieldIndex, string $name = '', ?string $subtotal = null, ?string $caption = null)
    {
        $this->fieldIndex = $fieldIndex;
        $this->name = $name;
        $this->subtotal = $subtotal;
        $this->caption = $caption;
    }

    public function getFieldIndex(): int
    {
        return $this->fieldIndex;
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

    public function getSubtotal(): ?string
    {
        return $this->subtotal;
    }

    public function setSubtotal(?string $subtotal): self
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function setCaption(?string $caption): self
    {
        $this->caption = $caption;

        return $this;
    }
}
