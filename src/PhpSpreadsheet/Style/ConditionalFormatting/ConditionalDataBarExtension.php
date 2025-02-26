<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting;

class ConditionalDataBarExtension
{
    /** <dataBar> attributes */
    private int $minLength;

    private int $maxLength;

    private ?bool $border = null;

    private ?bool $gradient = null;

    private ?string $direction = null;

    private ?bool $negativeBarBorderColorSameAsPositive = null;

    private ?string $axisPosition = null;

    // <dataBar> children

    private ConditionalFormatValueObject $maximumConditionalFormatValueObject;

    private ConditionalFormatValueObject $minimumConditionalFormatValueObject;

    private ?string $borderColor = null;

    private ?string $negativeFillColor = null;

    private ?string $negativeBorderColor = null;

    private array $axisColor = [
        'rgb' => null,
        'theme' => null,
        'tint' => null,
    ];

    public function getXmlAttributes(): array
    {
        $ret = [];
        foreach (['minLength', 'maxLength', 'direction', 'axisPosition'] as $attrKey) {
            if (null !== $this->{$attrKey}) {
                $ret[$attrKey] = $this->{$attrKey};
            }
        }
        foreach (['border', 'gradient', 'negativeBarBorderColorSameAsPositive'] as $attrKey) {
            if (null !== $this->{$attrKey}) {
                $ret[$attrKey] = $this->{$attrKey} ? '1' : '0';
            }
        }

        return $ret;
    }

    public function getXmlElements(): array
    {
        $ret = [];
        $elms = ['borderColor', 'negativeFillColor', 'negativeBorderColor'];
        foreach ($elms as $elmKey) {
            if (null !== $this->{$elmKey}) {
                $ret[$elmKey] = ['rgb' => $this->{$elmKey}];
            }
        }
        foreach (array_filter($this->axisColor) as $attrKey => $axisColorAttr) {
            if (!isset($ret['axisColor'])) {
                $ret['axisColor'] = [];
            }
            $ret['axisColor'][$attrKey] = $axisColorAttr;
        }

        return $ret;
    }

    public function getMinLength(): int
    {
        return $this->minLength;
    }

    public function setMinLength(int $minLength): self
    {
        $this->minLength = $minLength;

        return $this;
    }

    public function getMaxLength(): int
    {
        return $this->maxLength;
    }

    public function setMaxLength(int $maxLength): self
    {
        $this->maxLength = $maxLength;

        return $this;
    }

    public function getBorder(): ?bool
    {
        return $this->border;
    }

    public function setBorder(bool $border): self
    {
        $this->border = $border;

        return $this;
    }

    public function getGradient(): ?bool
    {
        return $this->gradient;
    }

    public function setGradient(bool $gradient): self
    {
        $this->gradient = $gradient;

        return $this;
    }

    public function getDirection(): ?string
    {
        return $this->direction;
    }

    public function setDirection(string $direction): self
    {
        $this->direction = $direction;

        return $this;
    }

    public function getNegativeBarBorderColorSameAsPositive(): ?bool
    {
        return $this->negativeBarBorderColorSameAsPositive;
    }

    public function setNegativeBarBorderColorSameAsPositive(bool $negativeBarBorderColorSameAsPositive): self
    {
        $this->negativeBarBorderColorSameAsPositive = $negativeBarBorderColorSameAsPositive;

        return $this;
    }

    public function getAxisPosition(): ?string
    {
        return $this->axisPosition;
    }

    public function setAxisPosition(string $axisPosition): self
    {
        $this->axisPosition = $axisPosition;

        return $this;
    }

    public function getMaximumConditionalFormatValueObject(): ConditionalFormatValueObject
    {
        return $this->maximumConditionalFormatValueObject;
    }

    public function setMaximumConditionalFormatValueObject(ConditionalFormatValueObject $maximumConditionalFormatValueObject): self
    {
        $this->maximumConditionalFormatValueObject = $maximumConditionalFormatValueObject;

        return $this;
    }

    public function getMinimumConditionalFormatValueObject(): ConditionalFormatValueObject
    {
        return $this->minimumConditionalFormatValueObject;
    }

    public function setMinimumConditionalFormatValueObject(ConditionalFormatValueObject $minimumConditionalFormatValueObject): self
    {
        $this->minimumConditionalFormatValueObject = $minimumConditionalFormatValueObject;

        return $this;
    }

    public function getBorderColor(): ?string
    {
        return $this->borderColor;
    }

    public function setBorderColor(string $borderColor): self
    {
        $this->borderColor = $borderColor;

        return $this;
    }

    public function getNegativeFillColor(): ?string
    {
        return $this->negativeFillColor;
    }

    public function setNegativeFillColor(string $negativeFillColor): self
    {
        $this->negativeFillColor = $negativeFillColor;

        return $this;
    }

    public function getNegativeBorderColor(): ?string
    {
        return $this->negativeBorderColor;
    }

    public function setNegativeBorderColor(string $negativeBorderColor): self
    {
        $this->negativeBorderColor = $negativeBorderColor;

        return $this;
    }

    public function getAxisColor(): array
    {
        return $this->axisColor;
    }

    public function setAxisColor(mixed $rgb, mixed $theme = null, mixed $tint = null): self
    {
        $this->axisColor = [
            'rgb' => $rgb,
            'theme' => $theme,
            'tint' => $tint,
        ];

        return $this;
    }
}
