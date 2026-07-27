<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet\PivotTable;

/**
 * Read-only representation of a single field within a pivot table.
 *
 * A pivot field describes how one of the source cache fields is used in the
 * pivot table: which axis it is placed on (row, column, page/filter or data),
 * and, for value fields, which aggregation function is applied.
 *
 * @see \PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotTable
 */
class PivotField
{
    // Axis placement, taken from the pivotField@axis attribute (empty when the
    // field is only used as a data/value field or is unused).
    const AXIS_ROW = 'axisRow';
    const AXIS_COLUMN = 'axisCol';
    const AXIS_PAGE = 'axisPage';
    const AXIS_VALUES = 'axisValues';
    const AXIS_NONE = '';

    // Aggregation functions for a data (value) field. These map directly to the
    // dataField@subtotal attribute values defined by the OOXML spec.
    const SUBTOTAL_SUM = 'sum';
    const SUBTOTAL_COUNT = 'count';
    const SUBTOTAL_AVERAGE = 'average';
    const SUBTOTAL_MAX = 'max';
    const SUBTOTAL_MIN = 'min';
    const SUBTOTAL_PRODUCT = 'product';
    const SUBTOTAL_COUNT_NUMS = 'countNums';
    const SUBTOTAL_STD_DEV = 'stdDev';
    const SUBTOTAL_STD_DEV_P = 'stdDevp';
    const SUBTOTAL_VAR = 'var';
    const SUBTOTAL_VAR_P = 'varp';

    /**
     * Name of the field, taken from the source cache field it maps to.
     */
    private string $name;

    /**
     * Zero-based index of the field within the pivot table / cache definition.
     */
    private int $index;

    /**
     * Axis the field is placed on, one of the AXIS_* constants.
     */
    private string $axis = self::AXIS_NONE;

    /**
     * True when the field appears in the <dataFields> section (a value field).
     */
    private bool $dataField = false;

    /**
     * Aggregation function for a data field (e.g. sum, count, average).
     * Null when this is not a data field.
     */
    private ?string $subtotal = null;

    /**
     * Display caption for a data field (e.g. "Sum of Amount"). Null when this
     * is not a data field.
     */
    private ?string $dataFieldCaption = null;

    public function __construct(int $index, string $name = '')
    {
        $this->index = $index;
        $this->name = $name;
    }

    public function getIndex(): int
    {
        return $this->index;
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

    public function getAxis(): string
    {
        return $this->axis;
    }

    public function setAxis(string $axis): self
    {
        $this->axis = $axis;

        return $this;
    }

    public function isDataField(): bool
    {
        return $this->dataField;
    }

    public function setDataField(bool $dataField): self
    {
        $this->dataField = $dataField;

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

    public function getDataFieldCaption(): ?string
    {
        return $this->dataFieldCaption;
    }

    public function setDataFieldCaption(?string $dataFieldCaption): self
    {
        $this->dataFieldCaption = $dataFieldCaption;

        return $this;
    }
}
