<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Percentiles;
use PhpOffice\PhpSpreadsheet\Style\Color;

class ConditionalColorScale
{
    private ?ConditionalFormatValueObject $minimumConditionalFormatValueObject = null;

    private ?ConditionalFormatValueObject $midpointConditionalFormatValueObject = null;

    private ?ConditionalFormatValueObject $maximumConditionalFormatValueObject = null;

    private ?Color $minimumColor = null;

    private ?Color $midpointColor = null;

    private ?Color $maximumColor = null;

    private ?string $sqref = null;

    private array $valueArray = [];

    private float $minValue = 0;

    private float $maxValue = 0;

    private float $midValue = 0;

    private ?\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet = null;

    public function getMinimumConditionalFormatValueObject(): ?ConditionalFormatValueObject
    {
        return $this->minimumConditionalFormatValueObject;
    }

    public function setMinimumConditionalFormatValueObject(ConditionalFormatValueObject $minimumConditionalFormatValueObject): self
    {
        $this->minimumConditionalFormatValueObject = $minimumConditionalFormatValueObject;

        return $this;
    }

    public function getMidpointConditionalFormatValueObject(): ?ConditionalFormatValueObject
    {
        return $this->midpointConditionalFormatValueObject;
    }

    public function setMidpointConditionalFormatValueObject(ConditionalFormatValueObject $midpointConditionalFormatValueObject): self
    {
        $this->midpointConditionalFormatValueObject = $midpointConditionalFormatValueObject;

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

    public function getMinimumColor(): ?Color
    {
        return $this->minimumColor;
    }

    public function setMinimumColor(Color $minimumColor): self
    {
        $this->minimumColor = $minimumColor;

        return $this;
    }

    public function getMidpointColor(): ?Color
    {
        return $this->midpointColor;
    }

    public function setMidpointColor(Color $midpointColor): self
    {
        $this->midpointColor = $midpointColor;

        return $this;
    }

    public function getMaximumColor(): ?Color
    {
        return $this->maximumColor;
    }

    public function setMaximumColor(Color $maximumColor): self
    {
        $this->maximumColor = $maximumColor;

        return $this;
    }

    public function getSqRef(): ?string
    {
        return $this->sqref;
    }

    public function setSqRef(string $sqref, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet): self
    {
        $this->sqref = $sqref;
        $this->worksheet = $worksheet;

        return $this;
    }

    public function setScaleArray(): self
    {
        if ($this->sqref !== null && $this->worksheet !== null) {
            $values = $this->worksheet->rangesToArray($this->sqref, null, true, true, true);
            $this->valueArray = [];
            foreach ($values as $key => $value) {
                foreach ($value as $k => $v) {
                    $this->valueArray[] = (float) $v;
                }
            }
            $this->prepareColorScale();
        }

        return $this;
    }

    public function getColorForValue(float $value): string
    {
        if ($this->minimumColor === null || $this->midpointColor === null || $this->maximumColor === null) {
            return 'FF000000';
        }
        $minColor = $this->minimumColor->getARGB();
        $midColor = $this->midpointColor->getARGB();
        $maxColor = $this->maximumColor->getARGB();

        if ($minColor === null || $midColor === null || $maxColor === null) {
            return 'FF000000';
        }

        if ($value <= $this->minValue) {
            return $minColor;
        }
        if ($value >= $this->maxValue) {
            return $maxColor;
        }
        if ($value == $this->midValue) {
            return $midColor;
        }
        if ($value < $this->midValue) {
            $blend = ($value - $this->minValue) / ($this->midValue - $this->minValue);
            $alpha1 = hexdec(substr($minColor, 0, 2));
            $alpha2 = hexdec(substr($midColor, 0, 2));
            $red1 = hexdec(substr($minColor, 2, 2));
            $red2 = hexdec(substr($midColor, 2, 2));
            $green1 = hexdec(substr($minColor, 4, 2));
            $green2 = hexdec(substr($midColor, 4, 2));
            $blue1 = hexdec(substr($minColor, 6, 2));
            $blue2 = hexdec(substr($midColor, 6, 2));

            return strtoupper(dechex((int) ($alpha2 * $blend + $alpha1 * (1 - $blend))) . '' . dechex((int) ($red2 * $blend + $red1 * (1 - $blend))) . '' . dechex((int) ($green2 * $blend + $green1 * (1 - $blend))) . '' . dechex((int) ($blue2 * $blend + $blue1 * (1 - $blend))));
        }
        $blend = ($value - $this->midValue) / ($this->maxValue - $this->midValue);
        $alpha1 = hexdec(substr($midColor, 0, 2));
        $alpha2 = hexdec(substr($maxColor, 0, 2));
        $red1 = hexdec(substr($midColor, 2, 2));
        $red2 = hexdec(substr($maxColor, 2, 2));
        $green1 = hexdec(substr($midColor, 4, 2));
        $green2 = hexdec(substr($maxColor, 4, 2));
        $blue1 = hexdec(substr($midColor, 6, 2));
        $blue2 = hexdec(substr($maxColor, 6, 2));

        return strtoupper(dechex((int) ($alpha2 * $blend + $alpha1 * (1 - $blend))) . '' . dechex((int) ($red2 * $blend + $red1 * (1 - $blend))) . '' . dechex((int) ($green2 * $blend + $green1 * (1 - $blend))) . '' . dechex((int) ($blue2 * $blend + $blue1 * (1 - $blend))));
    }

    private function getLimitValue(string $type, float $value = 0, float $formula = 0): float
    {
        if (count($this->valueArray) === 0) {
            return 0;
        }
        switch ($type) {
            case 'min':
                return (float) min($this->valueArray);
            case 'max':
                return (float) max($this->valueArray);
            case 'percentile':
                return (float) Percentiles::PERCENTILE($this->valueArray, (float) ($value / 100));
            case 'formula':
                return $formula;
            case 'percent':
                $min = (float) min($this->valueArray);
                $max = (float) max($this->valueArray);

                return $min + (float) ($value / 100) * ($max - $min);
            default:
                return 0;
        }
    }

    /**
     * Prepares color scale for execution, see the first if for variables that must be set beforehand.
     */
    public function prepareColorScale(): self
    {
        if ($this->minimumConditionalFormatValueObject !== null && $this->maximumConditionalFormatValueObject !== null && $this->minimumColor !== null && $this->maximumColor !== null) {
            if ($this->midpointConditionalFormatValueObject !== null && $this->midpointConditionalFormatValueObject->getType() !== 'None') {
                $this->minValue = $this->getLimitValue($this->minimumConditionalFormatValueObject->getType(), (float) $this->minimumConditionalFormatValueObject->getValue(), (float) $this->minimumConditionalFormatValueObject->getCellFormula());
                $this->midValue = $this->getLimitValue($this->midpointConditionalFormatValueObject->getType(), (float) $this->midpointConditionalFormatValueObject->getValue(), (float) $this->midpointConditionalFormatValueObject->getCellFormula());
                $this->maxValue = $this->getLimitValue($this->maximumConditionalFormatValueObject->getType(), (float) $this->maximumConditionalFormatValueObject->getValue(), (float) $this->maximumConditionalFormatValueObject->getCellFormula());
            } else {
                $this->minValue = $this->getLimitValue($this->minimumConditionalFormatValueObject->getType(), (float) $this->minimumConditionalFormatValueObject->getValue(), (float) $this->minimumConditionalFormatValueObject->getCellFormula());
                $this->maxValue = $this->getLimitValue($this->maximumConditionalFormatValueObject->getType(), (float) $this->maximumConditionalFormatValueObject->getValue(), (float) $this->maximumConditionalFormatValueObject->getCellFormula());
                $this->midValue = (float) ($this->minValue + $this->maxValue) / 2;
                $blend = 0.5;

                $minColor = $this->minimumColor->getARGB();
                $maxColor = $this->maximumColor->getARGB();

                if ($minColor !== null && $maxColor !== null) {
                    $alpha1 = hexdec(substr($minColor, 0, 2));
                    $alpha2 = hexdec(substr($maxColor, 0, 2));
                    $red1 = hexdec(substr($minColor, 2, 2));
                    $red2 = hexdec(substr($maxColor, 2, 2));
                    $green1 = hexdec(substr($minColor, 4, 2));
                    $green2 = hexdec(substr($maxColor, 4, 2));
                    $blue1 = hexdec(substr($minColor, 6, 2));
                    $blue2 = hexdec(substr($maxColor, 6, 2));
                    $this->midpointColor = new Color(strtoupper(dechex((int) ($alpha2 * $blend + $alpha1 * (1 - $blend))) . '' . dechex((int) ($red2 * $blend + $red1 * (1 - $blend))) . '' . dechex((int) ($green2 * $blend + $green1 * (1 - $blend))) . '' . dechex((int) ($blue2 * $blend + $blue1 * (1 - $blend)))));
                } else {
                    $this->midpointColor = null;
                }
            }
        }

        return $this;
    }

    /**
     * Checks that all needed color scale data is in place.
     */
    public function colorScaleReadyForUse(): bool
    {
        if ($this->minimumColor === null || $this->midpointColor === null || $this->maximumColor === null) {
            return false;
        }

        return true;
    }
}
