<?php

namespace PhpOffice\PhpSpreadsheet\Style;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\IComparable;
use PhpOffice\PhpSpreadsheet\Style;

/**
 * Copyright (c) 2006 - 2016 PhpSpreadsheet.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @category   PhpSpreadsheet
 *
 * @copyright  Copyright (c) 2006 - 2016 PhpSpreadsheet (https://github.com/PHPOffice/PhpSpreadsheet)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 */
class Conditional implements IComparable
{
    /* Condition types */
    const CONDITION_NONE = 'none';
    const CONDITION_CELLIS = 'cellIs';
    const CONDITION_CONTAINSTEXT = 'containsText';
    const CONDITION_EXPRESSION = 'expression';
    const CONDITION_CONTAINSBLANKS = 'containsBlanks';

    /* Operator types */
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

    /**
     * Condition type.
     *
     * @var string
     */
    private $conditionType;

    /**
     * Operator type.
     *
     * @var string
     */
    private $operatorType;

    /**
     * Text.
     *
     * @var string
     */
    private $text;

    /**
     * Condition.
     *
     * @var string[]
     */
    private $condition = [];

    /**
     * Style.
     *
     * @var \PhpOffice\PhpSpreadsheet\Style
     */
    private $style;

    /**
     * Create a new Conditional.
     */
    public function __construct()
    {
        // Initialise values
        $this->conditionType = self::CONDITION_NONE;
        $this->operatorType = self::OPERATOR_NONE;
        $this->text = null;
        $this->condition = [];
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
     * @return \PhpOffice\PhpSpreadsheet\Style
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
     * @throws PhpSpreadsheetException
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
}
