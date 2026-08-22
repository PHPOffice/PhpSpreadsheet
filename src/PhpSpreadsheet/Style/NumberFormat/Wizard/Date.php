<?php

namespace PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard;

class Date extends DateTimeWizard
{
    /**
     * Year (4 digits), e.g. 2023.
     */
    public const YEAR_FULL = 'yyyy';

    /**
     * Year (last 2 digits), e.g. 23.
     */
    public const YEAR_SHORT = 'yy';

    public const MONTH_FIRST_LETTER = 'mmmmm';
    /**
     * Month name, long form, e.g. January.
     */
    public const MONTH_NAME_FULL = 'mmmm';
    /**
     * Month name, short form, e.g. Jan.
     */
    public const MONTH_NAME_SHORT = 'mmm';
    /**
     * Month number with a leading zero if required, e.g. 01.
     */
    public const MONTH_NUMBER_LONG = 'mm';

    /**
     * Month number without a leading zero, e.g. 1.
     */
    public const MONTH_NUMBER_SHORT = 'm';

    /**
     * Day of the week, full form, e.g. Tuesday.
     */
    public const WEEKDAY_NAME_LONG = 'dddd';

    /**
     * Day of the week, short form, e.g. Tue.
     */
    public const WEEKDAY_NAME_SHORT = 'ddd';

    /**
     * Day number with a leading zero, e.g. 03.
     */
    public const DAY_NUMBER_LONG = 'dd';

    /**
     * Day number without a leading zero, e.g. 3.
     */
    public const DAY_NUMBER_SHORT = 'd';

    protected const DATE_BLOCKS = [
        self::YEAR_FULL,
        self::YEAR_SHORT,
        self::MONTH_FIRST_LETTER,
        self::MONTH_NAME_FULL,
        self::MONTH_NAME_SHORT,
        self::MONTH_NUMBER_LONG,
        self::MONTH_NUMBER_SHORT,
        self::WEEKDAY_NAME_LONG,
        self::WEEKDAY_NAME_SHORT,
        self::DAY_NUMBER_LONG,
        self::DAY_NUMBER_SHORT,
    ];

    public const SEPARATOR_DASH = '-';
    public const SEPARATOR_DOT = '.';
    public const SEPARATOR_SLASH = '/';
    public const SEPARATOR_SPACE_NONBREAKING = "\u{a0}";
    public const SEPARATOR_SPACE = ' ';

    protected const DATE_DEFAULT = [
        self::YEAR_FULL,
        self::MONTH_NUMBER_LONG,
        self::DAY_NUMBER_LONG,
    ];

    /**
     * @var array<?string>
     */
    protected array $separators;

    /**
     * @var string[]
     */
    protected array $formatBlocks;

    /**
     * @param null|array<?string>|string $separators
     *        If you want to use the same separator for all format blocks, then it can be passed as a string literal;
     *           if you wish to use different separators, then they should be passed as an array.
     *        If you want to use only a single format block, then pass a null as the separator argument
     */
    public function __construct($separators = self::SEPARATOR_DASH, string|null ...$formatBlocks)
    {
        $separators ??= self::SEPARATOR_DASH;
        $formatBlocks = (count($formatBlocks) === 0) ? self::DATE_DEFAULT : $formatBlocks;

        $this->separators = $this->padSeparatorArray(
            is_array($separators) ? $separators : [$separators],
            count($formatBlocks) - 1
        );
        $this->formatBlocks = array_map([$this, 'mapFormatBlocks'], $formatBlocks);
    }

    private function mapFormatBlocks(string $value): string
    {
        // Any date masking codes are returned as lower case values
        if (in_array(mb_strtolower($value), self::DATE_BLOCKS, true)) {
            return mb_strtolower($value);
        }

        // Wrap any string literals in quotes, so that they're clearly defined as string literals
        return $this->wrapLiteral($value);
    }

    public function format(): string
    {
        return implode('', array_map([$this, 'intersperse'], $this->formatBlocks, $this->separators));
    }
}
