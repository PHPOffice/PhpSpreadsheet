<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Html;

class Dimension
{
    const ABSOLUTE_UNITS = [
        'cm' => 0.003779717610677,
        'mm' => 0.03779717610677,
        'in' => 0.960048273111958,
        'px' => 0.14285546875,
        'pt' => 0.1071416015625,
        'pc' => 0.008928466796875,
    ];

    const RELATIVE_UNITS = [
        'em' => 0.1075433825684,
        'ex' => 0.1075433825684,
        'ch' => 0.1075433825684,
        'rem' => 0.0014285546875,
        'vw' => 0.0014285546875,
        'vh' => 0.0014285546875,
        'vmin' => 0.0014285546875,
        'vmax' => 0.0014285546875,
        '%' => 0.0014285546875,
    ];

    protected $width;
    protected $unit;

    public function __construct(string $dimension)
    {
        [$this->width, $this->unit] = sscanf($dimension, '%[1234567890.]%s');
    }

    public function width(): float
    {
        $width = (float) $this->width;
        if (isset(self::ABSOLUTE_UNITS[$this->unit])) {
            $width *= self::ABSOLUTE_UNITS[$this->unit];
        } elseif (isset(self::RELATIVE_UNITS[$this->unit])) {
            $width *= self::RELATIVE_UNITS[$this->unit];
        }

        return $width;
    }
}
