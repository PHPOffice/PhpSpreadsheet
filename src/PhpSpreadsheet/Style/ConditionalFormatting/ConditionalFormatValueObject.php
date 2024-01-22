<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting;

class ConditionalFormatValueObject
{
    private string $type;

    private null|float|int|string $value;

    private ?string $cellFormula;

    public function __construct(string $type, null|float|int|string $value = null, ?string $cellFormula = null)
    {
        $this->type = $type;
        $this->value = $value;
        $this->cellFormula = $cellFormula;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getValue(): null|float|int|string
    {
        return $this->value;
    }

    public function setValue(null|float|int|string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getCellFormula(): ?string
    {
        return $this->cellFormula;
    }

    public function setCellFormula(?string $cellFormula): self
    {
        $this->cellFormula = $cellFormula;

        return $this;
    }
}
