<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

class Lookup
{
    /**
     * LOOKUP
     * The LOOKUP function searches for value either from a one-row or one-column range or from an array.
     *
     * @param mixed $lookupValue The value that you want to match in lookup_array
     * @param mixed $lookupVector The range of cells being searched
     * @param null|mixed $resultVector The column from which the matching value must be returned
     *
     * @return mixed The value of the found cell
     */
    public static function lookup($lookupValue, $lookupVector, $resultVector = null)
    {
        $lookupValue = Functions::flattenSingleValue($lookupValue);

        if (!is_array($lookupVector)) {
            return Functions::NA();
        }
        $hasResultVector = isset($resultVector);
        $lookupRows = count($lookupVector);
        $l = array_keys($lookupVector);
        $l = array_shift($l);
        $lookupColumns = count($lookupVector[$l]);
        // we correctly orient our results
        if (($lookupRows === 1 && $lookupColumns > 1) || (!$hasResultVector && $lookupRows === 2 && $lookupColumns !== 2)) {
            $lookupVector = LookupRef::TRANSPOSE($lookupVector);
            $lookupRows = count($lookupVector);
            $l = array_keys($lookupVector);
            $lookupColumns = count($lookupVector[array_shift($l)]);
        }

        if ($resultVector === null) {
            $resultVector = $lookupVector;
        }
        $resultRows = count($resultVector);
        $l = array_keys($resultVector);
        $l = array_shift($l);
        $resultColumns = count($resultVector[$l]);
        // we correctly orient our results
        if ($resultRows === 1 && $resultColumns > 1) {
            $resultVector = LookupRef::TRANSPOSE($resultVector);
        }

        if ($lookupRows === 2 && !$hasResultVector) {
            $resultVector = array_pop($lookupVector);
            $lookupVector = array_shift($lookupVector);
        }

        if ($lookupColumns !== 2) {
            foreach ($lookupVector as &$value) {
                if (is_array($value)) {
                    $k = array_keys($value);
                    $key1 = $key2 = array_shift($k);
                    ++$key2;
                    $dataValue1 = $value[$key1];
                } else {
                    $key1 = 0;
                    $key2 = 1;
                    $dataValue1 = $value;
                }
                $dataValue2 = array_shift($resultVector);
                if (is_array($dataValue2)) {
                    $dataValue2 = array_shift($dataValue2);
                }
                $value = [$key1 => $dataValue1, $key2 => $dataValue2];
            }
            unset($value);
        }

        return VLookup::lookup($lookupValue, $lookupVector, 2);
    }
}
