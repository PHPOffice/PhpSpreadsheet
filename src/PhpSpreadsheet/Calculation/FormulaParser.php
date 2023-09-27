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
 * https://ewbi.blogs.com/develops/2007/03/excel_formula_p.html
 * https://ewbi.blogs.com/develops/2004/12/excel_formula_p.html
 */
class FormulaParser
{
    // Character constants
    const QUOTE_DOUBLE = '"';
    const QUOTE_SINGLE = '\'';
    const BRACKET_CLOSE = ']';
    const BRACKET_OPEN = '[';
    const BRACE_OPEN = '{';
    const BRACE_CLOSE = '}';
    const PAREN_OPEN = '(';
    const PAREN_CLOSE = ')';
    const SEMICOLON = ';';
    const WHITESPACE = ' ';
    const COMMA = ',';
    const ERROR_START = '#';

    const OPERATORS_SN = '+-';
    const OPERATORS_INFIX = '+-*/^&=><';
    const OPERATORS_POSTFIX = '%';

    /**
     * Formula.
     */
    private string $formula;

    /**
     * Tokens.
     *
     * @var FormulaToken[]
     */
    private $tokens = [];

    /**
     * Create a new FormulaParser.
     *
     * @param ?string $formula Formula to parse
     */
    public function __construct($formula = '')
    {
        // Check parameters
        if ($formula === null) {
            throw new Exception('Invalid parameter passed: formula');
        }

        // Initialise values
        $this->formula = trim($formula);
        // Parse!
        $this->parseToTokens();
    }

    /**
     * Get Formula.
     *
     * @return string
     */
    public function getFormula()
    {
        return $this->formula;
    }

    /**
     * Get Token.
     *
     * @param int $id Token id
     */
    public function getToken(int $id = 0): FormulaToken
    {
        if (isset($this->tokens[$id])) {
            return $this->tokens[$id];
        }

        throw new Exception("Token with id $id does not exist.");
    }

    /**
     * Get Token count.
     */
    public function getTokenCount(): int
    {
        return count($this->tokens);
    }

    /**
     * Get Tokens.
     *
     * @return FormulaToken[]
     */
    public function getTokens()
    {
        return $this->tokens;
    }

    /**
     * Parse to tokens.
     */
    private function parseToTokens(): void
    {
        // No attempt is made to verify formulas; assumes formulas are derived from Excel, where
        // they can only exist if valid; stack overflows/underflows sunk as nulls without exceptions.

        // Check if the formula has a valid starting =
        $formulaLength = strlen($this->formula);
        if ($formulaLength < 2 || $this->formula[0] != '=') {
            return;
        }

        // Helper variables
        $tokens1 = $tokens2 = $stack = [];
        $inString = $inPath = $inRange = $inError = false;
        $nextToken = null;
        //$token = $previousToken = null;

        $index = 1;
        $value = '';

        $ERRORS = ['#NULL!', '#DIV/0!', '#VALUE!', '#REF!', '#NAME?', '#NUM!', '#N/A'];
        $COMPARATORS_MULTI = ['>=', '<=', '<>'];

        while ($index < $formulaLength) {
            // state-dependent character evaluation (order is important)

            // double-quoted strings
            // embeds are doubled
            // end marks token
            if ($inString) {
                if ($this->formula[$index] == self::QUOTE_DOUBLE) {
                    if ((($index + 2) <= $formulaLength) && ($this->formula[$index + 1] == self::QUOTE_DOUBLE)) {
                        $value .= self::QUOTE_DOUBLE;
                        ++$index;
                    } else {
                        $inString = false;
                        $tokens1[] = new FormulaToken($value, FormulaToken::TOKEN_TYPE_OPERAND, FormulaToken::TOKEN_SUBTYPE_TEXT);
                        $value = '';
                    }
                } else {
                    $value .= $this->formula[$index];
                }
                ++$index;

                continue;
            }

            // single-quoted strings (links)
            // embeds are double
            // end does not mark a token
            if ($inPath) {
                if ($this->formula[$index] == self::QUOTE_SINGLE) {
                    if ((($index + 2) <= $formulaLength) && ($this->formula[$index + 1] == self::QUOTE_SINGLE)) {
                        $value .= self::QUOTE_SINGLE;
                        ++$index;
                    } else {
                        $inPath = false;
                    }
                } else {
                    $value .= $this->formula[$index];
                }
                ++$index;

                continue;
            }

            // bracked strings (R1C1 range index or linked workbook name)
            // no embeds (changed to "()" by Excel)
            // end does not mark a token
            if ($inRange) {
                if ($this->formula[$index] == self::BRACKET_CLOSE) {
                    $inRange = false;
                }
                $value .= $this->formula[$index];
                ++$index;

                continue;
            }

            // error values
            // end marks a token, determined from absolute list of values
            if ($inError) {
                $value .= $this->formula[$index];
                ++$index;
                if (in_array($value, $ERRORS)) {
                    $inError = false;
                    $tokens1[] = new FormulaToken($value, FormulaToken::TOKEN_TYPE_OPERAND, FormulaToken::TOKEN_SUBTYPE_ERROR);
                    $value = '';
                }

                continue;
            }

            // scientific notation check
            if (str_contains(self::OPERATORS_SN, $this->formula[$index])) {
                if (strlen($value) > 1) {
                    if (preg_match('/^[1-9]{1}(\\.\\d+)?E{1}$/', $this->formula[$index]) != 0) {
                        $value .= $this->formula[$index];
                        ++$index;

                        continue;
                    }
                }
            }

            // independent character evaluation (order not important)

            // establish state-dependent character evaluations
            if ($this->formula[$index] == self::QUOTE_DOUBLE) {
                if ($value !== '') {
                    // unexpected
                    $tokens1[] = new FormulaToken($value, FormulaToken::TOKEN_TYPE_UNKNOWN);
                    $value = '';
                }
                $inString = true;
                ++$index;

                continue;
            }

            if ($this->formula[$index] == self::QUOTE_SINGLE) {
                if ($value !== '') {
                    // unexpected
                    $tokens1[] = new FormulaToken($value, FormulaToken::TOKEN_TYPE_UNKNOWN);
                    $value = '';
                }
                $inPath = true;
                ++$index;

                continue;
            }

            if ($this->formula[$index] == self::BRACKET_OPEN) {
                $inRange = true;
                $value .= self::BRACKET_OPEN;
                ++$index;

                continue;
            }

            if ($this->formula[$index] == self::ERROR_START) {
                if ($value !== '') {
                    // unexpected
                    $tokens1[] = new FormulaToken($value, FormulaToken::TOKEN_TYPE_UNKNOWN);
                    $value = '';
                }
                $inError = true;
                $value .= self::ERROR_START;
                ++$index;

                continue;
            }

            // mark start and end of arrays and array rows
            if ($this->formula[$index] == self::BRACE_OPEN) {
                if ($value !== '') {
                    // unexpected
                    $tokens1[] = new FormulaToken($value, FormulaToken::TOKEN_TYPE_UNKNOWN);
                    $value = '';
                }

                $tmp = new FormulaToken('ARRAY', FormulaToken::TOKEN_TYPE_FUNCTION, FormulaToken::TOKEN_SUBTYPE_START);
                $tokens1[] = $tmp;
                $stack[] = clone $tmp;

                $tmp = new FormulaToken('ARRAYROW', FormulaToken::TOKEN_TYPE_FUNCTION, FormulaToken::TOKEN_SUBTYPE_START);
                $tokens1[] = $tmp;
                $stack[] = clone $tmp;

                ++$index;

                continue;
            }

            if ($this->formula[$index] == self::SEMICOLON) {
                if ($value !== '') {
                    $tokens1[] = new FormulaToken($value, FormulaToken::TOKEN_TYPE_OPERAND);
                    $value = '';
                }

                /** @var FormulaToken $tmp */
                $tmp = array_pop($stack);
                $tmp->setValue('');
                $tmp->setTokenSubType(FormulaToken::TOKEN_SUBTYPE_STOP);
                $tokens1[] = $tmp;

                $tmp = new FormulaToken(',', FormulaToken::TOKEN_TYPE_ARGUMENT);
                $tokens1[] = $tmp;

                $tmp = new FormulaToken('ARRAYROW', FormulaToken::TOKEN_TYPE_FUNCTION, FormulaToken::TOKEN_SUBTYPE_START);
                $tokens1[] = $tmp;
                $stack[] = clone $tmp;

                ++$index;

                continue;
            }

            if ($this->formula[$index] == self::BRACE_CLOSE) {
                if ($value !== '') {
                    $tokens1[] = new FormulaToken($value, FormulaToken::TOKEN_TYPE_OPERAND);
                    $value = '';
                }

                /** @var FormulaToken $tmp */
                $tmp = array_pop($stack);
                $tmp->setValue('');
                $tmp->setTokenSubType(FormulaToken::TOKEN_SUBTYPE_STOP);
                $tokens1[] = $tmp;

                /** @var FormulaToken $tmp */
                $tmp = array_pop($stack);
                $tmp->setValue('');
                $tmp->setTokenSubType(FormulaToken::TOKEN_SUBTYPE_STOP);
                $tokens1[] = $tmp;

                ++$index;

                continue;
            }

            // trim white-space
            if ($this->formula[$index] == self::WHITESPACE) {
                if ($value !== '') {
                    $tokens1[] = new FormulaToken($value, FormulaToken::TOKEN_TYPE_OPERAND);
                    $value = '';
                }
                $tokens1[] = new FormulaToken('', FormulaToken::TOKEN_TYPE_WHITESPACE);
                ++$index;
                while (($this->formula[$index] == self::WHITESPACE) && ($index < $formulaLength)) {
                    ++$index;
                }

                continue;
            }

            // multi-character comparators
            if (($index + 2) <= $formulaLength) {
                if (in_array(substr($this->formula, $index, 2), $COMPARATORS_MULTI)) {
                    if ($value !== '') {
                        $tokens1[] = new FormulaToken($value, FormulaToken::TOKEN_TYPE_OPERAND);
                        $value = '';
                    }
                    $tokens1[] = new FormulaToken(substr($this->formula, $index, 2), FormulaToken::TOKEN_TYPE_OPERATORINFIX, FormulaToken::TOKEN_SUBTYPE_LOGICAL);
                    $index += 2;

                    continue;
                }
            }

            // standard infix operators
            if (str_contains(self::OPERATORS_INFIX, $this->formula[$index])) {
                if ($value !== '') {
                    $tokens1[] = new FormulaToken($value, FormulaToken::TOKEN_TYPE_OPERAND);
                    $value = '';
                }
                $tokens1[] = new FormulaToken($this->formula[$index], FormulaToken::TOKEN_TYPE_OPERATORINFIX);
                ++$index;

                continue;
            }

            // standard postfix operators (only one)
            if (str_contains(self::OPERATORS_POSTFIX, $this->formula[$index])) {
                if ($value !== '') {
                    $tokens1[] = new FormulaToken($value, FormulaToken::TOKEN_TYPE_OPERAND);
                    $value = '';
                }
                $tokens1[] = new FormulaToken($this->formula[$index], FormulaToken::TOKEN_TYPE_OPERATORPOSTFIX);
                ++$index;

                continue;
            }

            // start subexpression or function
            if ($this->formula[$index] == self::PAREN_OPEN) {
                if ($value !== '') {
                    $tmp = new FormulaToken($value, FormulaToken::TOKEN_TYPE_FUNCTION, FormulaToken::TOKEN_SUBTYPE_START);
                    $tokens1[] = $tmp;
                    $stack[] = clone $tmp;
                    $value = '';
                } else {
                    $tmp = new FormulaToken('', FormulaToken::TOKEN_TYPE_SUBEXPRESSION, FormulaToken::TOKEN_SUBTYPE_START);
                    $tokens1[] = $tmp;
                    $stack[] = clone $tmp;
                }
                ++$index;

                continue;
            }

            // function, subexpression, or array parameters, or operand unions
            if ($this->formula[$index] == self::COMMA) {
                if ($value !== '') {
                    $tokens1[] = new FormulaToken($value, FormulaToken::TOKEN_TYPE_OPERAND);
                    $value = '';
                }

                /** @var FormulaToken $tmp */
                $tmp = array_pop($stack);
                $tmp->setValue('');
                $tmp->setTokenSubType(FormulaToken::TOKEN_SUBTYPE_STOP);
                $stack[] = $tmp;

                if ($tmp->getTokenType() == FormulaToken::TOKEN_TYPE_FUNCTION) {
                    $tokens1[] = new FormulaToken(',', FormulaToken::TOKEN_TYPE_OPERATORINFIX, FormulaToken::TOKEN_SUBTYPE_UNION);
                } else {
                    $tokens1[] = new FormulaToken(',', FormulaToken::TOKEN_TYPE_ARGUMENT);
                }
                ++$index;

                continue;
            }

            // stop subexpression
            if ($this->formula[$index] == self::PAREN_CLOSE) {
                if ($value !== '') {
                    $tokens1[] = new FormulaToken($value, FormulaToken::TOKEN_TYPE_OPERAND);
                    $value = '';
                }

                /** @var FormulaToken $tmp */
                $tmp = array_pop($stack);
                $tmp->setValue('');
                $tmp->setTokenSubType(FormulaToken::TOKEN_SUBTYPE_STOP);
                $tokens1[] = $tmp;

                ++$index;

                continue;
            }

            // token accumulation
            $value .= $this->formula[$index];
            ++$index;
        }

        // dump remaining accumulation
        if ($value !== '') {
            $tokens1[] = new FormulaToken($value, FormulaToken::TOKEN_TYPE_OPERAND);
        }

        // move tokenList to new set, excluding unnecessary white-space tokens and converting necessary ones to intersections
        $tokenCount = count($tokens1);
        for ($i = 0; $i < $tokenCount; ++$i) {
            $token = $tokens1[$i];
            if (isset($tokens1[$i - 1])) {
                $previousToken = $tokens1[$i - 1];
            } else {
                $previousToken = null;
            }
            if (isset($tokens1[$i + 1])) {
                $nextToken = $tokens1[$i + 1];
            } else {
                $nextToken = null;
            }

            if ($token->getTokenType() != FormulaToken::TOKEN_TYPE_WHITESPACE) {
                $tokens2[] = $token;

                continue;
            }

            if ($previousToken === null) {
                continue;
            }

            if (
                !(
                    (($previousToken->getTokenType() == FormulaToken::TOKEN_TYPE_FUNCTION) && ($previousToken->getTokenSubType() == FormulaToken::TOKEN_SUBTYPE_STOP))
                || (($previousToken->getTokenType() == FormulaToken::TOKEN_TYPE_SUBEXPRESSION) && ($previousToken->getTokenSubType() == FormulaToken::TOKEN_SUBTYPE_STOP))
                || ($previousToken->getTokenType() == FormulaToken::TOKEN_TYPE_OPERAND)
                )
            ) {
                continue;
            }

            if ($nextToken === null) {
                continue;
            }

            if (
                !(
                    (($nextToken->getTokenType() == FormulaToken::TOKEN_TYPE_FUNCTION) && ($nextToken->getTokenSubType() == FormulaToken::TOKEN_SUBTYPE_START))
                || (($nextToken->getTokenType() == FormulaToken::TOKEN_TYPE_SUBEXPRESSION) && ($nextToken->getTokenSubType() == FormulaToken::TOKEN_SUBTYPE_START))
                || ($nextToken->getTokenType() == FormulaToken::TOKEN_TYPE_OPERAND)
                )
            ) {
                continue;
            }

            $tokens2[] = new FormulaToken($value, FormulaToken::TOKEN_TYPE_OPERATORINFIX, FormulaToken::TOKEN_SUBTYPE_INTERSECTION);
        }

        // move tokens to final list, switching infix "-" operators to prefix when appropriate, switching infix "+" operators
        // to noop when appropriate, identifying operand and infix-operator subtypes, and pulling "@" from function names
        $this->tokens = [];

        $tokenCount = count($tokens2);
        for ($i = 0; $i < $tokenCount; ++$i) {
            $token = $tokens2[$i];
            if (isset($tokens2[$i - 1])) {
                $previousToken = $tokens2[$i - 1];
            } else {
                $previousToken = null;
            }

            if ($token->getTokenType() == FormulaToken::TOKEN_TYPE_OPERATORINFIX && $token->getValue() == '-') {
                if ($i == 0) {
                    $token->setTokenType(FormulaToken::TOKEN_TYPE_OPERATORPREFIX);
                } elseif (
                    (($previousToken->getTokenType() == FormulaToken::TOKEN_TYPE_FUNCTION)
                        && ($previousToken->getTokenSubType() == FormulaToken::TOKEN_SUBTYPE_STOP))
                    || (($previousToken->getTokenType() == FormulaToken::TOKEN_TYPE_SUBEXPRESSION)
                        && ($previousToken->getTokenSubType() == FormulaToken::TOKEN_SUBTYPE_STOP))
                    || ($previousToken->getTokenType() == FormulaToken::TOKEN_TYPE_OPERATORPOSTFIX)
                    || ($previousToken->getTokenType() == FormulaToken::TOKEN_TYPE_OPERAND)
                ) {
                    $token->setTokenSubType(FormulaToken::TOKEN_SUBTYPE_MATH);
                } else {
                    $token->setTokenType(FormulaToken::TOKEN_TYPE_OPERATORPREFIX);
                }

                $this->tokens[] = $token;

                continue;
            }

            if ($token->getTokenType() == FormulaToken::TOKEN_TYPE_OPERATORINFIX && $token->getValue() == '+') {
                if ($i == 0) {
                    continue;
                } elseif (
                    (($previousToken->getTokenType() == FormulaToken::TOKEN_TYPE_FUNCTION)
                        && ($previousToken->getTokenSubType() == FormulaToken::TOKEN_SUBTYPE_STOP))
                    || (($previousToken->getTokenType() == FormulaToken::TOKEN_TYPE_SUBEXPRESSION)
                        && ($previousToken->getTokenSubType() == FormulaToken::TOKEN_SUBTYPE_STOP))
                    || ($previousToken->getTokenType() == FormulaToken::TOKEN_TYPE_OPERATORPOSTFIX)
                    || ($previousToken->getTokenType() == FormulaToken::TOKEN_TYPE_OPERAND)
                ) {
                    $token->setTokenSubType(FormulaToken::TOKEN_SUBTYPE_MATH);
                } else {
                    continue;
                }

                $this->tokens[] = $token;

                continue;
            }

            if (
                $token->getTokenType() == FormulaToken::TOKEN_TYPE_OPERATORINFIX
                && $token->getTokenSubType() == FormulaToken::TOKEN_SUBTYPE_NOTHING
            ) {
                if (str_contains('<>=', substr($token->getValue(), 0, 1))) {
                    $token->setTokenSubType(FormulaToken::TOKEN_SUBTYPE_LOGICAL);
                } elseif ($token->getValue() == '&') {
                    $token->setTokenSubType(FormulaToken::TOKEN_SUBTYPE_CONCATENATION);
                } else {
                    $token->setTokenSubType(FormulaToken::TOKEN_SUBTYPE_MATH);
                }

                $this->tokens[] = $token;

                continue;
            }

            if (
                $token->getTokenType() == FormulaToken::TOKEN_TYPE_OPERAND
                && $token->getTokenSubType() == FormulaToken::TOKEN_SUBTYPE_NOTHING
            ) {
                if (!is_numeric($token->getValue())) {
                    if (strtoupper($token->getValue()) == 'TRUE' || strtoupper($token->getValue()) == 'FALSE') {
                        $token->setTokenSubType(FormulaToken::TOKEN_SUBTYPE_LOGICAL);
                    } else {
                        $token->setTokenSubType(FormulaToken::TOKEN_SUBTYPE_RANGE);
                    }
                } else {
                    $token->setTokenSubType(FormulaToken::TOKEN_SUBTYPE_NUMBER);
                }

                $this->tokens[] = $token;

                continue;
            }

            if ($token->getTokenType() == FormulaToken::TOKEN_TYPE_FUNCTION) {
                if ($token->getValue() !== '') {
                    if (str_starts_with($token->getValue(), '@')) {
                        $token->setValue(substr($token->getValue(), 1));
                    }
                }
            }

            $this->tokens[] = $token;
        }
    }
}
