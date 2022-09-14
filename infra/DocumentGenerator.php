<?php

namespace PhpOffice\PhpSpreadsheetInfra;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use ReflectionClass;
use UnexpectedValueException;

class DocumentGenerator
{
    /**
     * @param array[] $phpSpreadsheetFunctions
     */
    public static function generateFunctionListByCategory(array $phpSpreadsheetFunctions): string
    {
        $result = "# Function list by category\n";
        foreach (self::getCategories() as $categoryConstant => $category) {
            $result .= "\n";
            $result .= "## {$categoryConstant}\n";
            $result .= "\n";
            $lengths = [25, 37];
            $result .= self::tableRow($lengths, ['Excel Function', 'PhpSpreadsheet Function']) . "\n";
            $result .= self::tableRow($lengths, null) . "\n";
            foreach ($phpSpreadsheetFunctions as $excelFunction => $functionInfo) {
                if ($category === $functionInfo['category']) {
                    $phpFunction = self::getPhpSpreadsheetFunctionText($functionInfo['functionCall']);
                    $result .= self::tableRow($lengths, [$excelFunction, $phpFunction]) . "\n";
                }
            }
        }

        return $result;
    }

    private static function getCategories(): array
    {
        return (new ReflectionClass(Category::class))->getConstants();
    }

    private static function tableRow(array $lengths, ?array $values = null): string
    {
        $result = '';
        foreach (array_map(/** @scrutinizer ignore-type */ null, $lengths, $values ?? []) as $i => [$length, $value]) {
            $pad = $value === null ? '-' : ' ';
            if ($i > 0) {
                $result .= '|' . $pad;
            }
            $result .= str_pad($value ?? '', $length, $pad);
        }

        return rtrim($result, ' ');
    }

    private static function getPhpSpreadsheetFunctionText($functionCall): string
    {
        if (is_string($functionCall)) {
            return $functionCall;
        }
        if ($functionCall === [Functions::class, 'DUMMY']) {
            return '**Not yet Implemented**';
        }
        if (is_array($functionCall)) {
            return "\\{$functionCall[0]}::{$functionCall[1]}";
        }

        throw new UnexpectedValueException(
            '$functionCall is of type ' . gettype($functionCall) . '. string or array expected'
        );
    }

    /**
     * @param array[] $phpSpreadsheetFunctions
     */
    public static function generateFunctionListByName(array $phpSpreadsheetFunctions): string
    {
        $categoryConstants = array_flip(self::getCategories());
        $result = "# Function list by name\n";
        $lastAlphabet = null;
        foreach ($phpSpreadsheetFunctions as $excelFunction => $functionInfo) {
            /** @var string $excelFunction */
            $lengths = [25, 31, 37];
            if ($lastAlphabet !== $excelFunction[0]) {
                $lastAlphabet = $excelFunction[0];
                $result .= "\n";
                $result .= "## {$lastAlphabet}\n";
                $result .= "\n";
                $result .= self::tableRow($lengths, ['Excel Function', 'Category', 'PhpSpreadsheet Function']) . "\n";
                $result .= self::tableRow($lengths, null) . "\n";
            }
            $category = $categoryConstants[$functionInfo['category']];
            $phpFunction = self::getPhpSpreadsheetFunctionText($functionInfo['functionCall']);
            $result .= self::tableRow($lengths, [$excelFunction, $category, $phpFunction]) . "\n";
        }

        return $result;
    }
}
