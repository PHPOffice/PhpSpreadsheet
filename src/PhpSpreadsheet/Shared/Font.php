<?php

namespace PhpOffice\PhpSpreadsheet\Shared;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font as FontStyle;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Font
{
    // Methods for resolving autosize value
    const AUTOSIZE_METHOD_APPROX = 'approx';
    const AUTOSIZE_METHOD_EXACT = 'exact';

    private const AUTOSIZE_METHODS = [
        self::AUTOSIZE_METHOD_APPROX,
        self::AUTOSIZE_METHOD_EXACT,
    ];

    /** Character set codes used by BIFF5-8 in Font records */
    const CHARSET_ANSI_LATIN = 0x00;
    const CHARSET_SYSTEM_DEFAULT = 0x01;
    const CHARSET_SYMBOL = 0x02;
    const CHARSET_APPLE_ROMAN = 0x4D;
    const CHARSET_ANSI_JAPANESE_SHIFTJIS = 0x80;
    const CHARSET_ANSI_KOREAN_HANGUL = 0x81;
    const CHARSET_ANSI_KOREAN_JOHAB = 0x82;
    const CHARSET_ANSI_CHINESE_SIMIPLIFIED = 0x86; //    gb2312
    const CHARSET_ANSI_CHINESE_TRADITIONAL = 0x88; //    big5
    const CHARSET_ANSI_GREEK = 0xA1;
    const CHARSET_ANSI_TURKISH = 0xA2;
    const CHARSET_ANSI_VIETNAMESE = 0xA3;
    const CHARSET_ANSI_HEBREW = 0xB1;
    const CHARSET_ANSI_ARABIC = 0xB2;
    const CHARSET_ANSI_BALTIC = 0xBA;
    const CHARSET_ANSI_CYRILLIC = 0xCC;
    const CHARSET_ANSI_THAI = 0xDD;
    const CHARSET_ANSI_LATIN_II = 0xEE;
    const CHARSET_OEM_LATIN_I = 0xFF;

    //  XXX: Constants created!
    /** Font filenames */
    const ARIAL = 'arial.ttf';
    const ARIAL_BOLD = 'arialbd.ttf';
    const ARIAL_ITALIC = 'ariali.ttf';
    const ARIAL_BOLD_ITALIC = 'arialbi.ttf';

    const CALIBRI = 'calibri.ttf';
    const CALIBRI_BOLD = 'calibrib.ttf';
    const CALIBRI_ITALIC = 'calibrii.ttf';
    const CALIBRI_BOLD_ITALIC = 'calibriz.ttf';

    const COMIC_SANS_MS = 'comic.ttf';
    const COMIC_SANS_MS_BOLD = 'comicbd.ttf';

    const COURIER_NEW = 'cour.ttf';
    const COURIER_NEW_BOLD = 'courbd.ttf';
    const COURIER_NEW_ITALIC = 'couri.ttf';
    const COURIER_NEW_BOLD_ITALIC = 'courbi.ttf';

    const GEORGIA = 'georgia.ttf';
    const GEORGIA_BOLD = 'georgiab.ttf';
    const GEORGIA_ITALIC = 'georgiai.ttf';
    const GEORGIA_BOLD_ITALIC = 'georgiaz.ttf';

    const IMPACT = 'impact.ttf';

    const LIBERATION_SANS = 'LiberationSans-Regular.ttf';
    const LIBERATION_SANS_BOLD = 'LiberationSans-Bold.ttf';
    const LIBERATION_SANS_ITALIC = 'LiberationSans-Italic.ttf';
    const LIBERATION_SANS_BOLD_ITALIC = 'LiberationSans-BoldItalic.ttf';

    const LUCIDA_CONSOLE = 'lucon.ttf';
    const LUCIDA_SANS_UNICODE = 'l_10646.ttf';

    const MICROSOFT_SANS_SERIF = 'micross.ttf';

    const PALATINO_LINOTYPE = 'pala.ttf';
    const PALATINO_LINOTYPE_BOLD = 'palab.ttf';
    const PALATINO_LINOTYPE_ITALIC = 'palai.ttf';
    const PALATINO_LINOTYPE_BOLD_ITALIC = 'palabi.ttf';

    const SYMBOL = 'symbol.ttf';

    const TAHOMA = 'tahoma.ttf';
    const TAHOMA_BOLD = 'tahomabd.ttf';

    const TIMES_NEW_ROMAN = 'times.ttf';
    const TIMES_NEW_ROMAN_BOLD = 'timesbd.ttf';
    const TIMES_NEW_ROMAN_ITALIC = 'timesi.ttf';
    const TIMES_NEW_ROMAN_BOLD_ITALIC = 'timesbi.ttf';

    const TREBUCHET_MS = 'trebuc.ttf';
    const TREBUCHET_MS_BOLD = 'trebucbd.ttf';
    const TREBUCHET_MS_ITALIC = 'trebucit.ttf';
    const TREBUCHET_MS_BOLD_ITALIC = 'trebucbi.ttf';

    const VERDANA = 'verdana.ttf';
    const VERDANA_BOLD = 'verdanab.ttf';
    const VERDANA_ITALIC = 'verdanai.ttf';
    const VERDANA_BOLD_ITALIC = 'verdanaz.ttf';

    const FONT_FILE_NAMES = [
        'Arial' => [
            'x' => self::ARIAL,
            'xb' => self::ARIAL_BOLD,
            'xi' => self::ARIAL_ITALIC,
            'xbi' => self::ARIAL_BOLD_ITALIC,
        ],
        'Calibri' => [
            'x' => self::CALIBRI,
            'xb' => self::CALIBRI_BOLD,
            'xi' => self::CALIBRI_ITALIC,
            'xbi' => self::CALIBRI_BOLD_ITALIC,
        ],
        'Comic Sans MS' => [
            'x' => self::COMIC_SANS_MS,
            'xb' => self::COMIC_SANS_MS_BOLD,
            'xi' => self::COMIC_SANS_MS,
            'xbi' => self::COMIC_SANS_MS_BOLD,
        ],
        'Courier New' => [
            'x' => self::COURIER_NEW,
            'xb' => self::COURIER_NEW_BOLD,
            'xi' => self::COURIER_NEW_ITALIC,
            'xbi' => self::COURIER_NEW_BOLD_ITALIC,
        ],
        'Georgia' => [
            'x' => self::GEORGIA,
            'xb' => self::GEORGIA_BOLD,
            'xi' => self::GEORGIA_ITALIC,
            'xbi' => self::GEORGIA_BOLD_ITALIC,
        ],
        'Impact' => [
            'x' => self::IMPACT,
            'xb' => self::IMPACT,
            'xi' => self::IMPACT,
            'xbi' => self::IMPACT,
        ],
        'Liberation Sans' => [
            'x' => self::LIBERATION_SANS,
            'xb' => self::LIBERATION_SANS_BOLD,
            'xi' => self::LIBERATION_SANS_ITALIC,
            'xbi' => self::LIBERATION_SANS_BOLD_ITALIC,
        ],
        'Lucida Console' => [
            'x' => self::LUCIDA_CONSOLE,
            'xb' => self::LUCIDA_CONSOLE,
            'xi' => self::LUCIDA_CONSOLE,
            'xbi' => self::LUCIDA_CONSOLE,
        ],
        'Lucida Sans Unicode' => [
            'x' => self::LUCIDA_SANS_UNICODE,
            'xb' => self::LUCIDA_SANS_UNICODE,
            'xi' => self::LUCIDA_SANS_UNICODE,
            'xbi' => self::LUCIDA_SANS_UNICODE,
        ],
        'Microsoft Sans Serif' => [
            'x' => self::MICROSOFT_SANS_SERIF,
            'xb' => self::MICROSOFT_SANS_SERIF,
            'xi' => self::MICROSOFT_SANS_SERIF,
            'xbi' => self::MICROSOFT_SANS_SERIF,
        ],
        'Palatino Linotype' => [
            'x' => self::PALATINO_LINOTYPE,
            'xb' => self::PALATINO_LINOTYPE_BOLD,
            'xi' => self::PALATINO_LINOTYPE_ITALIC,
            'xbi' => self::PALATINO_LINOTYPE_BOLD_ITALIC,
        ],
        'Symbol' => [
            'x' => self::SYMBOL,
            'xb' => self::SYMBOL,
            'xi' => self::SYMBOL,
            'xbi' => self::SYMBOL,
        ],
        'Tahoma' => [
            'x' => self::TAHOMA,
            'xb' => self::TAHOMA_BOLD,
            'xi' => self::TAHOMA,
            'xbi' => self::TAHOMA_BOLD,
        ],
        'Times New Roman' => [
            'x' => self::TIMES_NEW_ROMAN,
            'xb' => self::TIMES_NEW_ROMAN_BOLD,
            'xi' => self::TIMES_NEW_ROMAN_ITALIC,
            'xbi' => self::TIMES_NEW_ROMAN_BOLD_ITALIC,
        ],
        'Trebuchet MS' => [
            'x' => self::TREBUCHET_MS,
            'xb' => self::TREBUCHET_MS_BOLD,
            'xi' => self::TREBUCHET_MS_ITALIC,
            'xbi' => self::TREBUCHET_MS_BOLD_ITALIC,
        ],
        'Verdana' => [
            'x' => self::VERDANA,
            'xb' => self::VERDANA_BOLD,
            'xi' => self::VERDANA_ITALIC,
            'xbi' => self::VERDANA_BOLD_ITALIC,
        ],
    ];

    /**
     * Array that can be used to supplement FONT_FILE_NAMES for calculating exact width.
     *
     * @var array<string, array<string, string>>
     */
    private static array $extraFontArray = [];

    /** @param array<string, array<string, string>> $extraFontArray */
    public static function setExtraFontArray(array $extraFontArray): void
    {
        self::$extraFontArray = $extraFontArray;
    }

    /** @return array<string, array<string, string>> */
    public static function getExtraFontArray(): array
    {
        return self::$extraFontArray;
    }

    /**
     * AutoSize method.
     */
    private static string $autoSizeMethod = self::AUTOSIZE_METHOD_APPROX;

    /**
     * Path to folder containing TrueType font .ttf files.
     */
    private static string $trueTypeFontPath = '';

    /**
     * How wide is a default column for a given default font and size?
     * Empirical data found by inspecting real Excel files and reading off the pixel width
     * in Microsoft Office Excel 2007.
     * Added height in points.
     */
    public const DEFAULT_COLUMN_WIDTHS = [
        'Arial' => [
            1 => ['px' => 24, 'width' => 12.00000000, 'height' => 5.25],
            2 => ['px' => 24, 'width' => 12.00000000, 'height' => 5.25],
            3 => ['px' => 32, 'width' => 10.66406250, 'height' => 6.0],

            4 => ['px' => 32, 'width' => 10.66406250, 'height' => 6.75],
            5 => ['px' => 40, 'width' => 10.00000000, 'height' => 8.25],
            6 => ['px' => 48, 'width' => 9.59765625, 'height' => 8.25],
            7 => ['px' => 48, 'width' => 9.59765625, 'height' => 9.0],
            8 => ['px' => 56, 'width' => 9.33203125, 'height' => 11.25],
            9 => ['px' => 64, 'width' => 9.14062500, 'height' => 12.0],
            10 => ['px' => 64, 'width' => 9.14062500, 'height' => 12.75],
        ],
        'Calibri' => [
            1 => ['px' => 24, 'width' => 12.00000000, 'height' => 5.25],
            2 => ['px' => 24, 'width' => 12.00000000, 'height' => 5.25],
            3 => ['px' => 32, 'width' => 10.66406250, 'height' => 6.00],
            4 => ['px' => 32, 'width' => 10.66406250, 'height' => 6.75],
            5 => ['px' => 40, 'width' => 10.00000000, 'height' => 8.25],
            6 => ['px' => 48, 'width' => 9.59765625, 'height' => 8.25],
            7 => ['px' => 48, 'width' => 9.59765625, 'height' => 9.0],
            8 => ['px' => 56, 'width' => 9.33203125, 'height' => 11.25],
            9 => ['px' => 56, 'width' => 9.33203125, 'height' => 12.0],
            10 => ['px' => 64, 'width' => 9.14062500, 'height' => 12.75],
            11 => ['px' => 64, 'width' => 9.14062500, 'height' => 15.0],
        ],
        'Verdana' => [
            1 => ['px' => 24, 'width' => 12.00000000, 'height' => 5.25],
            2 => ['px' => 24, 'width' => 12.00000000, 'height' => 5.25],
            3 => ['px' => 32, 'width' => 10.66406250, 'height' => 6.0],
            4 => ['px' => 32, 'width' => 10.66406250, 'height' => 6.75],
            5 => ['px' => 40, 'width' => 10.00000000, 'height' => 8.25],
            6 => ['px' => 48, 'width' => 9.59765625, 'height' => 8.25],
            7 => ['px' => 48, 'width' => 9.59765625, 'height' => 9.0],
            8 => ['px' => 64, 'width' => 9.14062500, 'height' => 10.5],
            9 => ['px' => 72, 'width' => 9.00000000, 'height' => 11.25],
            10 => ['px' => 72, 'width' => 9.00000000, 'height' => 12.75],
        ],
    ];

    /**
     * Set autoSize method.
     *
     * @param string $method see self::AUTOSIZE_METHOD_*
     *
     * @return bool Success or failure
     */
    public static function setAutoSizeMethod(string $method): bool
    {
        if (!in_array($method, self::AUTOSIZE_METHODS)) {
            return false;
        }
        self::$autoSizeMethod = $method;

        return true;
    }

    /**
     * Get autoSize method.
     */
    public static function getAutoSizeMethod(): string
    {
        return self::$autoSizeMethod;
    }

    /**
     * Set the path to the folder containing .ttf files. There should be a trailing slash.
     * Path will be recursively searched for font file.
     * Typical locations on various platforms:
     *    <ul>
     *        <li>C:/Windows/Fonts/</li>
     *        <li>/usr/share/fonts/truetype/</li>
     *        <li>~/.fonts/</li>
     * </ul>.
     */
    public static function setTrueTypeFontPath(string $folderPath): void
    {
        self::$trueTypeFontPath = $folderPath;
    }

    /**
     * Get the path to the folder containing .ttf files.
     */
    public static function getTrueTypeFontPath(): string
    {
        return self::$trueTypeFontPath;
    }

    /**
     * Pad amount for exact in pixels; use best guess if null.
     */
    private static null|float|int $paddingAmountExact = null;

    /**
     * Set pad amount for exact in pixels; use best guess if null.
     */
    public static function setPaddingAmountExact(null|float|int $paddingAmountExact): void
    {
        self::$paddingAmountExact = $paddingAmountExact;
    }

    /**
     * Get pad amount for exact in pixels; or null if using best guess.
     */
    public static function getPaddingAmountExact(): null|float|int
    {
        return self::$paddingAmountExact;
    }

    /**
     * Calculate an (approximate) OpenXML column width, based on font size and text contained.
     *
     * @param FontStyle $font Font object
     * @param null|RichText|string $cellText Text to calculate width
     * @param int $rotation Rotation angle
     * @param null|FontStyle $defaultFont Font object
     * @param bool $filterAdjustment Add space for Autofilter or Table dropdown
     */
    public static function calculateColumnWidth(
        FontStyle $font,
        $cellText = '',
        int $rotation = 0,
        ?FontStyle $defaultFont = null,
        bool $filterAdjustment = false,
        int $indentAdjustment = 0
    ): float {
        // If it is rich text, use plain text
        if ($cellText instanceof RichText) {
            $cellText = $cellText->getPlainText();
        }

        // Special case if there are one or more newline characters ("\n")
        $cellText = (string) $cellText;
        if (str_contains($cellText, "\n")) {
            $lineTexts = explode("\n", $cellText);
            $lineWidths = [];
            foreach ($lineTexts as $lineText) {
                $lineWidths[] = self::calculateColumnWidth($font, $lineText, $rotation = 0, $defaultFont, $filterAdjustment);
            }

            return max($lineWidths); // width of longest line in cell
        }

        // Try to get the exact text width in pixels
        $approximate = self::$autoSizeMethod === self::AUTOSIZE_METHOD_APPROX;
        $columnWidth = 0;
        if (!$approximate) {
            try {
                $columnWidthAdjust = ceil(
                    self::getTextWidthPixelsExact(
                        str_repeat('n', 1 * (($filterAdjustment ? 3 : 1) + ($indentAdjustment * 2))),
                        $font,
                        0
                    ) * 1.07
                );

                // Width of text in pixels excl. padding
                // and addition because Excel adds some padding, just use approx width of 'n' glyph
                $columnWidth = self::getTextWidthPixelsExact($cellText, $font, $rotation) + (self::$paddingAmountExact ?? $columnWidthAdjust);
            } catch (PhpSpreadsheetException) {
                $approximate = true;
            }
        }

        if ($approximate) {
            $columnWidthAdjust = self::getTextWidthPixelsApprox(
                str_repeat('n', 1 * (($filterAdjustment ? 3 : 1) + ($indentAdjustment * 2))),
                $font,
                0
            );
            // Width of text in pixels excl. padding, approximation
            // and addition because Excel adds some padding, just use approx width of 'n' glyph
            $columnWidth = self::getTextWidthPixelsApprox($cellText, $font, $rotation) + $columnWidthAdjust;
        }

        // Convert from pixel width to column width
        $columnWidth = Drawing::pixelsToCellDimension((int) $columnWidth, $defaultFont ?? new FontStyle());

        // Return
        return round($columnWidth, 4);
    }

    /**
     * Get GD text width in pixels for a string of text in a certain font at a certain rotation angle.
     */
    public static function getTextWidthPixelsExact(string $text, FontStyle $font, int $rotation = 0): float
    {
        // font size should really be supplied in pixels in GD2,
        // but since GD2 seems to assume 72dpi, pixels and points are the same
        $fontFile = self::getTrueTypeFontFileFromFont($font);
        $textBox = imagettfbbox($font->getSize() ?? 10.0, $rotation, $fontFile, $text);
        if ($textBox === false) {
            // @codeCoverageIgnoreStart
            throw new PhpSpreadsheetException('imagettfbbox failed');
            // @codeCoverageIgnoreEnd
        }

        // Get corners positions
        /** @var int[] $textBox */
        $lowerLeftCornerX = $textBox[0];
        $lowerRightCornerX = $textBox[2];
        $upperRightCornerX = $textBox[4];
        $upperLeftCornerX = $textBox[6];

        // Consider the rotation when calculating the width
        return round(max($lowerRightCornerX - $upperLeftCornerX, $upperRightCornerX - $lowerLeftCornerX), 4);
    }

    /**
     * Get approximate width in pixels for a string of text in a certain font at a certain rotation angle.
     *
     * @return int Text width in pixels (no padding added)
     */
    public static function getTextWidthPixelsApprox(string $columnText, FontStyle $font, int $rotation = 0): int
    {
        $fontName = $font->getName();
        $fontSize = $font->getSize();

        // Calculate column width in pixels.
        // We assume fixed glyph width, but count double for "fullwidth" characters.
        // Result varies with font name and size.
        switch ($fontName) {
            case 'Arial':
                // value 8 was set because of experience in different exports at Arial 10 font.
                $columnWidth = (int) (8 * StringHelper::countCharactersDbcs($columnText));
                $columnWidth = $columnWidth * $fontSize / 10; // extrapolate from font size

                break;
            case 'Verdana':
                // value 8 was found via interpolation by inspecting real Excel files with Verdana 10 font.
                $columnWidth = (int) (8 * StringHelper::countCharactersDbcs($columnText));
                $columnWidth = $columnWidth * $fontSize / 10; // extrapolate from font size

                break;
            default:
                // just assume Calibri
                // value 8.26 was found via interpolation by inspecting real Excel files with Calibri 11 font.
                $columnWidth = (int) (8.26 * StringHelper::countCharactersDbcs($columnText));
                $columnWidth = $columnWidth * $fontSize / 11; // extrapolate from font size

                break;
        }

        // Calculate approximate rotated column width
        if ($rotation !== 0) {
            if ($rotation == Alignment::TEXTROTATION_STACK_PHPSPREADSHEET) {
                // stacked text
                $columnWidth = 4; // approximation
            } else {
                // rotated text
                $columnWidth = $columnWidth * cos(deg2rad($rotation))
                                + $fontSize * abs(sin(deg2rad($rotation))) / 5; // approximation
            }
        }

        // pixel width is an integer
        return (int) $columnWidth;
    }

    /**
     * Calculate an (approximate) pixel size, based on a font points size.
     *
     * @param float|int $fontSizeInPoints Font size (in points)
     *
     * @return int Font size (in pixels)
     */
    public static function fontSizeToPixels(float|int $fontSizeInPoints): int
    {
        return (int) ((4 / 3) * $fontSizeInPoints);
    }

    /**
     * Calculate an (approximate) pixel size, based on inch size.
     *
     * @param float|int $sizeInInch Font size (in inch)
     *
     * @return float|int Size (in pixels)
     */
    public static function inchSizeToPixels(int|float $sizeInInch): int|float
    {
        return $sizeInInch * 96;
    }

    /**
     * Calculate an (approximate) pixel size, based on centimeter size.
     *
     * @param float|int $sizeInCm Font size (in centimeters)
     *
     * @return float Size (in pixels)
     */
    public static function centimeterSizeToPixels(int|float $sizeInCm): float
    {
        return $sizeInCm * 37.795275591;
    }

    /**
     * Returns the font path given the font.
     *
     * @return string Path to TrueType font file
     */
    public static function getTrueTypeFontFileFromFont(FontStyle $font, bool $checkPath = true): string
    {
        if ($checkPath && (!file_exists(self::$trueTypeFontPath) || !is_dir(self::$trueTypeFontPath))) {
            throw new PhpSpreadsheetException('Valid directory to TrueType Font files not specified');
        }

        $name = $font->getName();
        $fontArray = array_merge(self::FONT_FILE_NAMES, self::$extraFontArray);
        if (!isset($fontArray[$name])) {
            throw new PhpSpreadsheetException('Unknown font name "' . $name . '". Cannot map to TrueType font file');
        }
        $bold = $font->getBold();
        $italic = $font->getItalic();
        $index = 'x';
        if ($bold) {
            $index .= 'b';
        }
        if ($italic) {
            $index .= 'i';
        }
        $fontFile = $fontArray[$name][$index];

        $separator = '';
        if (mb_strlen(self::$trueTypeFontPath) > 1 && mb_substr(self::$trueTypeFontPath, -1) !== '/' && mb_substr(self::$trueTypeFontPath, -1) !== '\\') {
            $separator = DIRECTORY_SEPARATOR;
        }
        $fontFileAbsolute = preg_match('~^([A-Za-z]:)?[/\\\]~', $fontFile) === 1;
        if (!$fontFileAbsolute) {
            $fontFile = self::findFontFile(self::$trueTypeFontPath, $fontFile) ?? self::$trueTypeFontPath . $separator . $fontFile;
        }

        // Check if file actually exists
        if ($checkPath && !file_exists($fontFile) && !$fontFileAbsolute) {
            $alternateName = $name;
            if ($index !== 'x' && $fontArray[$name][$index] !== $fontArray[$name]['x']) {
                // Bold but no italic:
                //   Comic Sans
                //   Tahoma
                // Neither bold nor italic:
                //   Impact
                //   Lucida Console
                //   Lucida Sans Unicode
                //   Microsoft Sans Serif
                //   Symbol
                if ($index === 'xb') {
                    $alternateName .= ' Bold';
                } elseif ($index === 'xi') {
                    $alternateName .= ' Italic';
                } elseif ($fontArray[$name]['xb'] === $fontArray[$name]['xbi']) {
                    $alternateName .= ' Bold';
                } else {
                    $alternateName .= ' Bold Italic';
                }
            }
            $fontFile = self::$trueTypeFontPath . $separator . $alternateName . '.ttf';
            if (!file_exists($fontFile)) {
                throw new PhpSpreadsheetException('TrueType Font file not found');
            }
        }

        return $fontFile;
    }

    public const CHARSET_FROM_FONT_NAME = [
        'EucrosiaUPC' => self::CHARSET_ANSI_THAI,
        'Wingdings' => self::CHARSET_SYMBOL,
        'Wingdings 2' => self::CHARSET_SYMBOL,
        'Wingdings 3' => self::CHARSET_SYMBOL,
    ];

    /**
     * Returns the associated charset for the font name.
     *
     * @param string $fontName Font name
     *
     * @return int Character set code
     */
    public static function getCharsetFromFontName(string $fontName): int
    {
        return self::CHARSET_FROM_FONT_NAME[$fontName] ?? self::CHARSET_ANSI_LATIN;
    }

    /**
     * Get the effective column width for columns without a column dimension or column with width -1
     * For example, for Calibri 11 this is 9.140625 (64 px).
     *
     * @param FontStyle $font The workbooks default font
     * @param bool $returnAsPixels true = return column width in pixels, false = return in OOXML units
     *
     * @return ($returnAsPixels is true ? int : float) Column width
     */
    public static function getDefaultColumnWidthByFont(FontStyle $font, bool $returnAsPixels = false): float|int
    {
        $size = $font->getSize();
        $sizex = ($size !== null && $size == (int) $size) ? ((int) $size) : "$size";
        if (isset(self::DEFAULT_COLUMN_WIDTHS[$font->getName()][$sizex])) {
            // Exact width can be determined
            $columnWidth = $returnAsPixels
                ? self::DEFAULT_COLUMN_WIDTHS[$font->getName()][$sizex]['px']
                    : self::DEFAULT_COLUMN_WIDTHS[$font->getName()][$sizex]['width'];
        } else {
            // We don't have data for this particular font and size, use approximation by
            // extrapolating from Calibri 11
            $columnWidth = $returnAsPixels
                ? self::DEFAULT_COLUMN_WIDTHS['Calibri'][11]['px']
                    : self::DEFAULT_COLUMN_WIDTHS['Calibri'][11]['width'];
            $columnWidth = $columnWidth * $font->getSize() / 11;

            // Round pixels to closest integer
            if ($returnAsPixels) {
                $columnWidth = (int) round($columnWidth);
            }
        }

        return $columnWidth;
    }

    /**
     * Get the effective row height for rows without a row dimension or rows with height -1
     * For example, for Calibri 11 this is 15 points.
     *
     * @param FontStyle $font The workbooks default font
     *
     * @return float Row height in points
     */
    public static function getDefaultRowHeightByFont(FontStyle $font): float
    {
        $name = $font->getName();
        $size = $font->getSize();
        $sizex = ($size !== null && $size == (int) $size) ? ((int) $size) : "$size";
        if (isset(self::DEFAULT_COLUMN_WIDTHS[$name][$sizex])) {
            $rowHeight = self::DEFAULT_COLUMN_WIDTHS[$name][$sizex]['height'];
        } elseif ($name === 'Arial' || $name === 'Verdana') {
            $rowHeight = self::DEFAULT_COLUMN_WIDTHS[$name][10]['height'] * $size / 10.0;
        } else {
            $rowHeight = self::DEFAULT_COLUMN_WIDTHS['Calibri'][11]['height'] * $size / 11.0;
        }

        return $rowHeight;
    }

    private static function findFontFile(string $startDirectory, string $desiredFont): ?string
    {
        $fontPath = null;
        if ($startDirectory === '') {
            return null;
        }
        if (file_exists("$startDirectory/$desiredFont")) {
            $fontPath = "$startDirectory/$desiredFont";
        } else {
            $iterations = 0;
            $it = new RecursiveDirectoryIterator(
                $startDirectory,
                RecursiveDirectoryIterator::SKIP_DOTS
                | RecursiveDirectoryIterator::FOLLOW_SYMLINKS
            );
            foreach (
                new RecursiveIteratorIterator(
                    $it,
                    RecursiveIteratorIterator::LEAVES_ONLY,
                    RecursiveIteratorIterator::CATCH_GET_CHILD
                ) as $filex
            ) {
                /** @var string */
                $file = $filex;
                if (basename($file) === $desiredFont) {
                    $fontPath = $file;

                    break;
                }
                ++$iterations;
                if ($iterations > 5000) {
                    // @codeCoverageIgnoreStart
                    break;
                    // @codeCoverageIgnoreEnd
                }
            }
        }

        return $fontPath;
    }
}
