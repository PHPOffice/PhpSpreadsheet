<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter;

class Column
{
    const AUTOFILTER_FILTERTYPE_FILTER = 'filters';
    const AUTOFILTER_FILTERTYPE_CUSTOMFILTER = 'customFilters';
    //    Supports no more than 2 rules, with an And/Or join criteria
    //        if more than 1 rule is defined
    const AUTOFILTER_FILTERTYPE_DYNAMICFILTER = 'dynamicFilter';
    //    Even though the filter rule is constant, the filtered data can vary
    //        e.g. filtered by date = TODAY
    const AUTOFILTER_FILTERTYPE_TOPTENFILTER = 'top10';

    /**
     * Types of autofilter rules.
     *
     * @var string[]
     */
    private static array $filterTypes = [
        //    Currently we're not handling
        //        colorFilter
        //        extLst
        //        iconFilter
        self::AUTOFILTER_FILTERTYPE_FILTER,
        self::AUTOFILTER_FILTERTYPE_CUSTOMFILTER,
        self::AUTOFILTER_FILTERTYPE_DYNAMICFILTER,
        self::AUTOFILTER_FILTERTYPE_TOPTENFILTER,
    ];

    // Multiple Rule Connections
    const AUTOFILTER_COLUMN_JOIN_AND = 'and';
    const AUTOFILTER_COLUMN_JOIN_OR = 'or';

    /**
     * Join options for autofilter rules.
     *
     * @var string[]
     */
    private static array $ruleJoins = [
        self::AUTOFILTER_COLUMN_JOIN_AND,
        self::AUTOFILTER_COLUMN_JOIN_OR,
    ];

    /**
     * Autofilter.
     */
    private ?AutoFilter $parent;

    /**
     * Autofilter Column Index.
     */
    private string $columnIndex;

    /**
     * Autofilter Column Filter Type.
     */
    private string $filterType = self::AUTOFILTER_FILTERTYPE_FILTER;

    /**
     * Autofilter Multiple Rules And/Or.
     */
    private string $join = self::AUTOFILTER_COLUMN_JOIN_OR;

    /**
     * Autofilter Column Rules.
     *
     * @var Column\Rule[]
     */
    private array $ruleset = [];

    /**
     * Autofilter Column Dynamic Attributes.
     *
     * @var (float|int|string)[]
     */
    private array $attributes = [];

    /**
     * Create a new Column.
     *
     * @param string $column Column (e.g. A)
     * @param ?AutoFilter $parent Autofilter for this column
     */
    public function __construct(string $column, ?AutoFilter $parent = null)
    {
        $this->columnIndex = $column;
        $this->parent = $parent;
    }

    public function setEvaluatedFalse(): void
    {
        if ($this->parent !== null) {
            $this->parent->setEvaluated(false);
        }
    }

    /**
     * Get AutoFilter column index as string eg: 'A'.
     */
    public function getColumnIndex(): string
    {
        return $this->columnIndex;
    }

    /**
     * Set AutoFilter column index as string eg: 'A'.
     *
     * @param string $column Column (e.g. A)
     *
     * @return $this
     */
    public function setColumnIndex(string $column): static
    {
        $this->setEvaluatedFalse();
        // Uppercase coordinate
        $column = strtoupper($column);
        if ($this->parent !== null) {
            $this->parent->testColumnInRange($column);
        }

        $this->columnIndex = $column;

        return $this;
    }

    /**
     * Get this Column's AutoFilter Parent.
     */
    public function getParent(): ?AutoFilter
    {
        return $this->parent;
    }

    /**
     * Set this Column's AutoFilter Parent.
     *
     * @return $this
     */
    public function setParent(?AutoFilter $parent = null): static
    {
        $this->setEvaluatedFalse();
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get AutoFilter Type.
     */
    public function getFilterType(): string
    {
        return $this->filterType;
    }

    /**
     * Set AutoFilter Type.
     *
     * @return $this
     */
    public function setFilterType(string $filterType): static
    {
        $this->setEvaluatedFalse();
        if (!in_array($filterType, self::$filterTypes)) {
            throw new PhpSpreadsheetException('Invalid filter type for column AutoFilter.');
        }
        if ($filterType === self::AUTOFILTER_FILTERTYPE_CUSTOMFILTER && count($this->ruleset) > 2) {
            throw new PhpSpreadsheetException('No more than 2 rules are allowed in a Custom Filter');
        }

        $this->filterType = $filterType;

        return $this;
    }

    /**
     * Get AutoFilter Multiple Rules And/Or Join.
     */
    public function getJoin(): string
    {
        return $this->join;
    }

    /**
     * Set AutoFilter Multiple Rules And/Or.
     *
     * @param string $join And/Or
     *
     * @return $this
     */
    public function setJoin(string $join): static
    {
        $this->setEvaluatedFalse();
        // Lowercase And/Or
        $join = strtolower($join);
        if (!in_array($join, self::$ruleJoins)) {
            throw new PhpSpreadsheetException('Invalid rule connection for column AutoFilter.');
        }

        $this->join = $join;

        return $this;
    }

    /**
     * Set AutoFilter Attributes.
     *
     * @param (float|int|string)[] $attributes
     *
     * @return $this
     */
    public function setAttributes(array $attributes): static
    {
        $this->setEvaluatedFalse();
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Set An AutoFilter Attribute.
     *
     * @param string $name Attribute Name
     * @param float|int|string $value Attribute Value
     *
     * @return $this
     */
    public function setAttribute(string $name, $value): static
    {
        $this->setEvaluatedFalse();
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * Get AutoFilter Column Attributes.
     *
     * @return (float|int|string)[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Get specific AutoFilter Column Attribute.
     *
     * @param string $name Attribute Name
     */
    public function getAttribute(string $name): null|float|int|string
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }

        return null;
    }

    public function ruleCount(): int
    {
        return count($this->ruleset);
    }

    /**
     * Get all AutoFilter Column Rules.
     *
     * @return Column\Rule[]
     */
    public function getRules(): array
    {
        return $this->ruleset;
    }

    /**
     * Get a specified AutoFilter Column Rule.
     *
     * @param int $index Rule index in the ruleset array
     */
    public function getRule(int $index): Column\Rule
    {
        if (!isset($this->ruleset[$index])) {
            $this->ruleset[$index] = new Column\Rule($this);
        }

        return $this->ruleset[$index];
    }

    /**
     * Create a new AutoFilter Column Rule in the ruleset.
     */
    public function createRule(): Column\Rule
    {
        $this->setEvaluatedFalse();
        if ($this->filterType === self::AUTOFILTER_FILTERTYPE_CUSTOMFILTER && count($this->ruleset) >= 2) {
            throw new PhpSpreadsheetException('No more than 2 rules are allowed in a Custom Filter');
        }
        $this->ruleset[] = new Column\Rule($this);

        return end($this->ruleset);
    }

    /**
     * Add a new AutoFilter Column Rule to the ruleset.
     *
     * @return $this
     */
    public function addRule(Column\Rule $rule): static
    {
        $this->setEvaluatedFalse();
        $rule->setParent($this);
        $this->ruleset[] = $rule;

        return $this;
    }

    /**
     * Delete a specified AutoFilter Column Rule
     * If the number of rules is reduced to 1, then we reset And/Or logic to Or.
     *
     * @param int $index Rule index in the ruleset array
     *
     * @return $this
     */
    public function deleteRule(int $index): static
    {
        $this->setEvaluatedFalse();
        if (isset($this->ruleset[$index])) {
            unset($this->ruleset[$index]);
            //    If we've just deleted down to a single rule, then reset And/Or joining to Or
            if (count($this->ruleset) <= 1) {
                $this->setJoin(self::AUTOFILTER_COLUMN_JOIN_OR);
            }
        }

        return $this;
    }

    /**
     * Delete all AutoFilter Column Rules.
     *
     * @return $this
     */
    public function clearRules(): static
    {
        $this->setEvaluatedFalse();
        $this->ruleset = [];
        $this->setJoin(self::AUTOFILTER_COLUMN_JOIN_OR);

        return $this;
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone()
    {
        $vars = get_object_vars($this);
        /** @var AutoFilter\Column\Rule[] $value */
        foreach ($vars as $key => $value) {
            if ($key === 'parent') {
                // Detach from autofilter parent
                $this->parent = null;
            } elseif ($key === 'ruleset') {
                // The columns array of \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet\AutoFilter objects
                $this->ruleset = [];
                foreach ($value as $k => $v) {
                    $cloned = clone $v;
                    $cloned->setParent($this); // attach the new cloned Rule to this new cloned Autofilter Cloned object
                    $this->ruleset[$k] = $cloned;
                }
            } else {
                $this->$key = $value;
            }
        }
    }
}
