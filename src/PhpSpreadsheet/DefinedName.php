<?php

namespace PhpOffice\PhpSpreadsheet;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

abstract class DefinedName
{
    protected const REGEXP_IDENTIFY_FORMULA = '[^_\p{N}\p{L}:, \$\'!]';

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
     */
    public function __construct(
        string $name,
        ?Worksheet $worksheet = null,
        ?string $value = null,
        bool $localOnly = false,
        ?Worksheet $scope = null
    ) {
        if ($worksheet === null) {
            $worksheet = $scope;
        }

        // Set local members
        $this->name = $name;
        $this->worksheet = $worksheet;
        $this->value = (string) $value;
        $this->localOnly = $localOnly;
        // If local only, then the scope will be set to worksheet unless a scope is explicitly set
        $this->scope = ($localOnly === true) ? (($scope === null) ? $worksheet : $scope) : null;
        // If the range string contains characters that aren't associated with the range definition (A-Z,1-9
        //      for cell references, and $, or the range operators (colon comma or space), quotes and ! for
        //      worksheet names
        //  then this is treated as a named formula, and not a named range
        $this->isFormula = self::testIfFormula($this->value);
    }

    /**
     * Create a new defined name, either a range or a formula.
     */
    public static function createInstance(
        string $name,
        ?Worksheet $worksheet = null,
        ?string $value = null,
        bool $localOnly = false,
        ?Worksheet $scope = null
    ): self {
        $value = (string) $value;
        $isFormula = self::testIfFormula($value);
        if ($isFormula) {
            return new NamedFormula($name, $worksheet, $value, $localOnly, $scope);
        }

        return new NamedRange($name, $worksheet, $value, $localOnly, $scope);
    }

    public static function testIfFormula(string $value): bool
    {
        if (substr($value, 0, 1) === '=') {
            $value = substr($value, 1);
        }

        if (is_numeric($value)) {
            return true;
        }

        $segMatcher = false;
        foreach (explode("'", $value) as $subVal) {
            //    Only test in alternate array entries (the non-quoted blocks)
            if (
                ($segMatcher = !$segMatcher) &&
                (preg_match('/' . self::REGEXP_IDENTIFY_FORMULA . '/miu', $subVal))
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set name.
     */
    public function setName(string $name): self
    {
        if (!empty($name)) {
            // Old title
            $oldTitle = $this->name;

            // Re-attach
            if ($this->worksheet !== null) {
                $this->worksheet->getParent()->removeNamedRange($this->name, $this->worksheet);
            }
            $this->name = $name;

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
     */
    public function getWorksheet(): ?Worksheet
    {
        return $this->worksheet;
    }

    /**
     * Set worksheet.
     */
    public function setWorksheet(?Worksheet $value): self
    {
        $this->worksheet = $value;

        return $this;
    }

    /**
     * Get range or formula value.
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Set range or formula  value.
     */
    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get localOnly.
     */
    public function getLocalOnly(): bool
    {
        return $this->localOnly;
    }

    /**
     * Set localOnly.
     */
    public function setLocalOnly(bool $value): self
    {
        $this->localOnly = $value;
        $this->scope = $value ? $this->worksheet : null;

        return $this;
    }

    /**
     * Get scope.
     */
    public function getScope(): ?Worksheet
    {
        return $this->scope;
    }

    /**
     * Set scope.
     */
    public function setScope(?Worksheet $value): self
    {
        $this->scope = $value;
        $this->localOnly = $value !== null;

        return $this;
    }

    /**
     * Identify whether this is a named range or a named formula.
     */
    public function isFormula(): bool
    {
        return $this->isFormula;
    }

    /**
     * Resolve a named range to a regular cell range or formula.
     */
    public static function resolveName(string $pDefinedName, Worksheet $pSheet): ?self
    {
        return $pSheet->getParent()->getDefinedName($pDefinedName, $pSheet);
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
