<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Database\DAverage;
use PhpOffice\PhpSpreadsheet\Calculation\Database\DCount;
use PhpOffice\PhpSpreadsheet\Calculation\Database\DMax;
use PhpOffice\PhpSpreadsheet\Calculation\Database\DMin;
use PhpOffice\PhpSpreadsheet\Calculation\Database\DSum;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcException;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Conditional
{
    private const CONDITION_COLUMN_NAME = 'CONDITION';
    private const VALUE_COLUMN_NAME = 'VALUE';
    private const CONDITIONAL_COLUMN_NAME = 'CONDITIONAL %d';

    /**
     * AVERAGEIF.
     *
     * Returns the average value from a range of cells that contain numbers within the list of arguments
     *
     * Excel Function:
     *        AVERAGEIF(range,condition[, average_range])
     *
     * @param mixed $range Data values
     * @param string $condition the criteria that defines which cells will be checked
     * @param mixed $averageRange Data values
     *
     * @return null|float|string
     */
    public static function AVERAGEIF(mixed $range, $condition, mixed $averageRange = [])
    {
        if (!is_array($range) || !is_array($averageRange) || array_key_exists(0, $range) || array_key_exists(0, $averageRange)) {
            throw new CalcException('Must specify range of cells, not any kind of literal');
        }
        $database = self::databaseFromRangeAndValue($range, $averageRange);
        $condition = [[self::CONDITION_COLUMN_NAME, self::VALUE_COLUMN_NAME], [$condition, null]];

        return DAverage::evaluate($database, self::VALUE_COLUMN_NAME, $condition);
    }

    /**
     * AVERAGEIFS.
     *
     * Counts the number of cells that contain numbers within the list of arguments
     *
     * Excel Function:
     *        AVERAGEIFS(average_range, criteria_range1, criteria1, [criteria_range2, criteria2]…)
     *
     * @param mixed $args Pairs of Ranges and Criteria
     *
     * @return null|float|string
     */
    public static function AVERAGEIFS(mixed ...$args)
    {
        if (empty($args)) {
            return 0.0;
        } elseif (count($args) === 3) {
            return self::AVERAGEIF($args[1], $args[2], $args[0]);
        }
        foreach ($args as $arg) {
            if (is_array($arg) && array_key_exists(0, $arg)) {
                throw new CalcException('Must specify range of cells, not any kind of literal');
            }
        }

        $conditions = self::buildConditionSetForValueRange(...$args);
        $database = self::buildDatabaseWithValueRange(...$args);

        return DAverage::evaluate($database, self::VALUE_COLUMN_NAME, $conditions);
    }

    /**
     * COUNTIF.
     *
     * Counts the number of cells that contain numbers within the list of arguments
     *
     * Excel Function:
     *        COUNTIF(range,condition)
     *
     * @param mixed[] $range Data values
     * @param string $condition the criteria that defines which cells will be counted
     */
    public static function COUNTIF($range, $condition): string|int
    {
        // Filter out any empty values that shouldn't be included in a COUNT
        $range = array_filter(
            Functions::flattenArray($range),
            fn ($value): bool => $value !== null && $value !== ''
        );

        $range = array_merge([[self::CONDITION_COLUMN_NAME]], array_chunk($range, 1));
        $condition = array_merge([[self::CONDITION_COLUMN_NAME]], [[$condition]]);

        return DCount::evaluate($range, null, $condition, false);
    }

    /**
     * COUNTIFS.
     *
     * Counts the number of cells that contain numbers within the list of arguments
     *
     * Excel Function:
     *        COUNTIFS(criteria_range1, criteria1, [criteria_range2, criteria2]…)
     *
     * @param mixed $args Pairs of Ranges and Criteria
     *
     * @return int|string
     */
    public static function COUNTIFS(mixed ...$args)
    {
        if (empty($args)) {
            return 0;
        } elseif (count($args) === 2) {
            return self::COUNTIF(...$args);
        }

        $database = self::buildDatabase(...$args);
        $conditions = self::buildConditionSet(...$args);

        return DCount::evaluate($database, null, $conditions, false);
    }

    /**
     * MAXIFS.
     *
     * Returns the maximum value within a range of cells that contain numbers within the list of arguments
     *
     * Excel Function:
     *        MAXIFS(max_range, criteria_range1, criteria1, [criteria_range2, criteria2]…)
     *
     * @param mixed $args Pairs of Ranges and Criteria
     *
     * @return null|float|string
     */
    public static function MAXIFS(mixed ...$args)
    {
        if (empty($args)) {
            return 0.0;
        }

        $conditions = self::buildConditionSetForValueRange(...$args);
        $database = self::buildDatabaseWithValueRange(...$args);

        return DMax::evaluate($database, self::VALUE_COLUMN_NAME, $conditions, false);
    }

    /**
     * MINIFS.
     *
     * Returns the minimum value within a range of cells that contain numbers within the list of arguments
     *
     * Excel Function:
     *        MINIFS(min_range, criteria_range1, criteria1, [criteria_range2, criteria2]…)
     *
     * @param mixed $args Pairs of Ranges and Criteria
     *
     * @return null|float|string
     */
    public static function MINIFS(mixed ...$args)
    {
        if (empty($args)) {
            return 0.0;
        }

        $conditions = self::buildConditionSetForValueRange(...$args);
        $database = self::buildDatabaseWithValueRange(...$args);

        return DMin::evaluate($database, self::VALUE_COLUMN_NAME, $conditions, false);
    }

    /**
     * SUMIF.
     *
     * Totals the values of cells that contain numbers within the list of arguments
     *
     * Excel Function:
     *        SUMIF(range, criteria, [sum_range])
     *
     * @param array $range Data values
     *
     * @return null|float|string
     */
    public static function SUMIF(array $range, mixed $condition, array $sumRange = [])
    {
        $database = self::databaseFromRangeAndValue($range, $sumRange);
        $condition = [[self::CONDITION_COLUMN_NAME, self::VALUE_COLUMN_NAME], [$condition, null]];

        return DSum::evaluate($database, self::VALUE_COLUMN_NAME, $condition);
    }

    /**
     * SUMIFS.
     *
     * Counts the number of cells that contain numbers within the list of arguments
     *
     * Excel Function:
     *        SUMIFS(average_range, criteria_range1, criteria1, [criteria_range2, criteria2]…)
     *
     * @param mixed $args Pairs of Ranges and Criteria
     *
     * @return null|float|string
     */
    public static function SUMIFS(mixed ...$args)
    {
        if (empty($args)) {
            return 0.0;
        } elseif (count($args) === 3) {
            return self::SUMIF($args[1], $args[2], $args[0]);
        }

        $conditions = self::buildConditionSetForValueRange(...$args);
        $database = self::buildDatabaseWithValueRange(...$args);

        return DSum::evaluate($database, self::VALUE_COLUMN_NAME, $conditions);
    }

    /** @param array $args */
    private static function buildConditionSet(...$args): array
    {
        $conditions = self::buildConditions(1, ...$args);

        return array_map(null, ...$conditions);
    }

    /** @param array $args */
    private static function buildConditionSetForValueRange(...$args): array
    {
        $conditions = self::buildConditions(2, ...$args);

        if (count($conditions) === 1) {
            return array_map(
                fn ($value): array => [$value],
                $conditions[0]
            );
        }

        return array_map(null, ...$conditions);
    }

    /** @param array $args */
    private static function buildConditions(int $startOffset, ...$args): array
    {
        $conditions = [];

        $pairCount = 1;
        $argumentCount = count($args);
        for ($argument = $startOffset; $argument < $argumentCount; $argument += 2) {
            $conditions[] = array_merge([sprintf(self::CONDITIONAL_COLUMN_NAME, $pairCount)], [$args[$argument]]);
            ++$pairCount;
        }

        return $conditions;
    }

    /** @param array $args */
    private static function buildDatabase(...$args): array
    {
        $database = [];

        return self::buildDataSet(0, $database, ...$args);
    }

    /** @param array $args */
    private static function buildDatabaseWithValueRange(...$args): array
    {
        $database = [];
        $database[] = array_merge(
            [self::VALUE_COLUMN_NAME],
            Functions::flattenArray($args[0])
        );

        return self::buildDataSet(1, $database, ...$args);
    }

    /** @param array $args */
    private static function buildDataSet(int $startOffset, array $database, ...$args): array
    {
        $pairCount = 1;
        $argumentCount = count($args);
        for ($argument = $startOffset; $argument < $argumentCount; $argument += 2) {
            $database[] = array_merge(
                [sprintf(self::CONDITIONAL_COLUMN_NAME, $pairCount)],
                Functions::flattenArray($args[$argument])
            );
            ++$pairCount;
        }

        return array_map(null, ...$database);
    }

    private static function databaseFromRangeAndValue(array $range, array $valueRange = []): array
    {
        $range = Functions::flattenArray($range);

        $valueRange = Functions::flattenArray($valueRange);
        if (empty($valueRange)) {
            $valueRange = $range;
        }

        $database = array_map(null, array_merge([self::CONDITION_COLUMN_NAME], $range), array_merge([self::VALUE_COLUMN_NAME], $valueRange));

        return $database;
    }
}
