<?php

namespace PhpOffice\PhpSpreadsheetInfra;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use ReflectionClass;
use UnexpectedValueException;

class DocumentGenerator
{
    private const EXCLUDED_FUNCTIONS = [
        'CEILING.ODS',
        'CEILING.XCL',
        'FLOOR.ODS',
        'FLOOR.XCL',
    ];

    /**
     * @param array<string, array{category: string, functionCall: string|string[], argumentCount: string, passCellReference?: bool, passByReference?: bool[], custom?: bool}> $phpSpreadsheetFunctions
     */
    public static function generateFunctionListByCategory($phpSpreadsheetFunctions): string
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
                if (in_array($excelFunction, self::EXCLUDED_FUNCTIONS, true)) {
                    continue;
                }
                if ($category === $functionInfo['category']) {
                    $phpFunction = self::getPhpSpreadsheetFunctionText($functionInfo['functionCall']);
                    $result .= self::tableRow($lengths, [$excelFunction, $phpFunction]) . "\n";
                }
            }
        }

        return $result;
    }

    /** @return array<string, string> */
    private static function getCategories(): array
    {
        /** @var array<string, string> */
        $x = (new ReflectionClass(Category::class))->getConstants();

        return $x;
    }

    /**
     * @param int[] $lengths
     * @param null|array<int, int|string> $values
     */
    private static function tableRow(array $lengths, ?array $values = null): string
    {
        $result = '';
        foreach (array_map(null, $lengths, $values ?? []) as $i => [$length, $value]) {
            $pad = $value === null ? '-' : ' ';
            if ($i > 0) {
                $result .= '|' . $pad;
            }
            $result .= str_pad("$value", $length ?? 0, $pad);
        }

        return rtrim($result, ' ');
    }

    /** @param scalar|string|string[] $functionCall */
    private static function getPhpSpreadsheetFunctionText(mixed $functionCall): string
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
     * @param array<string, array{category: string, functionCall: string|string[], argumentCount: string, passCellReference?: bool, passByReference?: bool[], custom?: bool}> $phpSpreadsheetFunctions
     */
    public static function generateFunctionListByName(array $phpSpreadsheetFunctions, bool $compact = false): string
    {
        $categoryConstants = array_flip(self::getCategories());
        if ($compact) {
            $result = "# Function list by name compact\n";
            $result .= "\n";
            $result .= 'Category should be prefixed by `CATEGORY_` to match the values in \PhpOffice\PhpSpreadsheet\Calculation\Category';
            $result .= "\n\n";
            $result .= 'Function should be prefixed by `PhpOffice\PhpSpreadsheet\Calculation\`';
            $result .= "\n\n";
            $result .= 'A less compact list can be found [here](./function-list-by-name.md)';
            $result .= "\n\n";
        } else {
            $result = "# Function list by name\n";
            $result .= "\n";
            $result .= 'A more compact list can be found [here](./function-list-by-name-compact.md)';
            $result .= "\n\n";
        }
        $lastAlphabet = null;
        $lengths = $compact ? [25, 22, 37] : [25, 31, 37];
        foreach ($phpSpreadsheetFunctions as $excelFunction => $functionInfo) {
            if (in_array($excelFunction, self::EXCLUDED_FUNCTIONS, true)) {
                continue;
            }
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
            if ($compact) {
                $category = str_replace('CATEGORY_', '', $category);
                $phpFunction = str_replace(
                    '\PhpOffice\PhpSpreadsheet\Calculation\\',
                    '',
                    $phpFunction
                );
            }
            $result .= self::tableRow($lengths, [$excelFunction, $category, $phpFunction]) . "\n";
        }

        return $result;
    }
}
