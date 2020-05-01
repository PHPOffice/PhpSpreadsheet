<?php

namespace PhpOffice\PhpSpreadsheet\Style;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\IComparable;
use PhpOffice\PhpSpreadsheet\ReferenceHelper;

class Conditional implements IComparable
{
    // Condition types
    const CONDITION_NONE = 'none';
    const CONDITION_CELLIS = 'cellIs';
    const CONDITION_CONTAINSTEXT = 'containsText';
    const CONDITION_EXPRESSION = 'expression';
    const CONDITION_CONTAINSBLANKS = 'containsBlanks';
    const CONDITION_NOTCONTAINSBLANKS = 'notContainsBlanks';

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
     * @var string[]
     */
    private $condition = [];

    /**
     * Style.
     *
     * @var Style
     */
    private $style;

    /**
     * Create a new Conditional.
     */
    public function __construct()
    {
        // Initialise values
        $this->style = new Style(false, true);
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
     * @param string $pValue Condition type, see self::CONDITION_*
     *
     * @return Conditional
     */
    public function setConditionType($pValue)
    {
        $this->conditionType = $pValue;

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
     * @param string $pValue Conditional operator type, see self::OPERATOR_*
     *
     * @return Conditional
     */
    public function setOperatorType($pValue)
    {
        $this->operatorType = $pValue;

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
     * @param string $value
     *
     * @return Conditional
     */
    public function setText($value)
    {
        $this->text = $value;

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
     * @param bool $value
     *
     * @return Conditional
     */
    public function setStopIfTrue($value)
    {
        $this->stopIfTrue = $value;

        return $this;
    }

    /**
     * Get Conditions.
     *
     * @return string[]
     */
    public function getConditions()
    {
        return $this->condition;
    }

    /**
     * Set Conditions.
     *
     * @param string[] $pValue Condition
     *
     * @return Conditional
     */
    public function setConditions($pValue)
    {
        if (!is_array($pValue)) {
            $pValue = [$pValue];
        }
        $this->condition = $pValue;

        return $this;
    }

    /**
     * Add Condition.
     *
     * @param string $pValue Condition
     *
     * @return Conditional
     */
    public function addCondition($pValue)
    {
        $this->condition[] = $pValue;

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
     * @param Style $pValue
     *
     * @return Conditional
     */
    public function setStyle(Style $pValue = null)
    {
        $this->style = $pValue;

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
     * Checks if a conditional formatting is active.
     * Limitated to mathematical/numeric conditionals (<, <=, ==, !=, >=, >, between, notBetween )
     * updateConditionalRowBy is the count of connected rows to the conditional formatting.
     * updateConditionalColumBy is the count of connected colums to the conditional formatting.
     * Excel manges to have conditions that look like for e.g. =$E$5:$E$14 NOT Between -$D$5-0.4 And $D$5+0.4
     * That means the condtion will return -$D$5-0.4 and $D$5+0.4 everytime, but this must be adjusted in case of cell $E$6 to -$D$6-0.4 And $D$6+0.4
     * bccomp is used in case of floats comparison with a precision in number of decimalplaces.
     *
     * @param Calculation $calcer
     * @param Cell $cell
     * @param string $cellName
     * @param int $precision
     * @param int $updateConditionalColumBy
     * @param int $updateConditionalRowBy
     *
     * @return bool
     */
    public function isActive(Calculation $calcer, Cell $cell, int $precision = 8, int $updateConditionalColumBy = 0, int $updateConditionalRowBy = 0)
    {
        $return = false;

        // There should be only one (by <, <=, ==, !=, >=, >) or two conditions (between, notBetween)
        $conditions = updateCellConditionals($updateConditionalColumBy, $updateConditionalRowBy);
        $calcVal = $calcer->calculate($cell);
        $coords1Val = self::unwrapCalcFormRes($calcer->calculateFormula($conditions[0]));

        switch ($this->getOperatorType()) {
            case self::OPERATOR_LESSTHAN:
                $return = bccomp($calcVal, $coords1Val, $precision) < 0;

                break;
            case self::OPERATOR_LESSTHANOREQUAL:
                $return = bccomp($calcVal, $coords1Val, $precision) <= 0;

                break;
            case self::OPERATOR_EQUAL:
                $return = bccomp($calcVal, $coords1Val, $precision) == 0;

                break;
            case self::OPERATOR_NOTEQUAL:
                $return = bccomp($calcVal, $coords1Val, $precision) != 0;

                break;
            case self::OPERATOR_GREATERTHANOREQUAL:
                $return = bccomp($calcVal, $coords1Val, $precision) >= 0;

                break;
            case self::OPERATOR_GREATERTHAN:
                $return = bccomp($calcVal, $coords1Val, $precision) > 0;

                break;
            case self::OPERATOR_BETWEEN:
                $coords2Val = self::unwrapCalcFormRes($calcer->calculateFormula($conditions[1]));
                if ($coords1Val <= $coords2Val) {
                    $return = (bccomp($calcVal, $coords1Val, $precision) >= 0 && bccomp($calcVal, $coords2Val, $precision) <= 0);
                } else {
                    $return = (bccomp($calcVal, $coords2Val, $precision) >= 0 && bccomp($calcVal, $coords1Val, $precision) <= 0);
                }

                break;
            case self::OPERATOR_NOTBETWEEN:

                $coords2Val = self::unwrapCalcFormRes($calcer->calculateFormula($conditions[1]));
                if ($coords1Val <= $coords2Val) {
                    $return = (!(bccomp($calcVal, $coords1Val, $precision) <= 0 || bccomp($calcVal, $coords2Val, $precision) <= 0));
                } else {
                    $return = (!(bccomp($calcVal, $coords2Val, $precision) <= 0 || bccomp($calcVal, $coords1Val, $precision) <= 0));
                }

                break;
            case '':
                break;
            default:
                // This Code should never be done
                throw new Exception('Unknown ' . $this->getOperatorType() . ' during active check of condtional formatting.');
        }

        return $return;
    }

    /**
     * Simplify calculation result.
     *
     * @param mixed $calcRes
     *
     * @return mixed
     */
    private static function unwrapCalcFormRes($calcRes)
    {
        $res = $calcRes;
        if (is_array($res)) {
            $newRes = array_map('array_values', array_values($res));
            $res = $newRes[0][0];
        }

        return $res;
    }

    /**
     * Updates References of conditions by updateConditionalColumBy/updateConditionalRowBy use of conditional statement.
     *
     * @param int $updateConditionalColumBy
     * @param int $updateConditionalRowBy
     * @param bool $makeFormula
     *
     * @return array
     */
    public function updateCellConditionals(int $updateConditionalColumBy, int $updateConditionalRowBy, bool $makeFormula = true)
    {
        $referenceHelper = ReferenceHelper::getInstance(); // Update Internal References
        $conditions = $this->getConditions();

        // Adjust updateConditionalColumBy/updateConditionalColumBy condtions means condtionas that
        $count = count($conditions);
        for ($i = 0; $i < $count; ++$i) {
            if ($updateConditionalColumBy !== false || $updateConditionalRowBy !== false) {
                $conditions[$i] = $referenceHelper->updateFormulaReferences($conditions[$i], 'A1', $updateConditionalColumBy, $updateConditionalRowBy);
            }
            if ($makeFormula == true) {
                $conditions[$i] = makeUsableFormula($conditions[$i]);
            }
        }

        return $conditions;
    }

    /**
     * Make a formula out of condition string if needed.
     *
     * @param string $condition
     *
     * @return string
     */
    private static function makeUsableFormula(string $condition)
    {
        // Make usable formular if needed
        if (substr($condition, 0, 1) != '=') {
            $condition = '=' . $condition;
        }

        return $condition;
    }
}
