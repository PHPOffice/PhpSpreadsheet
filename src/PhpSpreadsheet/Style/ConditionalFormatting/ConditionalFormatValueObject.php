<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting;

class ConditionalFormatValueObject
{
    /** @var mixed */
    private $type;

    /** @var mixed */
    private $value;

    /** @var mixed */
    private $cellFormula;

    /**
     * ConditionalFormatValueObject constructor.
     *
     * @param null|mixed $cellFormula
     */
    public function __construct(mixed $type, mixed $value = null, $cellFormula = null)
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

    public function setType(mixed $type): self
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

    public function setValue(mixed $value): self
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

    public function setCellFormula(mixed $cellFormula): self
    {
        $this->cellFormula = $cellFormula;

        return $this;
    }
}
