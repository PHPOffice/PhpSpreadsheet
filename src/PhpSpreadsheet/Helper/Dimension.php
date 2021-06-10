<?php

namespace PhpOffice\PhpSpreadsheet\Helper;

use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Shared\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Font;

class Dimension
{
    /**
     * Based on 96 dpi.
     */
    const ABSOLUTE_UNITS = [
        'cm' => 96.0 / 2.54,
        'mm' => 96.0 / 25.4,
        'in' => 96.0,
        'px' => 1.0,
        'pt' => 96.0 / 72,
        'pc' => 96.0 * 12 / 72,
    ];

    /**
     * Based on a standard column width of 8.54 units in MS Excel.
     */
    const RELATIVE_UNITS = [
        'em' => 10.0 / 8.54,
        'ex' => 10.0 / 8.54,
        'ch' => 10.0 / 8.54,
        'rem' => 10.0 / 8.54,
        'vw' => 8.54,
        'vh' => 8.54,
        'vmin' => 8.54,
        'vmax' => 8.54,
        '%' => 8.54 / 100,
    ];

    /**
     * @var float|int width in pixels (if is set) or in Excel's column width units if is null
     */
    protected $width;

    /**
     * @var null|string
     */
    protected $unit;

    public function __construct(string $dimension)
    {
        [$width, $unit] = sscanf($dimension, '%[1234567890.]%s');
        $unit = strtolower(trim($unit));

        // If a UoM is specified, then convert the width to pixels for internal storage
        if (isset(self::ABSOLUTE_UNITS[$unit])) {
            $width *= self::ABSOLUTE_UNITS[$unit];
            $this->unit = 'px';
        } elseif (isset(self::RELATIVE_UNITS[$unit])) {
            $width *= self::RELATIVE_UNITS[$unit];
            $width = round($width, 4);
        }

        $this->width = $width;
    }

    public function width(): float
    {
        return (float) ($this->unit === null)
            ? $this->width
            : round(Drawing::pixelsToCellDimension((int) $this->width, new Font(false)), 4);
    }

    public function toUnit(string $unitOfMeasure): float
    {
        $unitOfMeasure = strtolower($unitOfMeasure);
        if (!array_key_exists($unitOfMeasure, self::ABSOLUTE_UNITS)) {
            throw new Exception("{$unitOfMeasure} is not a vaid unit of measure");
        }

        $width = $this->width;
        if ($this->unit === null) {
            $width = Drawing::cellDimensionToPixels($width, new Font(false));
        }

        return $width / self::ABSOLUTE_UNITS[$unitOfMeasure];
    }
}
