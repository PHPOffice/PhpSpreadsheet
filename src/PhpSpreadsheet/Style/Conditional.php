<?php

namespace PhpOffice\PhpSpreadsheet\Style;

use PhpOffice\PhpSpreadsheet\IComparable;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\ConditionalDataBar;

class Conditional implements IComparable
{
    // Condition types
    const CONDITION_NONE = 'none';
    const CONDITION_BEGINSWITH = 'beginsWith';
    const CONDITION_CELLIS = 'cellIs';
    const CONDITION_CONTAINSBLANKS = 'containsBlanks';
    const CONDITION_CONTAINSERRORS = 'containsErrors';
    const CONDITION_CONTAINSTEXT = 'containsText';
    const CONDITION_DATABAR = 'dataBar';
    const CONDITION_ENDSWITH = 'endsWith';
    const CONDITION_EXPRESSION = 'expression';
    const CONDITION_NOTCONTAINSBLANKS = 'notContainsBlanks';
    const CONDITION_NOTCONTAINSERRORS = 'notContainsErrors';
    const CONDITION_NOTCONTAINSTEXT = 'notContainsText';
    const CONDITION_TIMEPERIOD = 'timePeriod';
    const CONDITION_DUPLICATES = 'duplicateValues';
    const CONDITION_UNIQUE = 'uniqueValues';

    private const CONDITION_TYPES = [
        self::CONDITION_BEGINSWITH,
        self::CONDITION_CELLIS,
        self::CONDITION_CONTAINSBLANKS,
        self::CONDITION_CONTAINSERRORS,
        self::CONDITION_CONTAINSTEXT,
        self::CONDITION_DATABAR,
        self::CONDITION_DUPLICATES,
        self::CONDITION_ENDSWITH,
        self::CONDITION_EXPRESSION,
        self::CONDITION_NONE,
        self::CONDITION_NOTCONTAINSBLANKS,
        self::CONDITION_NOTCONTAINSERRORS,
        self::CONDITION_NOTCONTAINSTEXT,
        self::CONDITION_TIMEPERIOD,
        self::CONDITION_UNIQUE,
    ];

    // Operator types
    const OPERATOR_NONE = '';
    const OPERATOR_BEGINSWITH = 'beginsWith';
    const OPERATOR_ENDSWITH = 'endsWith';
    const OPERATOR_EQUAL = 'equal';
    const OPERATOR_GREATERTHAN = 'greaterThan';
    const OPERATOR_GREATERTHANOREQUAL = 'greaterThanOrEqual';
    const OPERATOR_LESSTHAN = 'lessThan';
    const OPERATOR_LESSTHANOREQUAL = 'lessThanOrEqual';
    const OPERATOR_NOTEQUAL = 'notEqual';
    const OPERATOR_CONTAINSTEXT = 'containsText';
    const OPERATOR_NOTCONTAINS = 'notContains';
    const OPERATOR_BETWEEN = 'between';
    const OPERATOR_NOTBETWEEN = 'notBetween';

    const TIMEPERIOD_TODAY = 'today';
    const TIMEPERIOD_YESTERDAY = 'yesterday';
    const TIMEPERIOD_TOMORROW = 'tomorrow';
    const TIMEPERIOD_LAST_7_DAYS = 'last7Days';
    const TIMEPERIOD_LAST_WEEK = 'lastWeek';
    const TIMEPERIOD_THIS_WEEK = 'thisWeek';
    const TIMEPERIOD_NEXT_WEEK = 'nextWeek';
    const TIMEPERIOD_LAST_MONTH = 'lastMonth';
    const TIMEPERIOD_THIS_MONTH = 'thisMonth';
    const TIMEPERIOD_NEXT_MONTH = 'nextMonth';

    /**
     * Condition type.
     *
     * @var string
     */
    private $conditionType = self::CONDITION_NONE;

    /**
     * Operator type.
     *
     * @var string
     */
    private $operatorType = self::OPERATOR_NONE;

    /**
     * Text.
     *
     * @var string
     */
    private $text;

    /**
     * Stop on this condition, if it matches.
     *
     * @var bool
     */
    private $stopIfTrue = false;

    /**
     * Condition.
     *
     * @var (bool|float|int|string)[]
     */
    private $condition = [];

    /**
     * @var ConditionalDataBar
     */
    private $dataBar;

    /**
     * Style.
     *
     * @var Style
     */
    private $style;

    /** @var bool */
    private $noFormatSet = false;

    /**
     * Create a new Conditional.
     */
    public function __construct()
    {
        // Initialise values
        $this->style = new Style(false, true);
    }

    public function getNoFormatSet(): bool
    {
        return $this->noFormatSet;
    }

    public function setNoFormatSet(bool $noFormatSet): self
    {
        $this->noFormatSet = $noFormatSet;

        return $this;
    }

    /**
     * Get Condition type.
     *
     * @return string
     */
    public function getConditionType()
    {
        return $this->conditionType;
    }

    /**
     * Set Condition type.
     *
     * @param string $type Condition type, see self::CONDITION_*
     *
     * @return $this
     */
    public function setConditionType($type)
    {
        $this->conditionType = $type;

        return $this;
    }

    /**
     * Get Operator type.
     *
     * @return string
     */
    public function getOperatorType()
    {
        return $this->operatorType;
    }

    /**
     * Set Operator type.
     *
     * @param string $type Conditional operator type, see self::OPERATOR_*
     *
     * @return $this
     */
    public function setOperatorType($type)
    {
        $this->operatorType = $type;

        return $this;
    }

    /**
     * Get text.
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set text.
     *
     * @param string $text
     *
     * @return $this
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get StopIfTrue.
     *
     * @return bool
     */
    public function getStopIfTrue()
    {
        return $this->stopIfTrue;
    }

    /**
     * Set StopIfTrue.
     *
     * @param bool $stopIfTrue
     *
     * @return $this
     */
    public function setStopIfTrue($stopIfTrue)
    {
        $this->stopIfTrue = $stopIfTrue;

        return $this;
    }

    /**
     * Get Conditions.
     *
     * @return (bool|float|int|string)[]
     */
    public function getConditions()
    {
        return $this->condition;
    }

    /**
     * Set Conditions.
     *
     * @param bool|float|int|string|(bool|float|int|string)[] $conditions Condition
     *
     * @return $this
     */
    public function setConditions($conditions)
    {
        if (!is_array($conditions)) {
            $conditions = [$conditions];
        }
        $this->condition = $conditions;

        return $this;
    }

    /**
     * Add Condition.
     *
     * @param bool|float|int|string $condition Condition
     *
     * @return $this
     */
    public function addCondition($condition)
    {
        $this->condition[] = $condition;

        return $this;
    }

    /**
     * Get Style.
     *
     * @return Style
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * Set Style.
     *
     * @return $this
     */
    public function setStyle(Style $style)
    {
        $this->style = $style;

        return $this;
    }

    /**
     * get DataBar.
     *
     * @return null|ConditionalDataBar
     */
    public function getDataBar()
    {
        return $this->dataBar;
    }

    /**
     * set DataBar.
     *
     * @return $this
     */
    public function setDataBar(ConditionalDataBar $dataBar)
    {
        $this->dataBar = $dataBar;

        return $this;
    }

    /**
     * Get hash code.
     *
     * @return string Hash code
     */
    public function getHashCode()
    {
        return md5(
            $this->conditionType .
            $this->operatorType .
            implode(';', $this->condition) .
            $this->style->getHashCode() .
            __CLASS__
        );
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if (is_object($value)) {
                $this->$key = clone $value;
            } else {
                $this->$key = $value;
            }
        }
    }

    /**
     * Verify if param is valid condition type.
     */
    public static function isValidConditionType(string $type): bool
    {
        return in_array($type, self::CONDITION_TYPES);
    }
}
