<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;

class Rule
{
    const AUTOFILTER_RULETYPE_FILTER = 'filter';
    const AUTOFILTER_RULETYPE_DATEGROUP = 'dateGroupItem';
    const AUTOFILTER_RULETYPE_CUSTOMFILTER = 'customFilter';
    const AUTOFILTER_RULETYPE_DYNAMICFILTER = 'dynamicFilter';
    const AUTOFILTER_RULETYPE_TOPTENFILTER = 'top10Filter';

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

    const AUTOFILTER_RULETYPE_DATEGROUP_YEAR = 'year';
    const AUTOFILTER_RULETYPE_DATEGROUP_MONTH = 'month';
    const AUTOFILTER_RULETYPE_DATEGROUP_DAY = 'day';
    const AUTOFILTER_RULETYPE_DATEGROUP_HOUR = 'hour';
    const AUTOFILTER_RULETYPE_DATEGROUP_MINUTE = 'minute';
    const AUTOFILTER_RULETYPE_DATEGROUP_SECOND = 'second';

    private const DATE_TIME_GROUPS = [
        self::AUTOFILTER_RULETYPE_DATEGROUP_YEAR,
        self::AUTOFILTER_RULETYPE_DATEGROUP_MONTH,
        self::AUTOFILTER_RULETYPE_DATEGROUP_DAY,
        self::AUTOFILTER_RULETYPE_DATEGROUP_HOUR,
        self::AUTOFILTER_RULETYPE_DATEGROUP_MINUTE,
        self::AUTOFILTER_RULETYPE_DATEGROUP_SECOND,
    ];

    const AUTOFILTER_RULETYPE_DYNAMIC_YESTERDAY = 'yesterday';
    const AUTOFILTER_RULETYPE_DYNAMIC_TODAY = 'today';
    const AUTOFILTER_RULETYPE_DYNAMIC_TOMORROW = 'tomorrow';
    const AUTOFILTER_RULETYPE_DYNAMIC_YEARTODATE = 'yearToDate';
    const AUTOFILTER_RULETYPE_DYNAMIC_THISYEAR = 'thisYear';
    const AUTOFILTER_RULETYPE_DYNAMIC_THISQUARTER = 'thisQuarter';
    const AUTOFILTER_RULETYPE_DYNAMIC_THISMONTH = 'thisMonth';
    const AUTOFILTER_RULETYPE_DYNAMIC_THISWEEK = 'thisWeek';
    const AUTOFILTER_RULETYPE_DYNAMIC_LASTYEAR = 'lastYear';
    const AUTOFILTER_RULETYPE_DYNAMIC_LASTQUARTER = 'lastQuarter';
    const AUTOFILTER_RULETYPE_DYNAMIC_LASTMONTH = 'lastMonth';
    const AUTOFILTER_RULETYPE_DYNAMIC_LASTWEEK = 'lastWeek';
    const AUTOFILTER_RULETYPE_DYNAMIC_NEXTYEAR = 'nextYear';
    const AUTOFILTER_RULETYPE_DYNAMIC_NEXTQUARTER = 'nextQuarter';
    const AUTOFILTER_RULETYPE_DYNAMIC_NEXTMONTH = 'nextMonth';
    const AUTOFILTER_RULETYPE_DYNAMIC_NEXTWEEK = 'nextWeek';
    const AUTOFILTER_RULETYPE_DYNAMIC_MONTH_1 = 'M1';
    const AUTOFILTER_RULETYPE_DYNAMIC_JANUARY = self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_1;
    const AUTOFILTER_RULETYPE_DYNAMIC_MONTH_2 = 'M2';
    const AUTOFILTER_RULETYPE_DYNAMIC_FEBRUARY = self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_2;
    const AUTOFILTER_RULETYPE_DYNAMIC_MONTH_3 = 'M3';
    const AUTOFILTER_RULETYPE_DYNAMIC_MARCH = self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_3;
    const AUTOFILTER_RULETYPE_DYNAMIC_MONTH_4 = 'M4';
    const AUTOFILTER_RULETYPE_DYNAMIC_APRIL = self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_4;
    const AUTOFILTER_RULETYPE_DYNAMIC_MONTH_5 = 'M5';
    const AUTOFILTER_RULETYPE_DYNAMIC_MAY = self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_5;
    const AUTOFILTER_RULETYPE_DYNAMIC_MONTH_6 = 'M6';
    const AUTOFILTER_RULETYPE_DYNAMIC_JUNE = self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_6;
    const AUTOFILTER_RULETYPE_DYNAMIC_MONTH_7 = 'M7';
    const AUTOFILTER_RULETYPE_DYNAMIC_JULY = self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_7;
    const AUTOFILTER_RULETYPE_DYNAMIC_MONTH_8 = 'M8';
    const AUTOFILTER_RULETYPE_DYNAMIC_AUGUST = self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_8;
    const AUTOFILTER_RULETYPE_DYNAMIC_MONTH_9 = 'M9';
    const AUTOFILTER_RULETYPE_DYNAMIC_SEPTEMBER = self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_9;
    const AUTOFILTER_RULETYPE_DYNAMIC_MONTH_10 = 'M10';
    const AUTOFILTER_RULETYPE_DYNAMIC_OCTOBER = self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_10;
    const AUTOFILTER_RULETYPE_DYNAMIC_MONTH_11 = 'M11';
    const AUTOFILTER_RULETYPE_DYNAMIC_NOVEMBER = self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_11;
    const AUTOFILTER_RULETYPE_DYNAMIC_MONTH_12 = 'M12';
    const AUTOFILTER_RULETYPE_DYNAMIC_DECEMBER = self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_12;
    const AUTOFILTER_RULETYPE_DYNAMIC_QUARTER_1 = 'Q1';
    const AUTOFILTER_RULETYPE_DYNAMIC_QUARTER_2 = 'Q2';
    const AUTOFILTER_RULETYPE_DYNAMIC_QUARTER_3 = 'Q3';
    const AUTOFILTER_RULETYPE_DYNAMIC_QUARTER_4 = 'Q4';
    const AUTOFILTER_RULETYPE_DYNAMIC_ABOVEAVERAGE = 'aboveAverage';
    const AUTOFILTER_RULETYPE_DYNAMIC_BELOWAVERAGE = 'belowAverage';

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
    const AUTOFILTER_COLUMN_RULE_EQUAL = 'equal';
    const AUTOFILTER_COLUMN_RULE_NOTEQUAL = 'notEqual';
    const AUTOFILTER_COLUMN_RULE_GREATERTHAN = 'greaterThan';
    const AUTOFILTER_COLUMN_RULE_GREATERTHANOREQUAL = 'greaterThanOrEqual';
    const AUTOFILTER_COLUMN_RULE_LESSTHAN = 'lessThan';
    const AUTOFILTER_COLUMN_RULE_LESSTHANOREQUAL = 'lessThanOrEqual';

    private const OPERATORS = [
        self::AUTOFILTER_COLUMN_RULE_EQUAL,
        self::AUTOFILTER_COLUMN_RULE_NOTEQUAL,
        self::AUTOFILTER_COLUMN_RULE_GREATERTHAN,
        self::AUTOFILTER_COLUMN_RULE_GREATERTHANOREQUAL,
        self::AUTOFILTER_COLUMN_RULE_LESSTHAN,
        self::AUTOFILTER_COLUMN_RULE_LESSTHANOREQUAL,
    ];

    const AUTOFILTER_COLUMN_RULE_TOPTEN_BY_VALUE = 'byValue';
    const AUTOFILTER_COLUMN_RULE_TOPTEN_PERCENT = 'byPercent';

    private const TOP_TEN_VALUE = [
        self::AUTOFILTER_COLUMN_RULE_TOPTEN_BY_VALUE,
        self::AUTOFILTER_COLUMN_RULE_TOPTEN_PERCENT,
    ];

    const AUTOFILTER_COLUMN_RULE_TOPTEN_TOP = 'top';
    const AUTOFILTER_COLUMN_RULE_TOPTEN_BOTTOM = 'bottom';

    private const TOP_TEN_TYPE = [
        self::AUTOFILTER_COLUMN_RULE_TOPTEN_TOP,
        self::AUTOFILTER_COLUMN_RULE_TOPTEN_BOTTOM,
    ];

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
     * Autofilter Column.
     *
     * @var ?Column
     */
    private $parent;

    /**
     * Autofilter Rule Type.
     *
     * @var string
     */
    private $ruleType = self::AUTOFILTER_RULETYPE_FILTER;

    /**
     * Autofilter Rule Value.
     *
     * @var int|int[]|string|string[]
     */
    private $value = '';

    /**
     * Autofilter Rule Operator.
     *
     * @var string
     */
    private $operator = self::AUTOFILTER_COLUMN_RULE_EQUAL;

    /**
     * DateTimeGrouping Group Value.
     *
     * @var string
     */
    private $grouping = '';

    /**
     * Create a new Rule.
     */
    public function __construct(?Column $parent = null)
    {
        $this->parent = $parent;
    }

    private function setEvaluatedFalse(): void
    {
        if ($this->parent !== null) {
            $this->parent->setEvaluatedFalse();
        }
    }

    /**
     * Get AutoFilter Rule Type.
     *
     * @return string
     */
    public function getRuleType()
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
    public function setRuleType($ruleType)
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
    public function setValue($value)
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
     *
     * @return string
     */
    public function getOperator()
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
    public function setOperator($operator)
    {
        $this->setEvaluatedFalse();
        if (empty($operator)) {
            $operator = self::AUTOFILTER_COLUMN_RULE_EQUAL;
        }
        if (
            (!in_array($operator, self::OPERATORS)) &&
            (!in_array($operator, self::TOP_TEN_VALUE))
        ) {
            throw new PhpSpreadsheetException('Invalid operator for column AutoFilter Rule.');
        }
        $this->operator = $operator;

        return $this;
    }

    /**
     * Get AutoFilter Rule Grouping.
     *
     * @return string
     */
    public function getGrouping()
    {
        return $this->grouping;
    }

    /**
     * Set AutoFilter Rule Grouping.
     *
     * @param string $grouping
     *
     * @return $this
     */
    public function setGrouping($grouping)
    {
        $this->setEvaluatedFalse();
        if (
            ($grouping !== null) &&
            (!in_array($grouping, self::DATE_TIME_GROUPS)) &&
            (!in_array($grouping, self::DYNAMIC_TYPES)) &&
            (!in_array($grouping, self::TOP_TEN_TYPE))
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
     * @param string $grouping
     *
     * @return $this
     */
    public function setRule($operator, $value, $grouping = null)
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
     *
     * @return ?Column
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set this Rule's AutoFilter Column Parent.
     *
     * @return $this
     */
    public function setParent(?Column $parent = null)
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
