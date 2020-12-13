<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting;

use PhpOffice\PhpSpreadsheet\Style\Color;

class ConditionalDataBar
{
    /** <dataBar> attribute  */
    private $showValue;

    /** <dataBar> children */

    /** @var ConditionalFormatValueObject[] */
    private $conditionalFormatValueObjects = [];

    /** @var Color */
    private $color;

    /** @var ConditionalFormattingRuleExtension[] */
    private $conditionalFormattingRuleExtList = [];

    /**
     * @return null|string
     */
    public function getShowValue()
    {
        return $this->showValue;
    }

    /**
     * @param int $showValue
     */
    public function setShowValue($showValue)
    {
        $this->showValue = $showValue;

        return $this;
    }

    /**
     * @return ConditionalFormatValueObject[]
     */
    public function getConditionalFormatValueObjects(): array
    {
        return $this->conditionalFormatValueObjects;
    }

    /**
     * @param ConditionalFormatValueObject[] $conditionalFormatValueObjects
     */
    public function setConditionalFormatValueObjects(array $conditionalFormatValueObjects)
    {
        $this->conditionalFormatValueObjects = $conditionalFormatValueObjects;

        return $this;
    }

    /**
     * @param mixed $type
     * @param null|mixed $value
     * @param null|mixed $cellFomula
     *
     * @return $this
     */
    public function addConditionalFormatValueObject($type, $value = null, $cellFomula = null): self
    {
        $this->conditionalFormatValueObjects[] = new ConditionalFormatValueObject($type, $value, $cellFomula);

        return $this;
    }

    public function getColor(): Color
    {
        return $this->color;
    }

    public function setColor(Color $color): self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return ConditionalFormattingRuleExtension[]
     */
    public function getConditionalFormattingRuleExtList(): array
    {
        return $this->conditionalFormattingRuleExtList;
    }

    /**
     * @param ConditionalFormattingRuleExtension[] $conditionalFormattingRuleExtList
     */
    public function setConditionalFormattingRuleExtList(array $conditionalFormattingRuleExtList): self
    {
        $this->conditionalFormattingRuleExtList = $conditionalFormattingRuleExtList;

        return $this;
    }

    /**
     * @return $this
     */
    public function addConditionalFormattingRuleExtList(ConditionalFormattingRuleExtension $conditionalFormattingRuleExt): self
    {
        $this->conditionalFormattingRuleExtList[] = $conditionalFormattingRuleExt;

        return $this;
    }
}
