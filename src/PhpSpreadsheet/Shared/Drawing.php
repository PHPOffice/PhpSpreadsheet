<?php

namespace PhpOffice\PhpSpreadsheet\Shared;

use SimpleXMLElement;

class Drawing
{
    /**
     * Convert pixels to EMU.
     *
     * @param int $pixelValue Value in pixels
     *
     * @return float|int Value in EMU
     */
    public static function pixelsToEMU(int $pixelValue): int|float
    {
        return $pixelValue * 9525;
    }

    /**
     * Convert EMU to pixels.
     *
     * @param int|SimpleXMLElement $emuValue Value in EMU
     *
     * @return int Value in pixels
     */
    public static function EMUToPixels($emuValue): int
    {
        $emuValue = (int) $emuValue;
        if ($emuValue != 0) {
            return (int) round($emuValue / 9525);
        }

        return 0;
    }

    /**
     * Convert pixels to column width. Exact algorithm not known.
     * By inspection of a real Excel file using Calibri 11, one finds 1000px ~ 142.85546875
     * This gives a conversion factor of 7. Also, we assume that pixels and font size are proportional.
     *
     * @param int $pixelValue Value in pixels
     *
     * @return float|int Value in cell dimension
     */
    public static function pixelsToCellDimension(int $pixelValue, \PhpOffice\PhpSpreadsheet\Style\Font $defaultFont): int|float
    {
        // Font name and size
        $name = $defaultFont->getName();
        $size = $defaultFont->getSize();

        if (isset(Font::DEFAULT_COLUMN_WIDTHS[$name][$size])) {
            // Exact width can be determined
            return $pixelValue * Font::DEFAULT_COLUMN_WIDTHS[$name][$size]['width']
                / Font::DEFAULT_COLUMN_WIDTHS[$name][$size]['px'];
        }

        // We don't have data for this particular font and size, use approximation by
        // extrapolating from Calibri 11
        return $pixelValue * 11 * Font::DEFAULT_COLUMN_WIDTHS['Calibri'][11]['width']
            / Font::DEFAULT_COLUMN_WIDTHS['Calibri'][11]['px'] / $size;
    }

    /**
     * Convert column width from (intrinsic) Excel units to pixels.
     *
     * @param float $cellWidth Value in cell dimension
     * @param \PhpOffice\PhpSpreadsheet\Style\Font $defaultFont Default font of the workbook
     *
     * @return int Value in pixels
     */
    public static function cellDimensionToPixels(float $cellWidth, \PhpOffice\PhpSpreadsheet\Style\Font $defaultFont): int
    {
        // Font name and size
        $name = $defaultFont->getName();
        $size = $defaultFont->getSize();

        if (isset(Font::DEFAULT_COLUMN_WIDTHS[$name][$size])) {
            // Exact width can be determined
            $colWidth = $cellWidth * Font::DEFAULT_COLUMN_WIDTHS[$name][$size]['px']
                / Font::DEFAULT_COLUMN_WIDTHS[$name][$size]['width'];
        } else {
            // We don't have data for this particular font and size, use approximation by
            // extrapolating from Calibri 11
            $colWidth = $cellWidth * $size * Font::DEFAULT_COLUMN_WIDTHS['Calibri'][11]['px']
                / Font::DEFAULT_COLUMN_WIDTHS['Calibri'][11]['width'] / 11;
        }

        // Round pixels to closest integer
        $colWidth = (int) round($colWidth);

        return $colWidth;
    }

    /**
     * Convert pixels to points.
     *
     * @param int $pixelValue Value in pixels
     *
     * @return float Value in points
     */
    public static function pixelsToPoints(int $pixelValue): float
    {
        return $pixelValue * 0.75;
    }

    /**
     * Convert points to pixels.
     *
     * @param float|int $pointValue Value in points
     *
     * @return int Value in pixels
     */
    public static function pointsToPixels($pointValue): int
    {
        if ($pointValue != 0) {
            return (int) ceil($pointValue / 0.75);
        }

        return 0;
    }

    /**
     * Convert degrees to angle.
     *
     * @param int $degrees Degrees
     *
     * @return int Angle
     */
    public static function degreesToAngle(int $degrees): int
    {
        return (int) round($degrees * 60000);
    }

    /**
     * Convert angle to degrees.
     *
     * @param int|SimpleXMLElement $angle Angle
     *
     * @return int Degrees
     */
    public static function angleToDegrees($angle): int
    {
        $angle = (int) $angle;
        if ($angle != 0) {
            return (int) round($angle / 60000);
        }

        return 0;
    }
}
