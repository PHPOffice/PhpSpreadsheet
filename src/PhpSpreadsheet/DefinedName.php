<?php

namespace PhpOffice\PhpSpreadsheet;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

abstract class DefinedName
{
    public const REGEXP_FORMULA = '[^_\p{N}\p{L}:, \$\'!]';

    /**
     * Name.
     *
     * @var string
     */
    protected $name;

    /**
     * Worksheet on which the defined name can be resolved.
     *
     * @var Worksheet
     */
    protected $worksheet;

    /**
     * Value of the named object.
     *
     * @var string
     */
    protected $value;

    /**
     * Is the defined named local? (i.e. can only be used on $this->worksheet).
     *
     * @var bool
     */
    protected $localOnly;

    /**
     * Scope.
     *
     * @var Worksheet
     */
    protected $scope;

    /**
     * Whether this is a named range or a named formula.
     *
     * @var bool
     */
    protected $isFormula;

    /**
     * Create a new Defined Name.
     *
     * @param string $name
     * @param Worksheet $worksheet
     * @param string $value
     * @param bool $localOnly
     * @param null|Worksheet $scope Scope. Only applies when $pLocalOnly = true. Null for global scope.
     */
    public function __construct($name, ?Worksheet $worksheet = null, $value = null, $localOnly = false, $scope = null)
    {
        // Set local members
        $this->name = $name;
        $this->worksheet = $worksheet;
        $this->value = $value;
        $this->localOnly = $localOnly;
        $this->scope = ($localOnly == true) ? (($scope == null) ? $worksheet : $scope) : null;
        // If the range string contains characters that aren't associated with the range definition (A-Z,1-9
        //      for cell references, and $, or the range operators (colon comma or space), quotes and ! for
        //      worksheet names
        //  then this is treated as a named formula, and not a named range
        $this->isFormula = self::testIfFormula($value);
    }

    public static function createInstance($name, ?Worksheet $worksheet = null, $value = null, $localOnly = false, $scope = null)
    {
        echo "DEFINED NAME {$name} HAS VALUE {$value}", PHP_EOL;
        $isFormula = self::testIfFormula($value);
        $type = $isFormula ? 'FORMULA' : 'RANGE';
        echo "IDENTIFIED AS {$type}", PHP_EOL;
        if ($isFormula) {
            return new NamedFormula($name, $worksheet, $value, $localOnly, $scope);
        }

        return new NamedRange($name, $worksheet, $value, $localOnly, $scope);
    }

    public static function testIfFormula(string $value): bool
    {
        $segMatcher = false;
        foreach (explode("'", $value) as $subVal) {
            //    Only test in alternate array entries (the non-quoted blocks)
            if (($segMatcher = !$segMatcher) &&
                (preg_match('/' . self::REGEXP_FORMULA . '/miu', $subVal))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setName($value)
    {
        if ($value !== null) {
            // Old title
            $oldTitle = $this->name;

            // Re-attach
            if ($this->worksheet !== null) {
                $this->worksheet->getParent()->removeNamedRange($this->name, $this->worksheet);
            }
            $this->name = $value;

            if ($this->worksheet !== null) {
                $this->worksheet->getParent()->addNamedRange($this);
            }

            // New title
            $newTitle = $this->name;
            ReferenceHelper::getInstance()->updateNamedFormulas($this->worksheet->getParent(), $oldTitle, $newTitle);
        }

        return $this;
    }

    /**
     * Get worksheet.
     *
     * @return Worksheet
     */
    public function getWorksheet()
    {
        return $this->worksheet;
    }

    /**
     * Set worksheet.
     *
     * @param Worksheet $value
     *
     * @return $this
     */
    public function setWorksheet(?Worksheet $value = null)
    {
        if ($value !== null) {
            $this->worksheet = $value;
        }

        return $this;
    }

    /**
     * Get range.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set range.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        if ($value !== null) {
            $this->value = $value;
        }

        return $this;
    }

    /**
     * Get localOnly.
     *
     * @return bool
     */
    public function getLocalOnly()
    {
        return $this->localOnly;
    }

    /**
     * Set localOnly.
     *
     * @param bool $value
     *
     * @return $this
     */
    public function setLocalOnly($value)
    {
        $this->localOnly = $value;
        $this->scope = $value ? $this->worksheet : null;

        return $this;
    }

    /**
     * Get scope.
     *
     * @return null|Worksheet
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * Set scope.
     *
     * @return $this
     */
    public function setScope(?Worksheet $value = null)
    {
        $this->scope = $value;
        $this->localOnly = $value != null;

        return $this;
    }

    /**
     * Identify whether this is a named range or a named formula.
     *
     * @return bool
     */
    public function isFormula()
    {
        return $this->isFormula;
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if (is_object($value)) {
                $this->$key = clone $value;
            } else {
                $this->$key = $value;
            }
        }
    }
}
