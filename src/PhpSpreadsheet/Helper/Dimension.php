<?php

namespace PhpOffice\PhpSpreadsheet\Helper;

use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Shared\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Font;

class Dimension
{
    public const UOM_CENTIMETERS = 'cm';
    public const UOM_MILLIMETERS = 'mm';
    public const UOM_INCHES = 'in';
    public const UOM_PIXELS = 'px';
    public const UOM_POINTS = 'pt';
    public const UOM_PICA = 'pc';

    /**
     * Based on 96 dpi.
     */
    const ABSOLUTE_UNITS = [
        self::UOM_CENTIMETERS => 96.0 / 2.54,
        self::UOM_MILLIMETERS => 96.0 / 25.4,
        self::UOM_INCHES => 96.0,
        self::UOM_PIXELS => 1.0,
        self::UOM_POINTS => 96.0 / 72,
        self::UOM_PICA => 96.0 * 12 / 72,
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
     * @var float|int If this is a width, then size is measured in pixels (if is set)
     *                   or in Excel's default column width units if $unit is null.
     *                If this is a height, then size is measured in pixels ()
     *                   or in points () if $unit is null.
     */
    protected float|int $size;

    protected ?string $unit = null;

    public function __construct(string $dimension)
    {
        $size = 0.0;
        $unit = '';
        $sscanf = sscanf($dimension, '%[1234567890.]%s');
        if (is_array($sscanf)) {
            $size = (float) ($sscanf[0] ?? 0.0);
            $unit = strtolower($sscanf[1] ?? '');
        }

        // If a UoM is specified, then convert the size to pixels for internal storage
        if (isset(self::ABSOLUTE_UNITS[$unit])) {
            $size *= self::ABSOLUTE_UNITS[$unit];
            $this->unit = self::UOM_PIXELS;
        } elseif (isset(self::RELATIVE_UNITS[$unit])) {
            $size *= self::RELATIVE_UNITS[$unit];
            $size = round($size, 4);
        }

        $this->size = $size;
    }

    public function width(): float
    {
        return (float) ($this->unit === null)
            ? $this->size
            : round(Drawing::pixelsToCellDimension((int) $this->size, new Font(false)), 4);
    }

    public function height(): float
    {
        return (float) ($this->unit === null)
            ? $this->size
            : $this->toUnit(self::UOM_POINTS);
    }

    public function toUnit(string $unitOfMeasure): float
    {
        $unitOfMeasure = strtolower($unitOfMeasure);
        if (!array_key_exists($unitOfMeasure, self::ABSOLUTE_UNITS)) {
            throw new Exception("{$unitOfMeasure} is not a vaid unit of measure");
        }

        $size = $this->size;
        if ($this->unit === null) {
            $size = Drawing::cellDimensionToPixels($size, new Font(false));
        }

        return $size / self::ABSOLUTE_UNITS[$unitOfMeasure];
    }
}
