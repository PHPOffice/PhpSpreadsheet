<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting;

class ConditionalDataBar
{
    /** <dataBar> attribute  */

    /** @var null|bool */
    private $showValue;

    /** <dataBar> children */

    /** @var ?ConditionalFormatValueObject */
    private $minimumConditionalFormatValueObject;

    /** @var ?ConditionalFormatValueObject */
    private $maximumConditionalFormatValueObject;

    /** @var string */
    private $color;

    /** <extLst> */

    /** @var ?ConditionalFormattingRuleExtension */
    private $conditionalFormattingRuleExt;

    /**
     * @return null|bool
     */
    public function getShowValue()
    {
        return $this->showValue;
    }

    /**
     * @param bool $showValue
     */
    public function setShowValue($showValue): self
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
