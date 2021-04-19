<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting;

class ConditionalFormatValueObject
{
    private $type;

    private $value;

    private $cellFormula;

    /**
     * ConditionalFormatValueObject constructor.
     *
     * @param null|mixed $cellFormula
     */
    public function __construct($type, $value = null, $cellFormula = null)
    {
        $this->type = $type;
        $this->value = $value;
        $this->cellFormula = $cellFormula;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCellFormula()
    {
        return $this->cellFormula;
    }

    /**
     * @param mixed $cellFormula
     */
    public function setCellFormula($cellFormula)
    {
        $this->cellFormula = $cellFormula;

        return $this;
    }
}
