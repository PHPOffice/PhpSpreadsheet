<?php

namespace PhpOffice\PhpSpreadsheet\Helper\NumberFormat;

abstract class BaseNumberFormatter
{
    protected const MASK_SEPARATOR = ';';
    protected const THOUSANDS_SEPARATOR = ',';
    protected const DECIMAL_SEPARATOR = '.';

    public const NON_BREAKING_SPACE = ' ';

    protected const SIGN_POSITIVE = '+';
    protected const SIGN_NEGATIVE = '-';

    protected const DIGIT_ALWAYS_DISPLAY = '0';
    protected const DIGIT_OPTIONAL_DISPLAY = '#';
    protected const DIGIT_POSITIONAL = '?';

    protected const MASK_POSITIVE_VALUE = 0;
    protected const MASK_NEGATIVE_VALUE = 1;
    protected const MASK_ZERO_VALUE = 2;

    abstract protected function zeroValueMask(): string;

    protected function setZeroValueMask(string $positiveMask): string
    {
        $zeroMask = $this->zeroValueMask();
        $mask = preg_replace('/((?:[#,]*0)(?:\.[0#]*)?)/u', $zeroMask, $positiveMask);

        if ($this->displayPositiveSign) {
            $pattern = $this->trailingSign
                ? $this->signSeparator . self::SIGN_POSITIVE
                : self::SIGN_POSITIVE . $this->signSeparator;
            $pattern = '/' . preg_quote($pattern) . '/u';
            $mask = preg_replace($pattern, '', $mask);
        }

        return $mask;
    }

    protected const DECIMAL_PATTERN_MASK = '/' .
        '(?:0\\' . self::DECIMAL_SEPARATOR . ')(0*)' .
        '/u';

    protected function setDecimalsMasking(string $mask): string
    {
        $mask = rtrim($mask, '#');

        // Set requested number of decimal places
        $decimalReplacement = ($this->decimals === 0)
            ? '0'
            : '0' . self::DECIMAL_SEPARATOR . str_repeat('0', $this->decimals);

        return preg_replace(self::DECIMAL_PATTERN_MASK, $decimalReplacement, $mask);
    }

    protected function setThousandsMasking(string $mask): string
    {
        if ($this->thousands === false) {
            $mask = str_replace(self::THOUSANDS_SEPARATOR, '', $mask);
            $mask = preg_replace('/#+0/', '0', $mask);
        }

        return $mask;
    }

    protected const SIGN_PATTERN_MASK = '/' .
        self::NON_BREAKING_SPACE . '?[-+]' . self::NON_BREAKING_SPACE . '?' .
        '/u';

    protected const SIGN_TRAILING_MASK = '/([0#])(?!.*[0#])/u';

    protected const SIGN_LEADING_MASK = '/([0#])/u';

    protected function setNegativeValueMask(string $positiveMask): string
    {
        if ($this->displayPositiveSign === true) {
            $stripPositiveSign = $this->trailingSign
                ? $this->signSeparator . '?' . self::SIGN_POSITIVE
                : self::SIGN_POSITIVE . $this->signSeparator . '?';
            $replaceNegatveSign = $this->trailingSign
                ? $this->signSeparator . self::SIGN_NEGATIVE
                : self::SIGN_NEGATIVE . $this->signSeparator;

            return preg_replace('/' . preg_quote($stripPositiveSign) . '/u', $replaceNegatveSign, $positiveMask);
        }

        $negativeMask = $this->trailingSign
            ? preg_replace(self::SIGN_TRAILING_MASK, '$1' . $this->signSeparator . self::SIGN_NEGATIVE, $positiveMask)
            : preg_replace(
                self::SIGN_LEADING_MASK,
                self::SIGN_NEGATIVE . $this->signSeparator . '$1',
                $positiveMask,
                1
            );

        return $negativeMask;
    }

    protected function setSignMasking(string $maskSet): string
    {
        $masks = explode(self::MASK_SEPARATOR, $maskSet);

        $mask = $masks[self::MASK_POSITIVE_VALUE];
        $mask = preg_replace(self::SIGN_PATTERN_MASK, '', $mask);
        $masks[self::MASK_POSITIVE_VALUE] = $mask;

        if ($this->displayPositiveSign === true) {
            $masks[self::MASK_ZERO_VALUE] = $this->setZeroValueMask($masks[self::MASK_POSITIVE_VALUE]);
            $masks[self::MASK_POSITIVE_VALUE] = $this->trailingSign
                ? preg_replace(self::SIGN_TRAILING_MASK, '$1' . $this->signSeparator . self::SIGN_POSITIVE, $mask)
                : preg_replace(self::SIGN_LEADING_MASK, self::SIGN_POSITIVE . $this->signSeparator . '$1', $mask, 1);
        }

        if ($this->trailingSign === true || $this->displayPositiveSign === true || $this->signSeparator !== '') {
            $masks[self::MASK_NEGATIVE_VALUE] = $this->trailingSign
                ? preg_replace(self::SIGN_TRAILING_MASK, '$1' . $this->signSeparator . self::SIGN_NEGATIVE, $mask)
                : preg_replace(self::SIGN_LEADING_MASK, self::SIGN_NEGATIVE . $this->signSeparator . '$1', $mask, 1);
        }

        ksort($masks);

        return implode(self::MASK_SEPARATOR, $masks);
    }

    protected function setColorMasking(string $maskSet): string
    {
        if (empty(array_filter($this->colors))) {
            return $maskSet;
        }
        $masks = preg_replace('/\[([a-z]*|Color[0-9]+)?\]/ui', '', $maskSet);
        $masks = explode(self::MASK_SEPARATOR, $masks);

        $masks[self::MASK_NEGATIVE_VALUE] = (array_key_exists(self::MASK_NEGATIVE_VALUE, $masks))
            ? $masks[self::MASK_NEGATIVE_VALUE]
            : $this->setNegativeValueMask($masks[self::MASK_POSITIVE_VALUE]);

        $masks[self::MASK_ZERO_VALUE] = (array_key_exists(self::MASK_ZERO_VALUE, $masks))
            ? $masks[self::MASK_ZERO_VALUE]
            : $this->setZeroValueMask($masks[self::MASK_POSITIVE_VALUE]);

        for ($colorMaskIndex = self::MASK_POSITIVE_VALUE; $colorMaskIndex <= self::MASK_ZERO_VALUE; ++$colorMaskIndex) {
            if (isset($this->colors[$colorMaskIndex])) {
                $masks[$colorMaskIndex] = "[{$this->colors[$colorMaskIndex]}]{$masks[$colorMaskIndex]}";
            }
        }

        if ($this->colors[self::MASK_ZERO_VALUE] === $this->colors[self::MASK_POSITIVE_VALUE]) {
            unset($masks[self::MASK_ZERO_VALUE]);
        }

        ksort($masks);

        return implode(self::MASK_SEPARATOR, $masks);
    }
}
