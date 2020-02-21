<?php


namespace PhpOffice\PhpSpreadsheet;


use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use ReflectionClass;
use ReflectionException;
use UnexpectedValueException;

class DocumentGenerator {
    /**
     * @throws ReflectionException
     */
    public static function generateFunctionListByCategory(): void {
        ob_start();
        try {
            echo "# Function list by category\n";
            $phpSpreadsheetFunctions = self::getPhpSpreadsheetFunctions();
            foreach (self::getCategories() as $categoryConstant => $category) {
                echo "\n";
                echo "## {$categoryConstant}\n";
                echo "\n";
                echo "Excel Function      | PhpSpreadsheet Function\n";
                echo "--------------------|-------------------------------------------\n";
                foreach ($phpSpreadsheetFunctions as $function => $functionInfo) {
                    if ($category === $functionInfo['category']) {
                        echo str_pad($function, 20)
                            . '| ' . self::getPhpSpreadsheetFunctionText($functionInfo['functionCall']) . "\n";
                    }
                }
            }
        } finally {
            file_put_contents(__DIR__ . '/../../docs/references/function-list-by-category.md', ob_get_clean());
        }
    }

    /**
     * @return mixed
     * @throws ReflectionException
     */
    private static function getPhpSpreadsheetFunctions() {
        $phpSpreadsheetFunctionsProperty = (new ReflectionClass(Calculation::class))->getProperty('phpSpreadsheetFunctions');
        $phpSpreadsheetFunctionsProperty->setAccessible(true);
        $phpSpreadsheetFunctions = $phpSpreadsheetFunctionsProperty->getValue();
        ksort($phpSpreadsheetFunctions);
        return $phpSpreadsheetFunctions;
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    private static function getCategories(): array {
        return (new ReflectionClass(Category::class))->getConstants();
    }

    private static function getPhpSpreadsheetFunctionText($functionCall): string {
        if (is_string($functionCall)) {
            return $functionCall;
        }
        if ($functionCall === [Functions::class, 'DUMMY']) {
            return '**Not yet Implemented**';
        }
        if (is_array($functionCall)) {
            return "\\{$functionCall[0]}::{$functionCall[1]}";
        }
        throw new UnexpectedValueException('$functionCall is of type ' . gettype($functionCall) . '. string or array expected');
    }

    /**
     * @throws ReflectionException
     */
    public static function generateFunctionListByName(): void {
        $categoryConstants = array_flip(self::getCategories());
        ob_start();
        try {
            echo "# Function list by name\n";
            $phpSpreadsheetFunctions = self::getPhpSpreadsheetFunctions();
            $lastAlphabet = null;
            foreach ($phpSpreadsheetFunctions as $function => $functionInfo) {
                if ($lastAlphabet !== $function[0]) {
                    $lastAlphabet = $function[0];
                    echo "\n";
                    echo "## {$lastAlphabet}\n";
                    echo "\n";
                    echo "Excel Function      | Category                       | PhpSpreadsheet Function\n";
                    echo "--------------------|--------------------------------|-------------------------------------------\n";
                }
                echo str_pad($function, 20)
                    . '| ' . str_pad($categoryConstants[$functionInfo['category']], 31)
                    . '| ' . self::getPhpSpreadsheetFunctionText($functionInfo['functionCall'])
                    . "\n";
            }
        } finally {
            file_put_contents(__DIR__ . '/../../docs/references/function-list-by-name.md', ob_get_clean());
        }
    }
}