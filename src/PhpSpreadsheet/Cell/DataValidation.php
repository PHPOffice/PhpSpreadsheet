<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

class DataValidation
{
    // Data validation types
    const TYPE_NONE = 'none';
    const TYPE_CUSTOM = 'custom';
    const TYPE_DATE = 'date';
    const TYPE_DECIMAL = 'decimal';
    const TYPE_LIST = 'list';
    const TYPE_TEXTLENGTH = 'textLength';
    const TYPE_TIME = 'time';
    const TYPE_WHOLE = 'whole';

    // Data validation error styles
    const STYLE_STOP = 'stop';
    const STYLE_WARNING = 'warning';
    const STYLE_INFORMATION = 'information';

    // Data validation operators
    const OPERATOR_BETWEEN = 'between';
    const OPERATOR_EQUAL = 'equal';
    const OPERATOR_GREATERTHAN = 'greaterThan';
    const OPERATOR_GREATERTHANOREQUAL = 'greaterThanOrEqual';
    const OPERATOR_LESSTHAN = 'lessThan';
    const OPERATOR_LESSTHANOREQUAL = 'lessThanOrEqual';
    const OPERATOR_NOTBETWEEN = 'notBetween';
    const OPERATOR_NOTEQUAL = 'notEqual';

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
    private string $operator = self::OPERATOR_BETWEEN;

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
     * @param string $type
     *
     * @return $this
     */
    public function setType($type): static
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
    public function setErrorStyle($errorStyle): static
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
     * @param string $operator
     *
     * @return $this
     */
    public function setOperator($operator): static
    {
        $this->operator = $operator;

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
     * @param bool $allowBlank
     *
     * @return $this
     */
    public function setAllowBlank($allowBlank): static
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
     * @param bool $showDropDown
     *
     * @return $this
     */
    public function setShowDropDown($showDropDown): static
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
     * @param bool $showInputMessage
     *
     * @return $this
     */
    public function setShowInputMessage($showInputMessage): static
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
     * @param bool $showErrorMessage
     *
     * @return $this
     */
    public function setShowErrorMessage($showErrorMessage): static
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
     * @param string $errorTitle
     *
     * @return $this
     */
    public function setErrorTitle($errorTitle): static
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
     * @param string $error
     *
     * @return $this
     */
    public function setError($error): static
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
     * @param string $promptTitle
     *
     * @return $this
     */
    public function setPromptTitle($promptTitle): static
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
     * @param string $prompt
     *
     * @return $this
     */
    public function setPrompt($prompt): static
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
            . __CLASS__
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
