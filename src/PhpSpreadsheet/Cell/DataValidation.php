<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

class DataValidation
{
    // Data validation types
    public const TYPE_NONE = 'none';
    public const TYPE_CUSTOM = 'custom';
    public const TYPE_DATE = 'date';
    public const TYPE_DECIMAL = 'decimal';
    public const TYPE_LIST = 'list';
    public const TYPE_TEXTLENGTH = 'textLength';
    public const TYPE_TIME = 'time';
    public const TYPE_WHOLE = 'whole';

    // Data validation error styles
    public const STYLE_STOP = 'stop';
    public const STYLE_WARNING = 'warning';
    public const STYLE_INFORMATION = 'information';

    // Data validation operators
    public const OPERATOR_BETWEEN = 'between';
    public const OPERATOR_EQUAL = 'equal';
    public const OPERATOR_GREATERTHAN = 'greaterThan';
    public const OPERATOR_GREATERTHANOREQUAL = 'greaterThanOrEqual';
    public const OPERATOR_LESSTHAN = 'lessThan';
    public const OPERATOR_LESSTHANOREQUAL = 'lessThanOrEqual';
    public const OPERATOR_NOTBETWEEN = 'notBetween';
    public const OPERATOR_NOTEQUAL = 'notEqual';
    private const DEFAULT_OPERATOR = self::OPERATOR_BETWEEN;

    /**
     * Formula 1.
     */
    private string $formula1 = '';

    /**
     * Formula 2.
     */
    private string $formula2 = '';

    /**
     * Type.
     */
    private string $type = self::TYPE_NONE;

    /**
     * Error style.
     */
    private string $errorStyle = self::STYLE_STOP;

    /**
     * Operator.
     */
    private string $operator = self::DEFAULT_OPERATOR;

    /**
     * Allow Blank.
     */
    private bool $allowBlank = false;

    /**
     * Show DropDown.
     */
    private bool $showDropDown = false;

    /**
     * Show InputMessage.
     */
    private bool $showInputMessage = false;

    /**
     * Show ErrorMessage.
     */
    private bool $showErrorMessage = false;

    /**
     * Error title.
     */
    private string $errorTitle = '';

    /**
     * Error.
     */
    private string $error = '';

    /**
     * Prompt title.
     */
    private string $promptTitle = '';

    /**
     * Prompt.
     */
    private string $prompt = '';

    /**
     * Create a new DataValidation.
     */
    public function __construct()
    {
    }

    /**
     * Get Formula 1.
     */
    public function getFormula1(): string
    {
        return $this->formula1;
    }

    /**
     * Set Formula 1.
     *
     * @return $this
     */
    public function setFormula1(string $formula): static
    {
        $this->formula1 = $formula;

        return $this;
    }

    /**
     * Get Formula 2.
     */
    public function getFormula2(): string
    {
        return $this->formula2;
    }

    /**
     * Set Formula 2.
     *
     * @return $this
     */
    public function setFormula2(string $formula): static
    {
        $this->formula2 = $formula;

        return $this;
    }

    /**
     * Get Type.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set Type.
     *
     * @return $this
     */
    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get Error style.
     */
    public function getErrorStyle(): string
    {
        return $this->errorStyle;
    }

    /**
     * Set Error style.
     *
     * @param string $errorStyle see self::STYLE_*
     *
     * @return $this
     */
    public function setErrorStyle(string $errorStyle): static
    {
        $this->errorStyle = $errorStyle;

        return $this;
    }

    /**
     * Get Operator.
     */
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * Set Operator.
     *
     * @return $this
     */
    public function setOperator(string $operator): static
    {
        $this->operator = ($operator === '') ? self::DEFAULT_OPERATOR : $operator;

        return $this;
    }

    /**
     * Get Allow Blank.
     */
    public function getAllowBlank(): bool
    {
        return $this->allowBlank;
    }

    /**
     * Set Allow Blank.
     *
     * @return $this
     */
    public function setAllowBlank(bool $allowBlank): static
    {
        $this->allowBlank = $allowBlank;

        return $this;
    }

    /**
     * Get Show DropDown.
     */
    public function getShowDropDown(): bool
    {
        return $this->showDropDown;
    }

    /**
     * Set Show DropDown.
     *
     * @return $this
     */
    public function setShowDropDown(bool $showDropDown): static
    {
        $this->showDropDown = $showDropDown;

        return $this;
    }

    /**
     * Get Show InputMessage.
     */
    public function getShowInputMessage(): bool
    {
        return $this->showInputMessage;
    }

    /**
     * Set Show InputMessage.
     *
     * @return $this
     */
    public function setShowInputMessage(bool $showInputMessage): static
    {
        $this->showInputMessage = $showInputMessage;

        return $this;
    }

    /**
     * Get Show ErrorMessage.
     */
    public function getShowErrorMessage(): bool
    {
        return $this->showErrorMessage;
    }

    /**
     * Set Show ErrorMessage.
     *
     * @return $this
     */
    public function setShowErrorMessage(bool $showErrorMessage): static
    {
        $this->showErrorMessage = $showErrorMessage;

        return $this;
    }

    /**
     * Get Error title.
     */
    public function getErrorTitle(): string
    {
        return $this->errorTitle;
    }

    /**
     * Set Error title.
     *
     * @return $this
     */
    public function setErrorTitle(string $errorTitle): static
    {
        $this->errorTitle = $errorTitle;

        return $this;
    }

    /**
     * Get Error.
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * Set Error.
     *
     * @return $this
     */
    public function setError(string $error): static
    {
        $this->error = $error;

        return $this;
    }

    /**
     * Get Prompt title.
     */
    public function getPromptTitle(): string
    {
        return $this->promptTitle;
    }

    /**
     * Set Prompt title.
     *
     * @return $this
     */
    public function setPromptTitle(string $promptTitle): static
    {
        $this->promptTitle = $promptTitle;

        return $this;
    }

    /**
     * Get Prompt.
     */
    public function getPrompt(): string
    {
        return $this->prompt;
    }

    /**
     * Set Prompt.
     *
     * @return $this
     */
    public function setPrompt(string $prompt): static
    {
        $this->prompt = $prompt;

        return $this;
    }

    /**
     * Get hash code.
     *
     * @return string Hash code
     */
    public function getHashCode(): string
    {
        return md5(
            $this->formula1
            . $this->formula2
            . $this->type
            . $this->errorStyle
            . $this->operator
            . ($this->allowBlank ? 't' : 'f')
            . ($this->showDropDown ? 't' : 'f')
            . ($this->showInputMessage ? 't' : 'f')
            . ($this->showErrorMessage ? 't' : 'f')
            . $this->errorTitle
            . $this->error
            . $this->promptTitle
            . $this->prompt
            . $this->sqref
            . self::class
        );
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

    private ?string $sqref = null;

    public function getSqref(): ?string
    {
        return $this->sqref;
    }

    public function setSqref(?string $str): self
    {
        $this->sqref = $str;

        return $this;
    }
}
