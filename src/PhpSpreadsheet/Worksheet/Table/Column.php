<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet\Table;

use PhpOffice\PhpSpreadsheet\Worksheet\Table;

class Column
{
    /**
     * Table Column Index.
     *
     * @var string
     */
    private $columnIndex = '';

    /**
     * Show Filter Button.
     *
     * @var bool
     */
    private $showFilterButton = true;

    /**
     * Total Row Label.
     *
     * @var string
     */
    private $totalsRowLabel;

    /**
     * Total Row Function.
     *
     * @var string
     */
    private $totalsRowFunction;

    /**
     * Total Row Formula.
     *
     * @var string
     */
    private $totalsRowFormula;

    /**
     * Column Formula.
     *
     * @var string
     */
    private $columnFormula;

    /**
     * Table.
     *
     * @var null|Table
     */
    private $table;

    /**
     * Create a new Column.
     *
     * @param string $column Column (e.g. A)
     * @param Table $table Table for this column
     */
    public function __construct($column, ?Table $table = null)
    {
        $this->columnIndex = $column;
        $this->table = $table;
    }

    /**
     * Get Table column index as string eg: 'A'.
     */
    public function getColumnIndex(): string
    {
        return $this->columnIndex;
    }

    /**
     * Set Table column index as string eg: 'A'.
     *
     * @param string $column Column (e.g. A)
     */
    public function setColumnIndex($column): self
    {
        // Uppercase coordinate
        $column = strtoupper($column);
        if ($this->table !== null) {
            $this->table->isColumnInRange($column);
        }

        $this->columnIndex = $column;

        return $this;
    }

    /**
     * Get show Filter Button.
     */
    public function getShowFilterButton(): bool
    {
        return $this->showFilterButton;
    }

    /**
     * Set show Filter Button.
     */
    public function setShowFilterButton(bool $showFilterButton): self
    {
        $this->showFilterButton = $showFilterButton;

        return $this;
    }

    /**
     * Get total Row Label.
     */
    public function getTotalsRowLabel(): ?string
    {
        return $this->totalsRowLabel;
    }

    /**
     * Set total Row Label.
     */
    public function setTotalsRowLabel(string $totalsRowLabel): self
    {
        $this->totalsRowLabel = $totalsRowLabel;

        return $this;
    }

    /**
     * Get total Row Function.
     */
    public function getTotalsRowFunction(): ?string
    {
        return $this->totalsRowFunction;
    }

    /**
     * Set total Row Function.
     */
    public function setTotalsRowFunction(string $totalsRowFunction): self
    {
        $this->totalsRowFunction = $totalsRowFunction;

        return $this;
    }

    /**
     * Get total Row Formula.
     */
    public function getTotalsRowFormula(): ?string
    {
        return $this->totalsRowFormula;
    }

    /**
     * Set total Row Formula.
     */
    public function setTotalsRowFormula(string $totalsRowFormula): self
    {
        $this->totalsRowFormula = $totalsRowFormula;

        return $this;
    }

    /**
     * Get column Formula.
     */
    public function getColumnFormula(): ?string
    {
        return $this->columnFormula;
    }

    /**
     * Set column Formula.
     */
    public function setColumnFormula(string $columnFormula): self
    {
        $this->columnFormula = $columnFormula;

        return $this;
    }

    /**
     * Get this Column's Table.
     */
    public function getTable(): ?Table
    {
        return $this->table;
    }

    /**
     * Set this Column's Table.
     */
    public function setTable(?Table $table = null): self
    {
        $this->table = $table;

        return $this;
    }
}
