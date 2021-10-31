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
     *
     * @var string
     */
    private $formula1 = '';

    /**
     * Formula 2.
     *
     * @var string
     */
    private $formula2 = '';

    /**
     * Type.
     *
     * @var string
     */
    private $type = self::TYPE_NONE;

    /**
     * Error style.
     *
     * @var string
     */
    private $errorStyle = self::STYLE_STOP;

    /**
     * Operator.
     *
     * @var string
     */
    private $operator = self::OPERATOR_BETWEEN;

    /**
     * Allow Blank.
     *
     * @var bool
     */
    private $allowBlank = false;

    /**
     * Show DropDown.
     *
     * @var bool
     */
    private $showDropDown = false;

    /**
     * Show InputMessage.
     *
     * @var bool
     */
    private $showInputMessage = false;

    /**
     * Show ErrorMessage.
     *
     * @var bool
     */
    private $showErrorMessage = false;

    /**
     * Error title.
     *
     * @var string
     */
    private $errorTitle = '';

    /**
     * Error.
     *
     * @var string
     */
    private $error = '';

    /**
     * Prompt title.
     *
     * @var string
     */
    private $promptTitle = '';

    /**
     * Prompt.
     *
     * @var string
     */
    private $prompt = '';

    /**
     * Create a new DataValidation.
     */
    public function __construct()
    {
    }

    /**
     * Get Formula 1.
     *
     * @return string
     */
    public function getFormula1()
    {
        return $this->formula1;
    }

    /**
     * Set Formula 1.
     *
     * @param string $formula
     *
     * @return $this
     */
    public function setFormula1($formula)
    {
        $this->formula1 = $formula;

        return $this;
    }

    /**
     * Get Formula 2.
     *
     * @return string
     */
    public function getFormula2()
    {
        return $this->formula2;
    }

    /**
     * Set Formula 2.
     *
     * @param string $formula
     *
     * @return $this
     */
    public function setFormula2($formula)
    {
        $this->formula2 = $formula;

        return $this;
    }

    /**
     * Get Type.
     *
     * @return string
     */
    public function getType()
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
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get Error style.
     *
     * @return string
     */
    public function getErrorStyle()
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
    public function setErrorStyle($errorStyle)
    {
        $this->errorStyle = $errorStyle;

        return $this;
    }

    /**
     * Get Operator.
     *
     * @return string
     */
    public function getOperator()
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
    public function setOperator($operator)
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * Get Allow Blank.
     *
     * @return bool
     */
    public function getAllowBlank()
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
    public function setAllowBlank($allowBlank)
    {
        $this->allowBlank = $allowBlank;

        return $this;
    }

    /**
     * Get Show DropDown.
     *
     * @return bool
     */
    public function getShowDropDown()
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
    public function setShowDropDown($showDropDown)
    {
        $this->showDropDown = $showDropDown;

        return $this;
    }

    /**
     * Get Show InputMessage.
     *
     * @return bool
     */
    public function getShowInputMessage()
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
    public function setShowInputMessage($showInputMessage)
    {
        $this->showInputMessage = $showInputMessage;

        return $this;
    }

    /**
     * Get Show ErrorMessage.
     *
     * @return bool
     */
    public function getShowErrorMessage()
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
    public function setShowErrorMessage($showErrorMessage)
    {
        $this->showErrorMessage = $showErrorMessage;

        return $this;
    }

    /**
     * Get Error title.
     *
     * @return string
     */
    public function getErrorTitle()
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
    public function setErrorTitle($errorTitle)
    {
        $this->errorTitle = $errorTitle;

        return $this;
    }

    /**
     * Get Error.
     *
     * @return string
     */
    public function getError()
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
    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * Get Prompt title.
     *
     * @return string
     */
    public function getPromptTitle()
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
    public function setPromptTitle($promptTitle)
    {
        $this->promptTitle = $promptTitle;

        return $this;
    }

    /**
     * Get Prompt.
     *
     * @return string
     */
    public function getPrompt()
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
    public function setPrompt($prompt)
    {
        $this->prompt = $prompt;

        return $this;
    }

    /**
     * Get hash code.
     *
     * @return string Hash code
     */
    public function getHashCode()
    {
        return md5(
            $this->formula1 .
            $this->formula2 .
            $this->type .
            $this->errorStyle .
            $this->operator .
            ($this->allowBlank ? 't' : 'f') .
            ($this->showDropDown ? 't' : 'f') .
            ($this->showInputMessage ? 't' : 'f') .
            ($this->showErrorMessage ? 't' : 'f') .
            $this->errorTitle .
            $this->error .
            $this->promptTitle .
            $this->prompt .
            __CLASS__
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

    /** @var ?string */
    private $sqref;

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
