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
    private static $filterTypes = [
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
    private static $ruleJoins = [
        self::AUTOFILTER_COLUMN_JOIN_AND,
        self::AUTOFILTER_COLUMN_JOIN_OR,
    ];

    /**
     * Autofilter.
     *
     * @var AutoFilter
     */
    private $parent;

    /**
     * Autofilter Column Index.
     *
     * @var string
     */
    private $columnIndex = '';

    /**
     * Autofilter Column Filter Type.
     *
     * @var string
     */
    private $filterType = self::AUTOFILTER_FILTERTYPE_FILTER;

    /**
     * Autofilter Multiple Rules And/Or.
     *
     * @var string
     */
    private $join = self::AUTOFILTER_COLUMN_JOIN_OR;

    /**
     * Autofilter Column Rules.
     *
     * @var array of Column\Rule
     */
    private $ruleset = [];

    /**
     * Autofilter Column Dynamic Attributes.
     *
     * @var array of mixed
     */
    private $attributes = [];

    /**
     * Create a new Column.
     *
     * @param string $pColumn Column (e.g. A)
     * @param AutoFilter $pParent Autofilter for this column
     */
    public function __construct($pColumn, ?AutoFilter $pParent = null)
    {
        $this->columnIndex = $pColumn;
        $this->parent = $pParent;
    }

    /**
     * Get AutoFilter column index as string eg: 'A'.
     *
     * @return string
     */
    public function getColumnIndex()
    {
        return $this->columnIndex;
    }

    /**
     * Set AutoFilter column index as string eg: 'A'.
     *
     * @param string $pColumn Column (e.g. A)
     *
     * @return $this
     */
    public function setColumnIndex($pColumn)
    {
        // Uppercase coordinate
        $pColumn = strtoupper($pColumn);
        if ($this->parent !== null) {
            $this->parent->testColumnInRange($pColumn);
        }

        $this->columnIndex = $pColumn;

        return $this;
    }

    /**
     * Get this Column's AutoFilter Parent.
     *
     * @return AutoFilter
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set this Column's AutoFilter Parent.
     *
     * @param AutoFilter $pParent
     *
     * @return $this
     */
    public function setParent(?AutoFilter $pParent = null)
    {
        $this->parent = $pParent;

        return $this;
    }

    /**
     * Get AutoFilter Type.
     *
     * @return string
     */
    public function getFilterType()
    {
        return $this->filterType;
    }

    /**
     * Set AutoFilter Type.
     *
     * @param string $pFilterType
     *
     * @return $this
     */
    public function setFilterType($pFilterType)
    {
        if (!in_array($pFilterType, self::$filterTypes)) {
            throw new PhpSpreadsheetException('Invalid filter type for column AutoFilter.');
        }

        $this->filterType = $pFilterType;

        return $this;
    }

    /**
     * Get AutoFilter Multiple Rules And/Or Join.
     *
     * @return string
     */
    public function getJoin()
    {
        return $this->join;
    }

    /**
     * Set AutoFilter Multiple Rules And/Or.
     *
     * @param string $pJoin And/Or
     *
     * @return $this
     */
    public function setJoin($pJoin)
    {
        // Lowercase And/Or
        $pJoin = strtolower($pJoin);
        if (!in_array($pJoin, self::$ruleJoins)) {
            throw new PhpSpreadsheetException('Invalid rule connection for column AutoFilter.');
        }

        $this->join = $pJoin;

        return $this;
    }

    /**
     * Set AutoFilter Attributes.
     *
     * @param string[] $attributes
     *
     * @return $this
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Set An AutoFilter Attribute.
     *
     * @param string $pName Attribute Name
     * @param string $pValue Attribute Value
     *
     * @return $this
     */
    public function setAttribute($pName, $pValue)
    {
        $this->attributes[$pName] = $pValue;

        return $this;
    }

    /**
     * Get AutoFilter Column Attributes.
     *
     * @return string[]
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Get specific AutoFilter Column Attribute.
     *
     * @param string $pName Attribute Name
     *
     * @return string
     */
    public function getAttribute($pName)
    {
        if (isset($this->attributes[$pName])) {
            return $this->attributes[$pName];
        }

        return null;
    }

    /**
     * Get all AutoFilter Column Rules.
     *
     * @return Column\Rule[]
     */
    public function getRules()
    {
        return $this->ruleset;
    }

    /**
     * Get a specified AutoFilter Column Rule.
     *
     * @param int $pIndex Rule index in the ruleset array
     *
     * @return Column\Rule
     */
    public function getRule($pIndex)
    {
        if (!isset($this->ruleset[$pIndex])) {
            $this->ruleset[$pIndex] = new Column\Rule($this);
        }

        return $this->ruleset[$pIndex];
    }

    /**
     * Create a new AutoFilter Column Rule in the ruleset.
     *
     * @return Column\Rule
     */
    public function createRule()
    {
        $this->ruleset[] = new Column\Rule($this);

        return end($this->ruleset);
    }

    /**
     * Add a new AutoFilter Column Rule to the ruleset.
     *
     * @return $this
     */
    public function addRule(Column\Rule $pRule)
    {
        $pRule->setParent($this);
        $this->ruleset[] = $pRule;

        return $this;
    }

    /**
     * Delete a specified AutoFilter Column Rule
     * If the number of rules is reduced to 1, then we reset And/Or logic to Or.
     *
     * @param int $pIndex Rule index in the ruleset array
     *
     * @return $this
     */
    public function deleteRule($pIndex)
    {
        if (isset($this->ruleset[$pIndex])) {
            unset($this->ruleset[$pIndex]);
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
    public function clearRules()
    {
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
            } elseif (is_object($value)) {
                $this->$key = clone $value;
            } else {
                $this->$key = $value;
            }
        }
    }
}
