<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ErrorValue;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class TorowTocol
{
    /**
     * Excel function TOCOL.
     *
     * @return mixed[]|string
     */
    public static function tocol(mixed $array, mixed $ignore = 0, mixed $byColumn = false): array|string
    {
        $result = self::torow($array, $ignore, $byColumn);
        if (is_array($result)) {
            return array_map((fn ($x) => [$x]), $result);
        }

        return $result;
    }

    /**
     * Excel function TOROW.
     *
     * @return mixed[]|string
     */
    public static function torow(mixed $array, mixed $ignore = 0, mixed $byColumn = false): array|string
    {
        if (!is_numeric($ignore)) {
            return ExcelError::VALUE();
        }
        $ignore = (int) $ignore;
        if ($ignore < 0 || $ignore > 3) {
            return ExcelError::VALUE();
        }
        if (is_int($byColumn) || is_float($byColumn)) {
            $byColumn = (bool) $byColumn;
        }
        if (!is_bool($byColumn)) {
            return ExcelError::VALUE();
        }
        if (!is_array($array)) {
            $array = [$array];
        }
        if ($byColumn) {
            $temp = [];
            foreach ($array as $row) {
                if (!is_array($row)) {
                    $row = [$row];
                }
                $temp[] = Functions::flattenArray($row);
            }
            $array = ChooseRowsEtc::transpose($temp);
        } else {
            $array = Functions::flattenArray($array);
        }

        return self::byRow($array, $ignore);
    }

    /**
     * @param mixed[] $array
     *
     * @return mixed[]
     */
    private static function byRow(array $array, int $ignore): array
    {
        $returnMatrix = [];
        foreach ($array as $row) {
            if (!is_array($row)) {
                $row = [$row];
            }
            foreach ($row as $cell) {
                if ($cell === null) {
                    if ($ignore === 1 || $ignore === 3) {
                        continue;
                    }
                    $cell = 0;
                } elseif (ErrorValue::isError($cell, true)) {
                    if ($ignore === 2 || $ignore === 3) {
                        continue;
                    }
                }
                $returnMatrix[] = $cell;
            }
        }

        return $returnMatrix;
    }
}
