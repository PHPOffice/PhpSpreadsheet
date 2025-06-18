<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

/**
 * PARTLY BASED ON:
 * Copyright (c) 2007 E. W. Bachtal, Inc.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software
 * and associated documentation files (the "Software"), to deal in the Software without restriction,
 * including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial
 * portions of the Software.
 *
 * The software is provided "as is", without warranty of any kind, express or implied, including but not
 * limited to the warranties of merchantability, fitness for a particular purpose and noninfringement. In
 * no event shall the authors or copyright holders be liable for any claim, damages or other liability,
 * whether in an action of contract, tort or otherwise, arising from, out of or in connection with the
 * software or the use or other dealings in the software.
 *
 * http://ewbi.blogs.com/develops/2007/03/excel_formula_p.html
 * http://ewbi.blogs.com/develops/2004/12/excel_formula_p.html
 */
class FormulaToken
{
    // Token types
    const TOKEN_TYPE_NOOP = 'Noop';
    const TOKEN_TYPE_OPERAND = 'Operand';
    const TOKEN_TYPE_FUNCTION = 'Function';
    const TOKEN_TYPE_SUBEXPRESSION = 'Subexpression';
    const TOKEN_TYPE_ARGUMENT = 'Argument';
    const TOKEN_TYPE_OPERATORPREFIX = 'OperatorPrefix';
    const TOKEN_TYPE_OPERATORINFIX = 'OperatorInfix';
    const TOKEN_TYPE_OPERATORPOSTFIX = 'OperatorPostfix';
    const TOKEN_TYPE_WHITESPACE = 'Whitespace';
    const TOKEN_TYPE_UNKNOWN = 'Unknown';

    // Token subtypes
    const TOKEN_SUBTYPE_NOTHING = 'Nothing';
    const TOKEN_SUBTYPE_START = 'Start';
    const TOKEN_SUBTYPE_STOP = 'Stop';
    const TOKEN_SUBTYPE_TEXT = 'Text';
    const TOKEN_SUBTYPE_NUMBER = 'Number';
    const TOKEN_SUBTYPE_LOGICAL = 'Logical';
    const TOKEN_SUBTYPE_ERROR = 'Error';
    const TOKEN_SUBTYPE_RANGE = 'Range';
    const TOKEN_SUBTYPE_MATH = 'Math';
    const TOKEN_SUBTYPE_CONCATENATION = 'Concatenation';
    const TOKEN_SUBTYPE_INTERSECTION = 'Intersection';
    const TOKEN_SUBTYPE_UNION = 'Union';

    /**
     * Value.
     *
     * @var string
     */
    private $value;

    /**
     * Token Type (represented by TOKEN_TYPE_*).
     *
     * @var string
     */
    private $tokenType;

    /**
     * Token SubType (represented by TOKEN_SUBTYPE_*).
     *
     * @var string
     */
    private $tokenSubType;

    /**
     * Create a new FormulaToken.
     *
     * @param string $pValue
     * @param string $pTokenType Token type (represented by TOKEN_TYPE_*)
     * @param string $pTokenSubType Token Subtype (represented by TOKEN_SUBTYPE_*)
     */
    public function __construct($pValue, $pTokenType = self::TOKEN_TYPE_UNKNOWN, $pTokenSubType = self::TOKEN_SUBTYPE_NOTHING)
    {
        // Initialise values
        $this->value = $pValue;
        $this->tokenType = $pTokenType;
        $this->tokenSubType = $pTokenSubType;
    }

    /**
     * Get Value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set Value.
     *
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Get Token Type (represented by TOKEN_TYPE_*).
     *
     * @return string
     */
    public function getTokenType()
    {
        return $this->tokenType;
    }

    /**
     * Set Token Type (represented by TOKEN_TYPE_*).
     *
     * @param string $value
     */
    public function setTokenType($value)
    {
        $this->tokenType = $value;
    }

    /**
     * Get Token SubType (represented by TOKEN_SUBTYPE_*).
     *
     * @return string
     */
    public function getTokenSubType()
    {
        return $this->tokenSubType;
    }

    /**
     * Set Token SubType (represented by TOKEN_SUBTYPE_*).
     *
     * @param string $value
     */
    public function setTokenSubType($value)
    {
        $this->tokenSubType = $value;
    }
}
