<?php

namespace PhpOffice\PhpSpreadsheet;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NamedRange extends DefinedName
{
    /**
     * Create a new Named Range.
     *
     * @param string $name
     * @param Worksheet $worksheet
     * @param string $range
     * @param bool $localOnly
     * @param null|Worksheet $scope Scope. Only applies when $pLocalOnly = true. Null for global scope.
     */
    public function __construct($name, ?Worksheet $worksheet = null, $range = 'A1', $localOnly = false, $scope = null)
    {
        // Validate data
        if (($name === null) || ($range === null)) {
            throw new Exception('Name or Range Parameters cannot be null.');
        }

        parent::__construct($name, $worksheet, $range, $localOnly, $scope);
    }

    /**
     * Get range.
     *
     * @return string
     */
    public function getRange()
    {
        return $this->value;
    }

    /**
     * Set range.
     *
     * @param string $range
     *
     * @return $this
     */
    public function setRange($range)
    {
        if ($range !== null) {
            $this->value = $range;
        }

        return $this;
    }

    /**
     * Resolve a named range to a regular cell range.
     *
     * @param string $pNamedRange Named range
     * @param null|Worksheet $pSheet Scope. Use null for global scope
     *
     * @return NamedRange
     */
    public static function resolveRange($pNamedRange, Worksheet $pSheet)
    {
        return $pSheet->getParent()->getNamedRange($pNamedRange, $pSheet);
    }
}
