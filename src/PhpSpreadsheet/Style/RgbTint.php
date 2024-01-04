<?php

namespace PhpOffice\PhpSpreadsheet\Style;

/**
 * Class to handle tint applied to color.
 * Code borrows heavily from some Python projects.
 *
 * @see https://docs.python.org/3/library/colorsys.html
 * @see https://gist.github.com/Mike-Honey/b36e651e9a7f1d2e1d60ce1c63b9b633
 */
class RgbTint
{
    private const ONE_THIRD = 1.0 / 3.0;
    private const ONE_SIXTH = 1.0 / 6.0;
    private const TWO_THIRD = 2.0 / 3.0;
    private const RGBMAX = 255.0;
    /**
     * MS excel's tint function expects that HLS is base 240.
     *
     * @see https://social.msdn.microsoft.com/Forums/en-US/e9d8c136-6d62-4098-9b1b-dac786149f43/excel-color-tint-algorithm-incorrect?forum=os_binaryfile#d3c2ac95-52e0-476b-86f1-e2a697f24969
     */
    private const HLSMAX = 240.0;

    /**
     * Convert red/green/blue to hue/luminance/saturation.
     *
     * @param float $red 0.0 through 1.0
     * @param float $green 0.0 through 1.0
     * @param float $blue 0.0 through 1.0
     *
     * @return float[]
     */
    private static function rgbToHls(float $red, float $green, float $blue): array
    {
        $maxc = max($red, $green, $blue);
        $minc = min($red, $green, $blue);
        $luminance = ($minc + $maxc) / 2.0;
        if ($minc === $maxc) {
            return [0.0, $luminance, 0.0];
        }
        $maxMinusMin = $maxc - $minc;
        if ($luminance <= 0.5) {
            $s = $maxMinusMin / ($maxc + $minc);
        } else {
            $s = $maxMinusMin / (2.0 - $maxc - $minc);
        }
        $rc = ($maxc - $red) / $maxMinusMin;
        $gc = ($maxc - $green) / $maxMinusMin;
        $bc = ($maxc - $blue) / $maxMinusMin;
        if ($red === $maxc) {
            $h = $bc - $gc;
        } elseif ($green === $maxc) {
            $h = 2.0 + $rc - $bc;
        } else {
            $h = 4.0 + $gc - $rc;
        }
        $h = self::positiveDecimalPart($h / 6.0);

        return [$h, $luminance, $s];
    }

    /**
     * Convert hue/luminance/saturation to red/green/blue.
     *
     * @param float $hue 0.0 through 1.0
     * @param float $luminance 0.0 through 1.0
     * @param float $saturation 0.0 through 1.0
     *
     * @return float[]
     */
    private static function hlsToRgb(float $hue, float $luminance, float $saturation): array
    {
        if ($saturation === 0.0) {
            return [$luminance, $luminance, $luminance];
        }
        if ($luminance <= 0.5) {
            $m2 = $luminance * (1.0 + $saturation);
        } else {
            $m2 = $luminance + $saturation - ($luminance * $saturation);
        }
        $m1 = 2.0 * $luminance - $m2;

        return [
            self::vFunction($m1, $m2, $hue + self::ONE_THIRD),
            self::vFunction($m1, $m2, $hue),
            self::vFunction($m1, $m2, $hue - self::ONE_THIRD),
        ];
    }

    private static function vFunction(float $m1, float $m2, float $hue): float
    {
        $hue = self::positiveDecimalPart($hue);
        if ($hue < self::ONE_SIXTH) {
            return $m1 + ($m2 - $m1) * $hue * 6.0;
        }
        if ($hue < 0.5) {
            return $m2;
        }
        if ($hue < self::TWO_THIRD) {
            return $m1 + ($m2 - $m1) * (self::TWO_THIRD - $hue) * 6.0;
        }

        return $m1;
    }

    private static function positiveDecimalPart(float $hue): float
    {
        $hue = fmod($hue, 1.0);

        return ($hue >= 0.0) ? $hue : (1.0 + $hue);
    }

    /**
     * Convert red/green/blue to HLSMAX-based hue/luminance/saturation.
     *
     * @return int[]
     */
    private static function rgbToMsHls(int $red, int $green, int $blue): array
    {
        $red01 = $red / self::RGBMAX;
        $green01 = $green / self::RGBMAX;
        $blue01 = $blue / self::RGBMAX;
        [$hue, $luminance, $saturation] = self::rgbToHls($red01, $green01, $blue01);

        return [
            (int) round($hue * self::HLSMAX),
            (int) round($luminance * self::HLSMAX),
            (int) round($saturation * self::HLSMAX),
        ];
    }

    /**
     * Converts HLSMAX based HLS values to rgb values in the range (0,1).
     *
     * @return float[]
     */
    private static function msHlsToRgb(int $hue, int $lightness, int $saturation): array
    {
        return self::hlsToRgb($hue / self::HLSMAX, $lightness / self::HLSMAX, $saturation / self::HLSMAX);
    }

    /**
     * Tints HLSMAX based luminance.
     *
     * @see http://ciintelligence.blogspot.co.uk/2012/02/converting-excel-theme-color-and-tint.html
     */
    private static function tintLuminance(float $tint, float $luminance): int
    {
        if ($tint < 0) {
            return (int) round($luminance * (1.0 + $tint));
        }

        return (int) round($luminance * (1.0 - $tint) + (self::HLSMAX - self::HLSMAX * (1.0 - $tint)));
    }

    /**
     * Return result of tinting supplied rgb as 6 hex digits.
     */
    public static function rgbAndTintToRgb(int $red, int $green, int $blue, float $tint): string
    {
        [$hue, $luminance, $saturation] = self::rgbToMsHls($red, $green, $blue);
        [$red, $green, $blue] = self::msHlsToRgb($hue, self::tintLuminance($tint, $luminance), $saturation);

        return sprintf(
            '%02X%02X%02X',
            (int) round($red * self::RGBMAX),
            (int) round($green * self::RGBMAX),
            (int) round($blue * self::RGBMAX)
        );
    }
}
