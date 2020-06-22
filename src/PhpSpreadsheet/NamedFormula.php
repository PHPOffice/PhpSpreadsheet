<?php

namespace PhpOffice\PhpSpreadsheet;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NamedFormula extends DefinedName
{
    /**
     * Create a new Named Formula.
     *
     * @param string $name
     * @param Worksheet $worksheet
     * @param string $formula
     * @param bool $localOnly
     * @param null|Worksheet $scope Scope. Only applies when $pLocalOnly = true. Null for global scope.
     */
    public function __construct($name, ?Worksheet $worksheet = null, $formula = null, $localOnly = false, $scope = null)
    {
        // Validate data
        if (($name === null) || ($formula === null)) {
            throw new Exception('Name or Formula Parameters cannot be null.');
        }
        parent::__construct($name, $worksheet, $formula, $localOnly, $scope);
    }

    /**
     * Get range.
     *
     * @return string
     */
    public function getFormula()
    {
        return $this->value;
    }

    /**
     * Set range.
     *
     * @param string $formula
     *
     * @return $this
     */
    public function setFormula($formula)
    {
        if ($formula !== null) {
            $this->value = $formula;
        }

        return $this;
    }
}
