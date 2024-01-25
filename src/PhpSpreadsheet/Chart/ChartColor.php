<?php

namespace PhpOffice\PhpSpreadsheet\Chart;

class ChartColor
{
    public const EXCEL_COLOR_TYPE_STANDARD = 'prstClr';
    public const EXCEL_COLOR_TYPE_SCHEME = 'schemeClr';
    public const EXCEL_COLOR_TYPE_RGB = 'srgbClr';
    public const EXCEL_COLOR_TYPES = [
        self::EXCEL_COLOR_TYPE_RGB,
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

    public function setColorProperties(?string $color, null|float|int|string $alpha = null, ?string $type = null, null|float|int|string $brightness = null): self
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
     */
    public function getColorProperty(string $propertyName): null|int|string
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

    public static function alphaFromXml(float|int|string $alpha): int
    {
        return 100 - ((int) $alpha / 1000);
    }
}
