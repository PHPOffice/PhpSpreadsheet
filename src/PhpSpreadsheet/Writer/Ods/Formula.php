<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Ods;

use Composer\Pcre\Preg;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\DefinedName;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class Formula
{
    /** @var string[] */
    private array $definedNames = [];

    /**
     * @param DefinedName[] $definedNames
     */
    public function __construct(array $definedNames)
    {
        foreach ($definedNames as $definedName) {
            $this->definedNames[] = $definedName->getName();
        }
    }

    public function convertFormula(string $formula, string $worksheetName = ''): string
    {
        // Convert only outside of string literals, so that the text in
        // ="THIS IS E1" is left alone rather than treated as a cell reference.
        $converted = '';
        foreach ($this->splitOnStringLiterals($formula) as $index => $part) {
            if (1 === $index % 2) { // the captured string literal
                $converted .= $part;

                continue;
            }

            $part = $this->convertCellReferences($part, $worksheetName);
            $part = $this->convertDefinedNames($part);
            $converted .= $this->convertFunctionNames($part);
        }
        $formula = $converted;

        if (!str_starts_with($formula, '=')) {
            $formula = '=' . $formula;
        }

        return 'of:' . $formula;
    }

    /**
     * Splits a formula into alternating segments outside and inside string literals,
     * with the literals themselves at the odd offsets.
     *
     * A range such as A1:B2 can never be interrupted by a string literal, so every
     * reference stays within a single segment and keeps its surrounding context.
     *
     * @return string[]
     */
    private function splitOnStringLiterals(string $formula): array
    {
        return Preg::split(
            '/(' . Calculation::CALCULATION_REGEXP_STRING . ')/ui',
            $formula,
            -1,
            PREG_SPLIT_DELIM_CAPTURE
        );
    }

    private function convertDefinedNames(string $formula): string
    {
        $splitCount = Preg::matchAllWithOffsets(
            '/' . Calculation::CALCULATION_REGEXP_DEFINEDNAME . '/mui',
            $formula,
            $splitRanges
        );

        $lengths = array_map([StringHelper::class, 'strlenAllowNull'], array_column($splitRanges[0], 0));
        $offsets = array_column($splitRanges[0], 1);
        $values = array_column($splitRanges[0], 0);

        while ($splitCount > 0) {
            --$splitCount;
            $length = $lengths[$splitCount];
            $offset = $offsets[$splitCount];
            $value = $values[$splitCount];

            if (in_array($value, $this->definedNames, true)) {
                $formula = substr($formula, 0, $offset) . '$$' . $value . substr($formula, $offset + $length);
            }
        }

        return $formula;
    }

    private function convertCellReferences(string $formula, string $worksheetName): string
    {
        $splitCount = Preg::matchAllWithOffsets(
            '/' . Calculation::CALCULATION_REGEXP_CELLREF_RELATIVE . '/mui',
            $formula,
            $splitRanges
        );

        $lengths = array_map([StringHelper::class, 'strlenAllowNull'], array_column($splitRanges[0], 0));
        $offsets = array_column($splitRanges[0], 1);

        $worksheets = $splitRanges[2];
        $columns = $splitRanges[6];
        $rows = $splitRanges[7];

        // Replace any commas in the formula with semicolons for Ods
        // If by chance there are commas in worksheet names, then they will be "fixed" again in the loop
        //    because we've already extracted worksheet names with our Preg::matchAllWithOffsets()
        $formula = str_replace(',', ';', $formula);
        while ($splitCount > 0) {
            --$splitCount;
            $length = $lengths[$splitCount];
            $offset = $offsets[$splitCount];
            $worksheet = $worksheets[$splitCount][0];
            $column = $columns[$splitCount][0];
            $row = $rows[$splitCount][0];

            $newRange = '';
            if (empty($worksheet)) {
                if (($offset === 0) || ($formula[$offset - 1] !== ':')) {
                    // We need a worksheet
                    $worksheet = $worksheetName;
                }
            } else {
                $worksheet = str_replace("''", "'", trim($worksheet, "'"));
            }
            if (!empty($worksheet)) {
                $newRange = "['" . str_replace("'", "''", $worksheet) . "'";
            } elseif (substr($formula, $offset - 1, 1) !== ':') {
                $newRange = '[';
            }
            $newRange .= '.';

            //if (!empty($column)) { // phpstan says always true
            $newRange .= $column;
            //}
            if (!empty($row)) {
                $newRange .= $row;
            }
            // close the wrapping [] unless this is the first part of a range
            $newRange .= substr($formula, $offset + $length, 1) !== ':' ? ']' : '';

            $formula = substr($formula, 0, $offset) . $newRange . substr($formula, $offset + $length);
        }

        return $formula;
    }

    private function convertFunctionNames(string $formula): string
    {
        return Preg::replace(
            [
                '/\b((CEILING|FLOOR)'
                    . '([.](MATH|PRECISE))?)\s*[(]/ui',
                '/\b(CEILING|FLOOR)[.]XCL\s*[(]/ui',
                '/\b(CEILING|FLOOR)[.]ODS\s*[(]/ui',
            ],
            [
                'COM.MICROSOFT.$1(',
                'COM.MICROSOFT.$1(',
                '$1(',
            ],
            $formula
        );
    }
}
