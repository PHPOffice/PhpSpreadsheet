<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting;

class ConditionalDataBarExtension
{
    /** <dataBar> attributes */

    /** @var int */
    private $minLength;

    /** @var int */
    private $maxLength;

    /** @var null|bool */
    private $border;

    /** @var null|bool */
    private $gradient;

    /** @var string */
    private $direction;

    /** @var null|bool */
    private $negativeBarBorderColorSameAsPositive;

    /** @var string */
    private $axisPosition;

    // <dataBar> children

    /** @var ConditionalFormatValueObject */
    private $maximumConditionalFormatValueObject;

    /** @var ConditionalFormatValueObject */
    private $minimumConditionalFormatValueObject;

    /** @var string */
    private $borderColor;

    /** @var string */
    private $negativeFillColor;

    /** @var string */
    private $negativeBorderColor;

    /** @var array */
    private $axisColor = [
        'rgb' => null,
        'theme' => null,
        'tint' => null,
    ];

    public function getXmlAttributes()
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

    public function getXmlElements()
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

    /**
     * @return int
     */
    public function getMinLength()
    {
        return $this->minLength;
    }

    public function setMinLength(int $minLength): self
    {
        $this->minLength = $minLength;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxLength()
    {
        return $this->maxLength;
    }

    public function setMaxLength(int $maxLength): self
    {
        $this->maxLength = $maxLength;

        return $this;
    }

    /**
     * @return null|bool
     */
    public function getBorder()
    {
        return $this->border;
    }

    public function setBorder(bool $border): self
    {
        $this->border = $border;

        return $this;
    }

    /**
     * @return null|bool
     */
    public function getGradient()
    {
        return $this->gradient;
    }

    public function setGradient(bool $gradient): self
    {
        $this->gradient = $gradient;

        return $this;
    }

    /**
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }

    public function setDirection(string $direction): self
    {
        $this->direction = $direction;

        return $this;
    }

    /**
     * @return null|bool
     */
    public function getNegativeBarBorderColorSameAsPositive()
    {
        return $this->negativeBarBorderColorSameAsPositive;
    }

    public function setNegativeBarBorderColorSameAsPositive(bool $negativeBarBorderColorSameAsPositive): self
    {
        $this->negativeBarBorderColorSameAsPositive = $negativeBarBorderColorSameAsPositive;

        return $this;
    }

    /**
     * @return string
     */
    public function getAxisPosition()
    {
        return $this->axisPosition;
    }

    public function setAxisPosition(string $axisPosition): self
    {
        $this->axisPosition = $axisPosition;

        return $this;
    }

    /**
     * @return ConditionalFormatValueObject
     */
    public function getMaximumConditionalFormatValueObject()
    {
        return $this->maximumConditionalFormatValueObject;
    }

    public function setMaximumConditionalFormatValueObject(ConditionalFormatValueObject $maximumConditionalFormatValueObject)
    {
        $this->maximumConditionalFormatValueObject = $maximumConditionalFormatValueObject;

        return $this;
    }

    /**
     * @return ConditionalFormatValueObject
     */
    public function getMinimumConditionalFormatValueObject()
    {
        return $this->minimumConditionalFormatValueObject;
    }

    public function setMinimumConditionalFormatValueObject(ConditionalFormatValueObject $minimumConditionalFormatValueObject)
    {
        $this->minimumConditionalFormatValueObject = $minimumConditionalFormatValueObject;

        return $this;
    }

    /**
     * @return string
     */
    public function getBorderColor()
    {
        return $this->borderColor;
    }

    public function setBorderColor(string $borderColor): self
    {
        $this->borderColor = $borderColor;

        return $this;
    }

    /**
     * @return string
     */
    public function getNegativeFillColor()
    {
        return $this->negativeFillColor;
    }

    public function setNegativeFillColor(string $negativeFillColor): self
    {
        $this->negativeFillColor = $negativeFillColor;

        return $this;
    }

    /**
     * @return string
     */
    public function getNegativeBorderColor()
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

    /**
     * @param mixed $rgb
     * @param null|mixed $theme
     * @param null|mixed $tint
     */
    public function setAxisColor($rgb, $theme = null, $tint = null): self
    {
        $this->axisColor = [
            'rgb' => $rgb,
            'theme' => $theme,
            'tint' => $tint,
        ];

        return $this;
    }
}
