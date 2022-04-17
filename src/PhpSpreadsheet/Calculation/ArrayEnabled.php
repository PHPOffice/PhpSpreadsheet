<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Engine\ArrayArgumentHelper;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\ArrayArgumentProcessor;

trait ArrayEnabled
{
    /**
     * @var ArrayArgumentHelper
     */
    private static $arrayArgumentHelper;

    /**
     * @param array|false $arguments Can be changed to array for Php8.1+
     */
    private static function initialiseHelper($arguments): void
    {
        if (self::$arrayArgumentHelper === null) {
            self::$arrayArgumentHelper = new ArrayArgumentHelper();
        }
        self::$arrayArgumentHelper->initialise($arguments ?: []);
    }

    /**
     * Handles array argument processing when the function accepts a single argument that can be an array argument.
     * Example use for:
     *         DAYOFMONTH() or FACT().
     */
    protected static function evaluateSingleArgumentArray(callable $method, array $values): array
    {
        $result = [];
        foreach ($values as $value) {
            $result[] = $method($value);
        }

        return $result;
    }

    /**
     * Handles array argument processing when the function accepts multiple arguments,
     *     and any of them can be an array argument.
     * Example use for:
     *         ROUND() or DATE().
     *
     * @param mixed ...$arguments
     */
    protected static function evaluateArrayArguments(callable $method, ...$arguments): array
    {
        self::initialiseHelper($arguments);
        $arguments = self::$arrayArgumentHelper->arguments();

        return ArrayArgumentProcessor::processArguments(self::$arrayArgumentHelper, $method, ...$arguments);
    }

    /**
     * Handles array argument processing when the function accepts multiple arguments,
     *     but only the first few (up to limit) can be an array arguments.
     * Example use for:
     *         NETWORKDAYS() or CONCATENATE(), where the last argument is a matrix (or a series of values) that need
     *                                         to be treated as a such rather than as an array arguments.
     *
     * @param mixed ...$arguments
     */
    protected static function evaluateArrayArgumentsSubset(callable $method, int $limit, ...$arguments): array
    {
        self::initialiseHelper(array_slice($arguments, 0, $limit));
        $trailingArguments = array_slice($arguments, $limit);
        $arguments = self::$arrayArgumentHelper->arguments();
        $arguments = array_merge($arguments, $trailingArguments);

        return ArrayArgumentProcessor::processArguments(self::$arrayArgumentHelper, $method, ...$arguments);
    }

    /**
     * @param mixed $value
     */
    private static function testFalse($value): bool
    {
        return $value === false;
    }

    /**
     * Handles array argument processing when the function accepts multiple arguments,
     *     but only the last few (from start) can be an array arguments.
     * Example use for:
     *         Z.TEST() or INDEX(), where the first argument 1 is a matrix that needs to be treated as a dataset
     *                   rather than as an array argument.
     *
     * @param mixed ...$arguments
     */
    protected static function evaluateArrayArgumentsSubsetFrom(callable $method, int $start, ...$arguments): array
    {
        $arrayArgumentsSubset = array_combine(
            range($start, count($arguments) - $start),
            array_slice($arguments, $start)
        );
        if (self::testFalse($arrayArgumentsSubset)) {
            return ['#VALUE!'];
        }

        self::initialiseHelper($arrayArgumentsSubset);
        $leadingArguments = array_slice($arguments, 0, $start);
        $arguments = self::$arrayArgumentHelper->arguments();
        $arguments = array_merge($leadingArguments, $arguments);

        return ArrayArgumentProcessor::processArguments(self::$arrayArgumentHelper, $method, ...$arguments);
    }

    /**
     * Handles array argument processing when the function accepts multiple arguments,
     *     and any of them can be an array argument except for the one specified by ignore.
     * Example use for:
     *         HLOOKUP() and VLOOKUP(), where argument 1 is a matrix that needs to be treated as a database
     *                                  rather than as an array argument.
     *
     * @param mixed ...$arguments
     */
    protected static function evaluateArrayArgumentsIgnore(callable $method, int $ignore, ...$arguments): array
    {
        $leadingArguments = array_slice($arguments, 0, $ignore);
        $ignoreArgument = array_slice($arguments, $ignore, 1);
        $trailingArguments = array_slice($arguments, $ignore + 1);

        self::initialiseHelper(array_merge($leadingArguments, [[null]], $trailingArguments));
        $arguments = self::$arrayArgumentHelper->arguments();

        array_splice($arguments, $ignore, 1, $ignoreArgument);

        return ArrayArgumentProcessor::processArguments(self::$arrayArgumentHelper, $method, ...$arguments);
    }
}
