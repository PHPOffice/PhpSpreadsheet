<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting;

class ConditionalIconSet
{
    /** The icon set to display. */
    private ?IconSetValues $iconSetType = null;

    /**  If true, reverses the default order of the icons in this icon set. */
    private ?bool $reverse = null;

    /** Indicates whether to show the values of the cells on which this icon set is applied. */
    private ?bool $showValue = null;

    /**
     * If true, indicates that the icon set is a custom icon set.
     * If this value is "true", there MUST be the same number of cfIcon elements
     * as cfvo elements.
     * If this value is "false", there MUST be 0 cfIcon elements.
     */
    private ?bool $custom = null;

    /** @var ConditionalFormatValueObject[] */
    private array $cfvos = [];

    public function getIconSetType(): ?IconSetValues
    {
        return $this->iconSetType;
    }

    public function setIconSetType(IconSetValues $type): self
    {
        $this->iconSetType = $type;

        return $this;
    }

    public function getReverse(): ?bool
    {
        return $this->reverse;
    }

    public function setReverse(bool $reverse): self
    {
        $this->reverse = $reverse;

        return $this;
    }

    public function getShowValue(): ?bool
    {
        return $this->showValue;
    }

    public function setShowValue(bool $showValue): self
    {
        $this->showValue = $showValue;

        return $this;
    }

    public function getCustom(): ?bool
    {
        return $this->custom;
    }

    public function setCustom(bool $custom): self
    {
        $this->custom = $custom;

        return $this;
    }

    /**
     * Get the conditional format value objects.
     *
     * @return ConditionalFormatValueObject[]
     */
    public function getCfvos(): array
    {
        return $this->cfvos;
    }

    /**
     * Set the conditional format value objects.
     *
     * @param ConditionalFormatValueObject[] $cfvos
     */
    public function setCfvos(array $cfvos): self
    {
        $this->cfvos = $cfvos;

        return $this;
    }
}
