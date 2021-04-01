<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class FuncDocs extends Calculation
{
    private static function hyphenLine(string $headerLine): string
    {
        $numchars = strlen($headerLine);
        $outString = '';
        for ($i = 0; $i < $numchars; ++$i) {
            $thisChar = $headerLine[$i];
            $outString .= ($thisChar === '|') ? '|' : '-';
        }

        return $outString;
    }

    private static function notImplemented(string $className, string $functionName): string
    {
        return ($functionName === 'DUMMY') ? '**Not yet implemented**' : "$className::$functionName";
    }

    public static function makeDocByName(): string
    {
        $outString = "# Function list by name\n";
        $heading1 = 'Excel Function';
        $heading2 = 'Category';
        $heading3 = 'PhpSpreadsheet Function';
        $maxExcel = strlen($heading1);
        $maxCategory = strlen($heading2);
        foreach (self::$phpSpreadsheetFunctions as $excelFunc => $value) {
            $category = $value['category'];
            $maxExcel = max($maxExcel, strlen($excelFunc));
            $maxCategory = max($maxCategory, strlen($category));
        }
        $excelFormat = "%-$maxExcel" . 's | ';
        $categoryFormat = "%-$maxCategory" . 's | ';
        $headerLine = sprintf($excelFormat, $heading1);
        $headerLine .= sprintf($categoryFormat, $heading2);
        $headerLine .= "$heading3";
        $hyphenLine = self::hyphenLine($headerLine);
        $headerLine .= "\n$hyphenLine\n";
        $firstChar = '';
        foreach (self::$phpSpreadsheetFunctions as $excelFunc => $value) {
            $category = $value['category'];
            $className = $value['functionCall'][0];
            $functionName = $value['functionCall'][1];
            if ($firstChar !== $excelFunc[0]) {
                $firstChar = $excelFunc[0];
                $outString .= "\n## $firstChar\n\n";
                $outString .= $headerLine;
            }
            $outString .= sprintf($excelFormat, $excelFunc);
            $outString .= sprintf($categoryFormat, $category);
            $outString .= self::notImplemented($className, $functionName);
            $outString .= "\n";
        }

        return $outString;
    }

    private static function categoryCompare(array $a, array $b): int
    {
        // compare categories
        if ($a[0] < $b[0]) {
            return -1;
        }
        if ($a[0] > $b[0]) {
            return 1;
        }

        // compare excel function name, which should be unique in array
        return ($a[1] < $b[1]) ? -1 : 1;
    }

    public static function makeDocByCategory(): string
    {
        $outString = "# Function list by category\n";
        $heading1 = 'Excel Function';
        $heading3 = 'PhpSpreadsheet Function';
        $maxExcel = strlen($heading1);
        $arrayCopy = [];
        foreach (self::$phpSpreadsheetFunctions as $excelFunc => $value) {
            $arrayCopy[] = [$value['category'], $excelFunc, $value['functionCall'][0], $value['functionCall'][1]];
            $maxExcel = max($maxExcel, strlen($excelFunc));
        }
        usort($arrayCopy, [self::class, 'categoryCompare']);

        $excelFormat = "%-$maxExcel" . 's | ';
        $headerLine = sprintf($excelFormat, $heading1);
        $headerLine .= "$heading3";
        $hyphenLine = self::hyphenLine($headerLine);
        $headerLine .= "\n$hyphenLine\n";
        $firstCategory = '';
        foreach ($arrayCopy as $value) {
            [$category, $excelFunc, $className, $functionName] = $value;
            if ($firstCategory !== $category) {
                $firstCategory = $category;
                $outString .= "\n## $firstCategory\n\n";
                $outString .= $headerLine;
            }
            $outString .= sprintf($excelFormat, $excelFunc);
            $outString .= self::notImplemented($className, $functionName);
            $outString .= "\n";
        }

        return $outString;
    }
}
