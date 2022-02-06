<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Engine\ArrayArgumentHelper;

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

        if (self::$arrayArgumentHelper->hasArrayArgument() === false) {
            return [$method(...$arguments)];
        }

        if (self::$arrayArgumentHelper->arrayArgumentCount() === 1) {
            $nthArgument = self::$arrayArgumentHelper->getFirstArrayArgumentNumber();

            return self::evaluateNthArgumentAsArray($method, $nthArgument, ...$arguments);
        }

        $singleRowVectorIndex = self::$arrayArgumentHelper->getSingleRowVector();
        $singleColumnVectorIndex = self::$arrayArgumentHelper->getSingleColumnVector();
        if ($singleRowVectorIndex !== null && $singleColumnVectorIndex !== null) {
            // Basic logic for a single row vector and a single column vector
            return self::evaluateVectorPair($method, $singleRowVectorIndex, $singleColumnVectorIndex, ...$arguments);
        }

        $rowVectorIndexes = self::$arrayArgumentHelper->getRowVectors();
        $columnVectorIndexes = self::$arrayArgumentHelper->getColumnVectors();

        // Logic for a two row vectors or two column vectors
        if (count($rowVectorIndexes) === 2 && count($columnVectorIndexes) === 0) {
            return self::evaluateRowVectorPair($method, $rowVectorIndexes, ...$arguments);
        } elseif (count($rowVectorIndexes) === 0 && count($columnVectorIndexes) === 2) {
            return self::evaluateColumnVectorPair($method, $columnVectorIndexes, ...$arguments);
        }

        // If we have multiple arrays, and they don't match a row vector/column vector pattern,
        //    or two row vectors and two column vectors,
        //    then we drop through to an error return for the moment
        // Still need to work out the logic for multiple matrices as array arguments,
        //       or when we have more than two arrays
        return ['#VALUE!'];
    }

    /**
     * @param mixed ...$arguments
     */
    private static function evaluateRowVectorPair(callable $method, array $vectorIndexes, ...$arguments): array
    {
        $vector2 = array_pop($vectorIndexes);
        $vectorValues2 = Functions::flattenArray($arguments[$vector2]);
        $vector1 = array_pop($vectorIndexes);
        $vectorValues1 = Functions::flattenArray($arguments[$vector1]);

        $result = [];
        foreach ($vectorValues1 as $index => $value1) {
            $value2 = $vectorValues2[$index];
            $arguments[$vector1] = $value1;
            $arguments[$vector2] = $value2;

            $result[] = $method(...$arguments);
        }

        return [$result];
    }

    /**
     * @param mixed ...$arguments
     */
    private static function evaluateColumnVectorPair(callable $method, array $vectorIndexes, ...$arguments): array
    {
        $vector2 = array_pop($vectorIndexes);
        $vectorValues2 = Functions::flattenArray($arguments[$vector2]);
        $vector1 = array_pop($vectorIndexes);
        $vectorValues1 = Functions::flattenArray($arguments[$vector1]);

        $result = [];
        foreach ($vectorValues1 as $index => $value1) {
            $value2 = $vectorValues2[$index];
            $arguments[$vector1] = $value1;
            $arguments[$vector2] = $value2;

            $result[] = [$method(...$arguments)];
        }

        return $result;
    }

    /**
     * @param mixed ...$arguments
     */
    private static function evaluateVectorPair(callable $method, int $rowIndex, int $columnIndex, ...$arguments): array
    {
        $rowVector = Functions::flattenArray($arguments[$rowIndex]);
        $columnVector = Functions::flattenArray($arguments[$columnIndex]);

        $result = [];
        foreach ($columnVector as $column) {
            $rowResults = [];
            foreach ($rowVector as $row) {
                $arguments[$rowIndex] = $row;
                $arguments[$columnIndex] = $column;

                $rowResults[] = $method(...$arguments);
            }
            $result[] = $rowResults;
        }

        return $result;
    }

    /**
     * Note, offset is from 1 (for the first argument) rather than from 0.
     *
     * @param mixed ...$arguments
     */
    private static function evaluateNthArgumentAsArray(callable $method, int $nthArgument, ...$arguments): array
    {
        $values = array_slice($arguments, $nthArgument - 1, 1);
        /** @var array $values */
        $values = array_pop($values);

        $result = [];
        foreach ($values as $value) {
            $arguments[$nthArgument - 1] = $value;
            $result[] = $method(...$arguments);
        }

        return $result;
    }
}
