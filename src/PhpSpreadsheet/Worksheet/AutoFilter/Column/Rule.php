<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;

class Rule
{
    public const AUTOFILTER_RULETYPE_FILTER = 'filter';
    public const AUTOFILTER_RULETYPE_DATEGROUP = 'dateGroupItem';
    public const AUTOFILTER_RULETYPE_CUSTOMFILTER = 'customFilter';
    public const AUTOFILTER_RULETYPE_DYNAMICFILTER = 'dynamicFilter';
    public const AUTOFILTER_RULETYPE_TOPTENFILTER = 'top10Filter';

    private const RULE_TYPES = [
        //    Currently we're not handling
        //        colorFilter
        //        extLst
        //        iconFilter
        self::AUTOFILTER_RULETYPE_FILTER,
        self::AUTOFILTER_RULETYPE_DATEGROUP,
        self::AUTOFILTER_RULETYPE_CUSTOMFILTER,
        self::AUTOFILTER_RULETYPE_DYNAMICFILTER,
        self::AUTOFILTER_RULETYPE_TOPTENFILTER,
    ];

    public const AUTOFILTER_RULETYPE_DATEGROUP_YEAR = 'year';
    public const AUTOFILTER_RULETYPE_DATEGROUP_MONTH = 'month';
    public const AUTOFILTER_RULETYPE_DATEGROUP_DAY = 'day';
    public const AUTOFILTER_RULETYPE_DATEGROUP_HOUR = 'hour';
    public const AUTOFILTER_RULETYPE_DATEGROUP_MINUTE = 'minute';
    public const AUTOFILTER_RULETYPE_DATEGROUP_SECOND = 'second';

    private const DATE_TIME_GROUPS = [
        self::AUTOFILTER_RULETYPE_DATEGROUP_YEAR,
        self::AUTOFILTER_RULETYPE_DATEGROUP_MONTH,
        self::AUTOFILTER_RULETYPE_DATEGROUP_DAY,
        self::AUTOFILTER_RULETYPE_DATEGROUP_HOUR,
        self::AUTOFILTER_RULETYPE_DATEGROUP_MINUTE,
        self::AUTOFILTER_RULETYPE_DATEGROUP_SECOND,
    ];

    public const AUTOFILTER_RULETYPE_DYNAMIC_YESTERDAY = 'yesterday';
    public const AUTOFILTER_RULETYPE_DYNAMIC_TODAY = 'today';
    public const AUTOFILTER_RULETYPE_DYNAMIC_TOMORROW = 'tomorrow';
    public const AUTOFILTER_RULETYPE_DYNAMIC_YEARTODATE = 'yearToDate';
    public const AUTOFILTER_RULETYPE_DYNAMIC_THISYEAR = 'thisYear';
    public const AUTOFILTER_RULETYPE_DYNAMIC_THISQUARTER = 'thisQuarter';
    public const AUTOFILTER_RULETYPE_DYNAMIC_THISMONTH = 'thisMonth';
    public const AUTOFILTER_RULETYPE_DYNAMIC_THISWEEK = 'thisWeek';
    public const AUTOFILTER_RULETYPE_DYNAMIC_LASTYEAR = 'lastYear';
    public const AUTOFILTER_RULETYPE_DYNAMIC_LASTQUARTER = 'lastQuarter';
    public const AUTOFILTER_RULETYPE_DYNAMIC_LASTMONTH = 'lastMonth';
    public const AUTOFILTER_RULETYPE_DYNAMIC_LASTWEEK = 'lastWeek';
    public const AUTOFILTER_RULETYPE_DYNAMIC_NEXTYEAR = 'nextYear';
    public const AUTOFILTER_RULETYPE_DYNAMIC_NEXTQUARTER = 'nextQuarter';
    public const AUTOFILTER_RULETYPE_DYNAMIC_NEXTMONTH = 'nextMonth';
    public const AUTOFILTER_RULETYPE_DYNAMIC_NEXTWEEK = 'nextWeek';
    public const AUTOFILTER_RULETYPE_DYNAMIC_MONTH_1 = 'M1';
    public const AUTOFILTER_RULETYPE_DYNAMIC_JANUARY = self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_1;
    public const AUTOFILTER_RULETYPE_DYNAMIC_MONTH_2 = 'M2';
    public const AUTOFILTER_RULETYPE_DYNAMIC_FEBRUARY = self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_2;
    public const AUTOFILTER_RULETYPE_DYNAMIC_MONTH_3 = 'M3';
    public const AUTOFILTER_RULETYPE_DYNAMIC_MARCH = self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_3;
    public const AUTOFILTER_RULETYPE_DYNAMIC_MONTH_4 = 'M4';
    public const AUTOFILTER_RULETYPE_DYNAMIC_APRIL = self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_4;
    public const AUTOFILTER_RULETYPE_DYNAMIC_MONTH_5 = 'M5';
    public const AUTOFILTER_RULETYPE_DYNAMIC_MAY = self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_5;
    public const AUTOFILTER_RULETYPE_DYNAMIC_MONTH_6 = 'M6';
    public const AUTOFILTER_RULETYPE_DYNAMIC_JUNE = self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_6;
    public const AUTOFILTER_RULETYPE_DYNAMIC_MONTH_7 = 'M7';
    public const AUTOFILTER_RULETYPE_DYNAMIC_JULY = self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_7;
    public const AUTOFILTER_RULETYPE_DYNAMIC_MONTH_8 = 'M8';
    public const AUTOFILTER_RULETYPE_DYNAMIC_AUGUST = self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_8;
    public const AUTOFILTER_RULETYPE_DYNAMIC_MONTH_9 = 'M9';
    public const AUTOFILTER_RULETYPE_DYNAMIC_SEPTEMBER = self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_9;
    public const AUTOFILTER_RULETYPE_DYNAMIC_MONTH_10 = 'M10';
    public const AUTOFILTER_RULETYPE_DYNAMIC_OCTOBER = self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_10;
    public const AUTOFILTER_RULETYPE_DYNAMIC_MONTH_11 = 'M11';
    public const AUTOFILTER_RULETYPE_DYNAMIC_NOVEMBER = self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_11;
    public const AUTOFILTER_RULETYPE_DYNAMIC_MONTH_12 = 'M12';
    public const AUTOFILTER_RULETYPE_DYNAMIC_DECEMBER = self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_12;
    public const AUTOFILTER_RULETYPE_DYNAMIC_QUARTER_1 = 'Q1';
    public const AUTOFILTER_RULETYPE_DYNAMIC_QUARTER_2 = 'Q2';
    public const AUTOFILTER_RULETYPE_DYNAMIC_QUARTER_3 = 'Q3';
    public const AUTOFILTER_RULETYPE_DYNAMIC_QUARTER_4 = 'Q4';
    public const AUTOFILTER_RULETYPE_DYNAMIC_ABOVEAVERAGE = 'aboveAverage';
    public const AUTOFILTER_RULETYPE_DYNAMIC_BELOWAVERAGE = 'belowAverage';

    private const DYNAMIC_TYPES = [
        self::AUTOFILTER_RULETYPE_DYNAMIC_YESTERDAY,
        self::AUTOFILTER_RULETYPE_DYNAMIC_TODAY,
        self::AUTOFILTER_RULETYPE_DYNAMIC_TOMORROW,
        self::AUTOFILTER_RULETYPE_DYNAMIC_YEARTODATE,
        self::AUTOFILTER_RULETYPE_DYNAMIC_THISYEAR,
        self::AUTOFILTER_RULETYPE_DYNAMIC_THISQUARTER,
        self::AUTOFILTER_RULETYPE_DYNAMIC_THISMONTH,
        self::AUTOFILTER_RULETYPE_DYNAMIC_THISWEEK,
        self::AUTOFILTER_RULETYPE_DYNAMIC_LASTYEAR,
        self::AUTOFILTER_RULETYPE_DYNAMIC_LASTQUARTER,
        self::AUTOFILTER_RULETYPE_DYNAMIC_LASTMONTH,
        self::AUTOFILTER_RULETYPE_DYNAMIC_LASTWEEK,
        self::AUTOFILTER_RULETYPE_DYNAMIC_NEXTYEAR,
        self::AUTOFILTER_RULETYPE_DYNAMIC_NEXTQUARTER,
        self::AUTOFILTER_RULETYPE_DYNAMIC_NEXTMONTH,
        self::AUTOFILTER_RULETYPE_DYNAMIC_NEXTWEEK,
        self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_1,
        self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_2,
        self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_3,
        self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_4,
        self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_5,
        self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_6,
        self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_7,
        self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_8,
        self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_9,
        self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_10,
        self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_11,
        self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_12,
        self::AUTOFILTER_RULETYPE_DYNAMIC_QUARTER_1,
        self::AUTOFILTER_RULETYPE_DYNAMIC_QUARTER_2,
        self::AUTOFILTER_RULETYPE_DYNAMIC_QUARTER_3,
        self::AUTOFILTER_RULETYPE_DYNAMIC_QUARTER_4,
        self::AUTOFILTER_RULETYPE_DYNAMIC_ABOVEAVERAGE,
        self::AUTOFILTER_RULETYPE_DYNAMIC_BELOWAVERAGE,
    ];

    // Filter rule operators for filter and customFilter types.
    public const AUTOFILTER_COLUMN_RULE_EQUAL = 'equal';
    public const AUTOFILTER_COLUMN_RULE_NOTEQUAL = 'notEqual';
    public const AUTOFILTER_COLUMN_RULE_GREATERTHAN = 'greaterThan';
    public const AUTOFILTER_COLUMN_RULE_GREATERTHANOREQUAL = 'greaterThanOrEqual';
    public const AUTOFILTER_COLUMN_RULE_LESSTHAN = 'lessThan';
    public const AUTOFILTER_COLUMN_RULE_LESSTHANOREQUAL = 'lessThanOrEqual';

    private const OPERATORS = [
        self::AUTOFILTER_COLUMN_RULE_EQUAL,
        self::AUTOFILTER_COLUMN_RULE_NOTEQUAL,
        self::AUTOFILTER_COLUMN_RULE_GREATERTHAN,
        self::AUTOFILTER_COLUMN_RULE_GREATERTHANOREQUAL,
        self::AUTOFILTER_COLUMN_RULE_LESSTHAN,
        self::AUTOFILTER_COLUMN_RULE_LESSTHANOREQUAL,
    ];

    public const AUTOFILTER_COLUMN_RULE_TOPTEN_BY_VALUE = 'byValue';
    public const AUTOFILTER_COLUMN_RULE_TOPTEN_PERCENT = 'byPercent';

    private const TOP_TEN_VALUE = [
        self::AUTOFILTER_COLUMN_RULE_TOPTEN_BY_VALUE,
        self::AUTOFILTER_COLUMN_RULE_TOPTEN_PERCENT,
    ];

    public const AUTOFILTER_COLUMN_RULE_TOPTEN_TOP = 'top';
    public const AUTOFILTER_COLUMN_RULE_TOPTEN_BOTTOM = 'bottom';

    private const TOP_TEN_TYPE = [
        self::AUTOFILTER_COLUMN_RULE_TOPTEN_TOP,
        self::AUTOFILTER_COLUMN_RULE_TOPTEN_BOTTOM,
    ];

    /**
     * Autofilter Rule Type.
     */
    private string $ruleType = self::AUTOFILTER_RULETYPE_FILTER;

    /**
     * Autofilter Rule Value.
     *
     * @var int|int[]|string|string[]
     */
    private $value = '';

    /**
     * Autofilter Rule Operator.
     */
    private string $operator = self::AUTOFILTER_COLUMN_RULE_EQUAL;

    /**
     * DateTimeGrouping Group Value.
     */
    private string $grouping = '';

    //  Unimplented Rule Operators (Numeric, Boolean etc)
    //    const AUTOFILTER_COLUMN_RULE_BETWEEN = 'between';        //    greaterThanOrEqual 1 && lessThanOrEqual 2
    // Rule Operators (Numeric Special) which are translated to standard numeric operators with calculated values
    // Rule Operators (String) which are set as wild-carded values
    //    const AUTOFILTER_COLUMN_RULE_BEGINSWITH            = 'beginsWith';            // A*
    //    const AUTOFILTER_COLUMN_RULE_ENDSWITH            = 'endsWith';            // *Z
    //    const AUTOFILTER_COLUMN_RULE_CONTAINS            = 'contains';            // *B*
    //    const AUTOFILTER_COLUMN_RULE_DOESNTCONTAIN        = 'notEqual';            //    notEqual *B*
    // Rule Operators (Date Special) which are translated to standard numeric operators with calculated values
    //    const AUTOFILTER_COLUMN_RULE_BEFORE                = 'lessThan';
    //    const AUTOFILTER_COLUMN_RULE_AFTER                = 'greaterThan';

    /**
     * Create a new Rule.
     */
    public function __construct(
        /**
         * Autofilter Column.
         */
        private ?Column $parent = null
    ) {
    }

    private function setEvaluatedFalse(): void
    {
        if ($this->parent !== null) {
            $this->parent->setEvaluatedFalse();
        }
    }

    /**
     * Get AutoFilter Rule Type.
     */
    public function getRuleType(): string
    {
        return $this->ruleType;
    }

    /**
     * Set AutoFilter Rule Type.
     *
     * @param string $ruleType see self::AUTOFILTER_RULETYPE_*
     *
     * @return $this
     */
    public function setRuleType(string $ruleType): static
    {
        $this->setEvaluatedFalse();
        if (!in_array($ruleType, self::RULE_TYPES)) {
            throw new PhpSpreadsheetException('Invalid rule type for column AutoFilter Rule.');
        }

        $this->ruleType = $ruleType;

        return $this;
    }

    /**
     * Get AutoFilter Rule Value.
     *
     * @return int|int[]|string|string[]
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set AutoFilter Rule Value.
     *
     * @param int|int[]|string|string[] $value
     *
     * @return $this
     */
    public function setValue($value): static
    {
        $this->setEvaluatedFalse();
        if (is_array($value)) {
            $grouping = -1;
            foreach ($value as $key => $v) {
                //    Validate array entries
                if (!in_array($key, self::DATE_TIME_GROUPS)) {
                    //    Remove any invalid entries from the value array
                    unset($value[$key]);
                } else {
                    //    Work out what the dateTime grouping will be
                    $grouping = max($grouping, array_search($key, self::DATE_TIME_GROUPS));
                }
            }
            if (count($value) == 0) {
                throw new PhpSpreadsheetException('Invalid rule value for column AutoFilter Rule.');
            }
            //    Set the dateTime grouping that we've anticipated
            $this->setGrouping(self::DATE_TIME_GROUPS[$grouping]);
        }
        $this->value = $value;

        return $this;
    }

    /**
     * Get AutoFilter Rule Operator.
     */
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * Set AutoFilter Rule Operator.
     *
     * @param string $operator see self::AUTOFILTER_COLUMN_RULE_*
     *
     * @return $this
     */
    public function setOperator(string $operator): static
    {
        $this->setEvaluatedFalse();
        if (empty($operator)) {
            $operator = self::AUTOFILTER_COLUMN_RULE_EQUAL;
        }
        if (
            (!in_array($operator, self::OPERATORS))
            && (!in_array($operator, self::TOP_TEN_VALUE))
        ) {
            throw new PhpSpreadsheetException('Invalid operator for column AutoFilter Rule.');
        }
        $this->operator = $operator;

        return $this;
    }

    /**
     * Get AutoFilter Rule Grouping.
     */
    public function getGrouping(): string
    {
        return $this->grouping;
    }

    /**
     * Set AutoFilter Rule Grouping.
     *
     * @return $this
     */
    public function setGrouping(string $grouping): static
    {
        $this->setEvaluatedFalse();
        if (
            ($grouping !== null)
            && (!in_array($grouping, self::DATE_TIME_GROUPS))
            && (!in_array($grouping, self::DYNAMIC_TYPES))
            && (!in_array($grouping, self::TOP_TEN_TYPE))
        ) {
            throw new PhpSpreadsheetException('Invalid grouping for column AutoFilter Rule.');
        }
        $this->grouping = $grouping;

        return $this;
    }

    /**
     * Set AutoFilter Rule.
     *
     * @param string $operator see self::AUTOFILTER_COLUMN_RULE_*
     * @param int|int[]|string|string[] $value
     *
     * @return $this
     */
    public function setRule(string $operator, $value, ?string $grouping = null): static
    {
        $this->setEvaluatedFalse();
        $this->setOperator($operator);
        $this->setValue($value);
        //  Only set grouping if it's been passed in as a user-supplied argument,
        //      otherwise we're calculating it when we setValue() and don't want to overwrite that
        //      If the user supplies an argumnet for grouping, then on their own head be it
        if ($grouping !== null) {
            $this->setGrouping($grouping);
        }

        return $this;
    }

    /**
     * Get this Rule's AutoFilter Column Parent.
     */
    public function getParent(): ?Column
    {
        return $this->parent;
    }

    /**
     * Set this Rule's AutoFilter Column Parent.
     *
     * @return $this
     */
    public function setParent(?Column $parent = null): static
    {
        $this->setEvaluatedFalse();
        $this->parent = $parent;

        return $this;
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if (is_object($value)) {
                if ($key == 'parent') { // this is only object
                    //    Detach from autofilter column parent
                    $this->$key = null;
                }
            } else {
                $this->$key = $value;
            }
        }
    }
}
