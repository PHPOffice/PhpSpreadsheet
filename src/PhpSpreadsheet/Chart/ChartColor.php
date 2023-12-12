<?php

namespace PhpOffice\PhpSpreadsheet\Chart;

class ChartColor
{
    const EXCEL_COLOR_TYPE_STANDARD = 'prstClr';
    const EXCEL_COLOR_TYPE_SCHEME = 'schemeClr';
    const EXCEL_COLOR_TYPE_RGB = 'srgbClr';
    /** @deprecated 1.24 use EXCEL_COLOR_TYPE_RGB instead */
    const EXCEL_COLOR_TYPE_ARGB = 'srgbClr';
    const EXCEL_COLOR_TYPES = [
        self::EXCEL_COLOR_TYPE_ARGB,
        self::EXCEL_COLOR_TYPE_SCHEME,
        self::EXCEL_COLOR_TYPE_STANDARD,
    ];

    private string $value = '';

    private string $type = '';

    private ?int $alpha = null;

    private ?int $brightness = null;

    /**
     * @param string|string[] $value
     */
    public function __construct($value = '', ?int $alpha = null, ?string $type = null, ?int $brightness = null)
    {
        if (is_array($value)) {
            $this->setColorPropertiesArray($value);
        } else {
            $this->setColorProperties($value, $alpha, $type, $brightness);
        }
    }

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

    public function getBrightness(): ?int
    {
        return $this->brightness;
    }

    public function setBrightness(?int $brightness): self
    {
        $this->brightness = $brightness;

        return $this;
    }

    /**
     * @param null|float|int|string $alpha
     * @param null|float|int|string $brightness
     */
    public function setColorProperties(?string $color, $alpha = null, ?string $type = null, $brightness = null): self
    {
        if (empty($type) && !empty($color)) {
            if (str_starts_with($color, '*')) {
                $type = 'schemeClr';
                $color = substr($color, 1);
            } elseif (str_starts_with($color, '/')) {
                $type = 'prstClr';
                $color = substr($color, 1);
            } elseif (preg_match('/^[0-9A-Fa-f]{6}$/', $color) === 1) {
                $type = 'srgbClr';
            }
        }
        if ($color !== null) {
            $this->setValue("$color");
        }
        if ($type !== null) {
            $this->setType($type);
        }
        if ($alpha === null) {
            $this->setAlpha(null);
        } elseif (is_numeric($alpha)) {
            $this->setAlpha((int) $alpha);
        }
        if ($brightness === null) {
            $this->setBrightness(null);
        } elseif (is_numeric($brightness)) {
            $this->setBrightness((int) $brightness);
        }

        return $this;
    }

    public function setColorPropertiesArray(array $color): self
    {
        return $this->setColorProperties(
            $color['value'] ?? '',
            $color['alpha'] ?? null,
            $color['type'] ?? null,
            $color['brightness'] ?? null
        );
    }

    public function isUsable(): bool
    {
        return $this->type !== '' && $this->value !== '';
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
        } elseif ($propertyName === 'brightness') {
            $retVal = $this->brightness;
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
