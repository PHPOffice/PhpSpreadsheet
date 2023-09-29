<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting;

class ConditionalDataBar
{
    private ?bool $showValue = null;

    private ?ConditionalFormatValueObject $minimumConditionalFormatValueObject = null;

    private ?ConditionalFormatValueObject $maximumConditionalFormatValueObject = null;

    private string $color = '';

    private ?ConditionalFormattingRuleExtension $conditionalFormattingRuleExt = null;

    public function getShowValue(): ?bool
    {
        return $this->showValue;
    }

    public function setShowValue(bool $showValue): self
    {
        $this->showValue = $showValue;

        return $this;
    }

    public function getMinimumConditionalFormatValueObject(): ?ConditionalFormatValueObject
    {
        return $this->minimumConditionalFormatValueObject;
    }

    public function setMinimumConditionalFormatValueObject(ConditionalFormatValueObject $minimumConditionalFormatValueObject): self
    {
        $this->minimumConditionalFormatValueObject = $minimumConditionalFormatValueObject;

        return $this;
    }

    public function getMaximumConditionalFormatValueObject(): ?ConditionalFormatValueObject
    {
        return $this->maximumConditionalFormatValueObject;
    }

    public function setMaximumConditionalFormatValueObject(ConditionalFormatValueObject $maximumConditionalFormatValueObject): self
    {
        $this->maximumConditionalFormatValueObject = $maximumConditionalFormatValueObject;

        return $this;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getConditionalFormattingRuleExt(): ?ConditionalFormattingRuleExtension
    {
        return $this->conditionalFormattingRuleExt;
    }

    public function setConditionalFormattingRuleExt(ConditionalFormattingRuleExtension $conditionalFormattingRuleExt): self
    {
        $this->conditionalFormattingRuleExt = $conditionalFormattingRuleExt;

        return $this;
    }
}
