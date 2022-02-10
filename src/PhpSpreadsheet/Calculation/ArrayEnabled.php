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

    private static function initialiseHelper(array $arguments): void
    {
        if (self::$arrayArgumentHelper === null) {
            self::$arrayArgumentHelper = new ArrayArgumentHelper();
        }
        self::$arrayArgumentHelper->initialise($arguments);
    }

    protected static function evaluateSingleArgumentArray(callable $method, array $values): array
    {
        $result = [];
        foreach ($values as $value) {
            $result[] = $method($value);
        }

        return $result;
    }

    /**
     * @param mixed ...$arguments
     */
    protected static function evaluateArrayArguments(callable $method, ...$arguments): array
    {
        self::initialiseHelper($arguments);
        $arguments = self::$arrayArgumentHelper->arguments();

        return ArrayArgumentProcessor::processArguments(self::$arrayArgumentHelper, $method, ...$arguments);
    }

    /**
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
}
