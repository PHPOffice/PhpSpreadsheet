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
     *
     * @return string
     */
    public function getColumnIndex()
    {
        return $this->columnIndex;
    }

    /**
     * Set Table column index as string eg: 'A'.
     *
     * @param string $column Column (e.g. A)
     *
     * @return $this
     */
    public function setColumnIndex($column)
    {
        // Uppercase coordinate
        $column = strtoupper($column);
        if ($this->table !== null) {
            $this->table->testColumnInRange($column);
        }

        $this->columnIndex = $column;

        return $this;
    }

    /**
     * Get show Filter Button.
     *
     * @return bool
     */
    public function getShowFilterButton()
    {
        return $this->showFilterButton;
    }

    /**
     * Set show Filter Button.
     *
     * @return  $this
     */
    public function setShowFilterButton(bool $showFilterButton)
    {
        $this->showFilterButton = $showFilterButton;

        return $this;
    }

    /**
     * Get total Row Label.
     *
     * @return string
     */
    public function getTotalsRowLabel()
    {
        return $this->totalsRowLabel;
    }

    /**
     * Set total Row Label.
     *
     * @return  $this
     */
    public function setTotalsRowLabel(string $totalsRowLabel)
    {
        $this->totalsRowLabel = $totalsRowLabel;

        return $this;
    }

    /**
     * Get total Row Function.
     *
     * @return string
     */
    public function getTotalsRowFunction()
    {
        return $this->totalsRowFunction;
    }

    /**
     * Set total Row Function.
     *
     * @return  $this
     */
    public function setTotalsRowFunction(string $totalsRowFunction)
    {
        $this->totalsRowFunction = $totalsRowFunction;

        return $this;
    }

    /**
     * Get total Row Formula.
     *
     * @return string
     */
    public function getTotalsRowFormula()
    {
        return $this->totalsRowFormula;
    }

    /**
     * Set total Row Formula.
     *
     * @return  $this
     */
    public function setTotalsRowFormula(string $totalsRowFormula)
    {
        $this->totalsRowFormula = $totalsRowFormula;

        return $this;
    }

    /**
     * Get this Column's Table.
     *
     * @return null|Table
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Set this Column's Table.
     *
     * @return $this
     */
    public function setTable(?Table $table = null)
    {
        $this->table = $table;

        return $this;
    }
}
