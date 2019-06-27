<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Helper class to manipulate cell coordinates.
 *
 * Columns indexes and rows are always based on 1, **not** on 0. This match the behavior
 * that Excel users are used to, and also match the Excel functions `COLUMN()` and `ROW()`.
 */
abstract class Coordinate
{
    /**
     * Default range variable constant.
     *
     * @var string
     */
    const DEFAULT_RANGE = 'A1:A1';

    /**
     * Coordinate from string.
     *
     * @param string $pCoordinateString eg: 'A1'
     *
     * @throws Exception
     *
     * @return string[] Array containing column and row (indexes 0 and 1)
     */
    public static function coordinateFromString($pCoordinateString)
    {
        if (preg_match('/^([$]?[A-Z]{1,3})([$]?\\d{1,7})$/', $pCoordinateString, $matches)) {
            return [$matches[1], $matches[2]];
        } elseif (self::coordinateIsRange($pCoordinateString)) {
            throw new Exception('Cell coordinate string can not be a range of cells');
        } elseif ($pCoordinateString == '') {
            throw new Exception('Cell coordinate can not be zero-length string');
        }

        throw new Exception('Invalid cell coordinate ' . $pCoordinateString);
    }

    /**
     * Checks if a coordinate represents a range of cells.
     *
     * @param string $coord eg: 'A1' or 'A1:A2' or 'A1:A2,C1:C2'
     *
     * @return bool Whether the coordinate represents a range of cells
     */
    public static function coordinateIsRange($coord)
    {
        return (strpos($coord, ':') !== false) || (strpos($coord, ',') !== false);
    }

    /**
     * Make string row, column or cell coordinate absolute.
     *
     * @param string $pCoordinateString e.g. 'A' or '1' or 'A1'
     *                    Note that this value can be a row or column reference as well as a cell reference
     *
     * @throws Exception
     *
     * @return string Absolute coordinate        e.g. '$A' or '$1' or '$A$1'
     */
    public static function absoluteReference($pCoordinateString)
    {
        if (self::coordinateIsRange($pCoordinateString)) {
            throw new Exception('Cell coordinate string can not be a range of cells');
        }

        // Split out any worksheet name from the reference
        list($worksheet, $pCoordinateString) = Worksheet::extractSheetTitle($pCoordinateString, true);
        if ($worksheet > '') {
            $worksheet .= '!';
        }

        // Create absolute coordinate
        if (ctype_digit($pCoordinateString)) {
            return $worksheet . '$' . $pCoordinateString;
        } elseif (ctype_alpha($pCoordinateString)) {
            return $worksheet . '$' . strtoupper($pCoordinateString);
        }

        return $worksheet . self::absoluteCoordinate($pCoordinateString);
    }

    /**
     * Make string coordinate absolute.
     *
     * @param string $pCoordinateString e.g. 'A1'
     *
     * @throws Exception
     *
     * @return string Absolute coordinate        e.g. '$A$1'
     */
    public static function absoluteCoordinate($pCoordinateString)
    {
        if (self::coordinateIsRange($pCoordinateString)) {
            throw new Exception('Cell coordinate string can not be a range of cells');
        }

        // Split out any worksheet name from the coordinate
        list($worksheet, $pCoordinateString) = Worksheet::extractSheetTitle($pCoordinateString, true);
        if ($worksheet > '') {
            $worksheet .= '!';
        }

        // Create absolute coordinate
        list($column, $row) = self::coordinateFromString($pCoordinateString);
        $column = ltrim($column, '$');
        $row = ltrim($row, '$');

        return $worksheet . '$' . $column . '$' . $row;
    }

    /**
     * Split range into coordinate strings.
     *
     * @param string $pRange e.g. 'B4:D9' or 'B4:D9,H2:O11' or 'B4'
     *
     * @return array Array containing one or more arrays containing one or two coordinate strings
     *                                e.g. ['B4','D9'] or [['B4','D9'], ['H2','O11']]
     *                                        or ['B4']
     */
    public static function splitRange($pRange)
    {
        // Ensure $pRange is a valid range
        if (empty($pRange)) {
            $pRange = self::DEFAULT_RANGE;
        }

        $exploded = explode(',', $pRange);
        $counter = count($exploded);
        for ($i = 0; $i < $counter; ++$i) {
            $exploded[$i] = explode(':', $exploded[$i]);
        }

        return $exploded;
    }

    /**
     * Build range from coordinate strings.
     *
     * @param array $pRange Array containg one or more arrays containing one or two coordinate strings
     *
     * @throws Exception
     *
     * @return string String representation of $pRange
     */
    public static function buildRange(array $pRange)
    {
        // Verify range
        if (empty($pRange) || !is_array($pRange[0])) {
            throw new Exception('Range does not contain any information');
        }

        // Build range
        $counter = count($pRange);
        for ($i = 0; $i < $counter; ++$i) {
            $pRange[$i] = implode(':', $pRange[$i]);
        }

        return implode(',', $pRange);
    }

    /**
     * Calculate range boundaries.
     *
     * @param string $pRange Cell range (e.g. A1:A1)
     *
     * @return array Range coordinates [Start Cell, End Cell]
     *                    where Start Cell and End Cell are arrays (Column Number, Row Number)
     */
    public static function rangeBoundaries($pRange)
    {
        // Ensure $pRange is a valid range
        if (empty($pRange)) {
            $pRange = self::DEFAULT_RANGE;
        }

        // Uppercase coordinate
        $pRange = strtoupper($pRange);

        // Extract range
        if (strpos($pRange, ':') === false) {
            $rangeA = $rangeB = $pRange;
        } else {
            list($rangeA, $rangeB) = explode(':', $pRange);
        }

        // Calculate range outer borders
        $rangeStart = self::coordinateFromString($rangeA);
        $rangeEnd = self::coordinateFromString($rangeB);

        // Translate column into index
        $rangeStart[0] = self::columnIndexFromString($rangeStart[0]);
        $rangeEnd[0] = self::columnIndexFromString($rangeEnd[0]);

        return [$rangeStart, $rangeEnd];
    }

    /**
     * Calculate range dimension.
     *
     * @param string $pRange Cell range (e.g. A1:A1)
     *
     * @return array Range dimension (width, height)
     */
    public static function rangeDimension($pRange)
    {
        // Calculate range outer borders
        list($rangeStart, $rangeEnd) = self::rangeBoundaries($pRange);

        return [($rangeEnd[0] - $rangeStart[0] + 1), ($rangeEnd[1] - $rangeStart[1] + 1)];
    }

    /**
     * Calculate range boundaries.
     *
     * @param string $pRange Cell range (e.g. A1:A1)
     *
     * @return array Range coordinates [Start Cell, End Cell]
     *                    where Start Cell and End Cell are arrays [Column ID, Row Number]
     */
    public static function getRangeBoundaries($pRange)
    {
        // Ensure $pRange is a valid range
        if (empty($pRange)) {
            $pRange = self::DEFAULT_RANGE;
        }

        // Uppercase coordinate
        $pRange = strtoupper($pRange);

        // Extract range
        if (strpos($pRange, ':') === false) {
            $rangeA = $rangeB = $pRange;
        } else {
            list($rangeA, $rangeB) = explode(':', $pRange);
        }

        return [self::coordinateFromString($rangeA), self::coordinateFromString($rangeB)];
    }

    /**
     * Column index from string.
     *
     * @param string $pString eg 'A'
     *
     * @return int Column index (A = 1)
     */
    public static function columnIndexFromString($pString)
    {
        //    Using a lookup cache adds a slight memory overhead, but boosts speed
        //    caching using a static within the method is faster than a class static,
        //        though it's additional memory overhead
        static $indexCache = [];

        if (isset($indexCache[$pString])) {
            return $indexCache[$pString];
        }
        //    It's surprising how costly the strtoupper() and ord() calls actually are, so we use a lookup array rather than use ord()
        //        and make it case insensitive to get rid of the strtoupper() as well. Because it's a static, there's no significant
        //        memory overhead either
        static $columnLookup = [
            'A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6, 'G' => 7, 'H' => 8, 'I' => 9, 'J' => 10, 'K' => 11, 'L' => 12, 'M' => 13,
            'N' => 14, 'O' => 15, 'P' => 16, 'Q' => 17, 'R' => 18, 'S' => 19, 'T' => 20, 'U' => 21, 'V' => 22, 'W' => 23, 'X' => 24, 'Y' => 25, 'Z' => 26,
            'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6, 'g' => 7, 'h' => 8, 'i' => 9, 'j' => 10, 'k' => 11, 'l' => 12, 'm' => 13,
            'n' => 14, 'o' => 15, 'p' => 16, 'q' => 17, 'r' => 18, 's' => 19, 't' => 20, 'u' => 21, 'v' => 22, 'w' => 23, 'x' => 24, 'y' => 25, 'z' => 26,
        ];

        //    We also use the language construct isset() rather than the more costly strlen() function to match the length of $pString
        //        for improved performance
        if (isset($pString[0])) {
            if (!isset($pString[1])) {
                $indexCache[$pString] = $columnLookup[$pString];

                return $indexCache[$pString];
            } elseif (!isset($pString[2])) {
                $indexCache[$pString] = $columnLookup[$pString[0]] * 26 + $columnLookup[$pString[1]];

                return $indexCache[$pString];
            } elseif (!isset($pString[3])) {
                $indexCache[$pString] = $columnLookup[$pString[0]] * 676 + $columnLookup[$pString[1]] * 26 + $columnLookup[$pString[2]];

                return $indexCache[$pString];
            }
        }

        throw new Exception('Column string index can not be ' . ((isset($pString[0])) ? 'longer than 3 characters' : 'empty'));
    }

    /**
     * String from column index.
     *
     * @param int $columnIndex Column index (A = 1)
     *
     * @return string
     */
    public static function stringFromColumnIndex($columnIndex)
    {
        static $indexCache = [];

        if (!isset($indexCache[$columnIndex])) {
            $indexValue = $columnIndex;
            $base26 = null;
            do {
                $characterValue = ($indexValue % 26) ?: 26;
                $indexValue = ($indexValue - $characterValue) / 26;
                $base26 = chr($characterValue + 64) . ($base26 ?: '');
            } while ($indexValue > 0);
            $indexCache[$columnIndex] = $base26;
        }

        return $indexCache[$columnIndex];
    }

    /**
     * Extract all cell references in range, which may be comprised of multiple cell ranges.
     *
     * @param string $pRange Range (e.g. A1 or A1:C10 or A1:E10 A20:E25)
     *
     * @return array Array containing single cell references
     */
    public static function extractAllCellReferencesInRange($pRange)
    {
        $returnValue = [];

        // Explode spaces
        $cellBlocks = self::getCellBlocksFromRangeString($pRange);
        foreach ($cellBlocks as $cellBlock) {
            $returnValue = array_merge($returnValue, self::getReferencesForCellBlock($cellBlock));
        }

        //    Sort the result by column and row
        $sortKeys = [];
        foreach (array_unique($returnValue) as $coord) {
            $column = '';
            $row = 0;

            sscanf($coord, '%[A-Z]%d', $column, $row);
            $sortKeys[sprintf('%3s%09d', $column, $row)] = $coord;
        }
        ksort($sortKeys);

        // Return value
        return array_values($sortKeys);
    }

    /**
     * Get all cell references for an individual cell block.
     *
     * @param string $cellBlock A cell range e.g. A4:B5
     *
     * @return array All individual cells in that range
     */
    private static function getReferencesForCellBlock($cellBlock)
    {
        $returnValue = [];

        // Single cell?
        if (!self::coordinateIsRange($cellBlock)) {
            return (array) $cellBlock;
        }

        // Range...
        $ranges = self::splitRange($cellBlock);
        foreach ($ranges as $range) {
            // Single cell?
            if (!isset($range[1])) {
                $returnValue[] = $range[0];

                continue;
            }

            // Range...
            list($rangeStart, $rangeEnd) = $range;
            list($startColumn, $startRow) = self::coordinateFromString($rangeStart);
            list($endColumn, $endRow) = self::coordinateFromString($rangeEnd);
            $startColumnIndex = self::columnIndexFromString($startColumn);
            $endColumnIndex = self::columnIndexFromString($endColumn);
            ++$endColumnIndex;

            // Current data
            $currentColumnIndex = $startColumnIndex;
            $currentRow = $startRow;

            self::validateRange($cellBlock, $startColumnIndex, $endColumnIndex, $currentRow, $endRow);

            // Loop cells
            while ($currentColumnIndex < $endColumnIndex) {
                while ($currentRow <= $endRow) {
                    $returnValue[] = self::stringFromColumnIndex($currentColumnIndex) . $currentRow;
                    ++$currentRow;
                }
                ++$currentColumnIndex;
                $currentRow = $startRow;
            }
        }

        return $returnValue;
    }

    /**
     * Convert an associative array of single cell coordinates to values to an associative array
     * of cell ranges to values.  Only adjacent cell coordinates with the same
     * value will be merged.  If the value is an object, it must implement the method getHashCode().
     *
     * For example, this function converts:
     *
     *    [ 'A1' => 'x', 'A2' => 'x', 'A3' => 'x', 'A4' => 'y' ]
     *
     * to:
     *
     *    [ 'A1:A3' => 'x', 'A4' => 'y' ]
     *
     * @param array $pCoordCollection associative array mapping coordinates to values
     *
     * @return array associative array mapping coordinate ranges to valuea
     */
    public static function mergeRangesInCollection(array $pCoordCollection)
    {
        $hashedValues = [];
        $mergedCoordCollection = [];

        foreach ($pCoordCollection as $coord => $value) {
            if (self::coordinateIsRange($coord)) {
                $mergedCoordCollection[$coord] = $value;

                continue;
            }

            list($column, $row) = self::coordinateFromString($coord);
            $row = (int) (ltrim($row, '$'));
            $hashCode = $column . '-' . (is_object($value) ? $value->getHashCode() : $value);

            if (!isset($hashedValues[$hashCode])) {
                $hashedValues[$hashCode] = (object) [
                    'value' => $value,
                    'col' => $column,
                    'rows' => [$row],
                ];
            } else {
                $hashedValues[$hashCode]->rows[] = $row;
            }
        }

        ksort($hashedValues);

        foreach ($hashedValues as $hashedValue) {
            sort($hashedValue->rows);
            $rowStart = null;
            $rowEnd = null;
            $ranges = [];

            foreach ($hashedValue->rows as $row) {
                if ($rowStart === null) {
                    $rowStart = $row;
                    $rowEnd = $row;
                } elseif ($rowEnd === $row - 1) {
                    $rowEnd = $row;
                } else {
                    if ($rowStart == $rowEnd) {
                        $ranges[] = $hashedValue->col . $rowStart;
                    } else {
                        $ranges[] = $hashedValue->col . $rowStart . ':' . $hashedValue->col . $rowEnd;
                    }

                    $rowStart = $row;
                    $rowEnd = $row;
                }
            }

            if ($rowStart !== null) {
                if ($rowStart == $rowEnd) {
                    $ranges[] = $hashedValue->col . $rowStart;
                } else {
                    $ranges[] = $hashedValue->col . $rowStart . ':' . $hashedValue->col . $rowEnd;
                }
            }

            foreach ($ranges as $range) {
                $mergedCoordCollection[$range] = $hashedValue->value;
            }
        }

        return $mergedCoordCollection;
    }

    /**
     * Get the individual cell blocks from a range string, splitting by space and removing any $ characters.
     *
     * @param string $pRange
     *
     * @return string[]
     */
    private static function getCellBlocksFromRangeString($pRange)
    {
        return explode(' ', str_replace('$', '', strtoupper($pRange)));
    }

    /**
     * Check that the given range is valid, i.e. that the start column and row are not greater than the end column and
     * row.
     *
     * @param string $cellBlock The original range, for displaying a meaningful error message
     * @param int $startColumnIndex
     * @param int $endColumnIndex
     * @param int $currentRow
     * @param int $endRow
     */
    private static function validateRange($cellBlock, $startColumnIndex, $endColumnIndex, $currentRow, $endRow)
    {
        if ($startColumnIndex >= $endColumnIndex || $currentRow > $endRow) {
            throw new Exception('Invalid range: "' . $cellBlock . '"');
        }
    }
}
