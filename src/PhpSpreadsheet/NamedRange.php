<?php

namespace PhpOffice\PhpSpreadsheet;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NamedRange extends DefinedName
{
    /**
     * Create a new Named Range.
     */
    public function __construct(
        string $name,
        ?Worksheet $worksheet = null,
        string $range = 'A1',
        bool $localOnly = false,
        ?Worksheet $scope = null
    ) {
        if ($worksheet === null && $scope === null) {
            throw new Exception("A Named Range must specify a worksheet or a scope");
        } elseif ($worksheet === null) {
            $worksheet = $scope;
        }

        parent::__construct($name, $worksheet, $range, $localOnly, $scope);
    }

    /**
     * Get the range value.
     */
    public function getRange(): string
    {
        return $this->value;
    }

    /**
     * Set the range value.
     */
    public function setRange(string $range): self
    {
        if (!empty($range)) {
            $this->value = $range;
        }

        return $this;
    }

    /**
     * Resolve a named range to a regular cell range.
     */
    public static function resolveRange(string $pNamedRange, Worksheet $pSheet): ?DefinedName
    {
        return $pSheet->getParent()->getNamedRange($pNamedRange, $pSheet);
    }
}
