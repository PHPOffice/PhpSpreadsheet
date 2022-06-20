<?php

namespace PhpOffice\PhpSpreadsheet\Chart;

class ChartColor
{
    const EXCEL_COLOR_TYPE_STANDARD = 'prstClr';
    const EXCEL_COLOR_TYPE_SCHEME = 'schemeClr';
    const EXCEL_COLOR_TYPE_ARGB = 'srgbClr';
    const EXCEL_COLOR_TYPES = [
        self::EXCEL_COLOR_TYPE_ARGB,
        self::EXCEL_COLOR_TYPE_SCHEME,
        self::EXCEL_COLOR_TYPE_STANDARD,
    ];

    /** @var string */
    private $value = '';

    /** @var string */
    private $type = '';

    /** @var ?int */
    private $alpha;

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
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

    public function getAlpha(): ?int
    {
        return $this->alpha;
    }

    public function setAlpha(?int $alpha): self
    {
        $this->alpha = $alpha;

        return $this;
    }

    /**
     * @param null|float|int|string $alpha
     */
    public function setColorProperties(?string $color, $alpha, ?string $type): self
    {
        if ($color !== null) {
            $this->setValue($color);
        }
        if ($type !== null) {
            $this->setType($type);
        }
        if ($alpha === null) {
            $this->setAlpha(null);
        } elseif (is_numeric($alpha)) {
            $this->setAlpha((int) $alpha);
        }

        return $this;
    }

    public function setColorPropertiesArray(array $color): self
    {
        if (array_key_exists('value', $color) && is_string($color['value'])) {
            $this->setValue($color['value']);
        }
        if (array_key_exists('type', $color) && is_string($color['type'])) {
            $this->setType($color['type']);
        }
        if (array_key_exists('alpha', $color)) {
            if ($color['alpha'] === null) {
                $this->setAlpha(null);
            } elseif (is_numeric($color['alpha'])) {
                $this->setAlpha((int) $color['alpha']);
            }
        }

        return $this;
    }

    /**
     * Get Color Property.
     *
     * @param string $propertyName
     *
     * @return null|int|string
     */
    public function getColorProperty($propertyName)
    {
        $retVal = null;
        if ($propertyName === 'value') {
            $retVal = $this->value;
        } elseif ($propertyName === 'type') {
            $retVal = $this->type;
        } elseif ($propertyName === 'alpha') {
            $retVal = $this->alpha;
        }

        return $retVal;
    }

    public static function alphaToXml(int $alpha): string
    {
        return (string) (100 - $alpha) . '000';
    }

    /**
     * @param float|int|string $alpha
     */
    public static function alphaFromXml($alpha): int
    {
        return 100 - ((int) $alpha / 1000);
    }
}
