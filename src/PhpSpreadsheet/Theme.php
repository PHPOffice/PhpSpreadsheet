<?php

namespace PhpOffice\PhpSpreadsheet;

class Theme
{
    private string $themeColorName = 'Office';

    private string $themeFontName = 'Office';

    public const HYPERLINK_THEME = 10;
    public const COLOR_SCHEME_2013_2022_NAME = 'Office 2013-2022';
    public const COLOR_SCHEME_2013_2022 = [
        'dk1' => '000000',
        'lt1' => 'FFFFFF',
        'dk2' => '44546A',
        'lt2' => 'E7E6E6',
        'accent1' => '4472C4',
        'accent2' => 'ED7D31',
        'accent3' => 'A5A5A5',
        'accent4' => 'FFC000',
        'accent5' => '5B9BD5',
        'accent6' => '70AD47',
        'hlink' => '0563C1',
        'folHlink' => '954F72',
    ];
    private const COLOR_SCHEME_2013_PLUS_NAME = 'Office 2013+';

    public const COLOR_SCHEME_2007_2010_NAME = 'Office 2007-2010';
    public const COLOR_SCHEME_2007_2010 = [
        'dk1' => '000000',
        'lt1' => 'FFFFFF',
        'dk2' => '1F497D',
        'lt2' => 'EEECE1',
        'accent1' => '4F81BD',
        'accent2' => 'C0504D',
        'accent3' => '9BBB59',
        'accent4' => '8064A2',
        'accent5' => '4BACC6',
        'accent6' => 'F79646',
        'hlink' => '0000FF',
        'folHlink' => '800080',
    ];

    public const COLOR_SCHEME_2023_PLUS_NAME = 'Office 2023+';
    public const COLOR_SCHEME_2023_PLUS = [
        'dk1' => '000000',
        'lt1' => 'FFFFFF',
        'dk2' => '0E2841',
        'lt2' => 'E8E8E8',
        'accent1' => '156082',
        'accent2' => 'E97132',
        'accent3' => '196B24',
        'accent4' => '0F9ED5',
        'accent5' => 'A02B93',
        'accent6' => '4EA72E',
        'hlink' => '467886',
        'folHlink' => '96607D',
    ];

    /** @var string[] */
    private array $themeColors = self::COLOR_SCHEME_2007_2010;

    private string $majorFontLatin = 'Cambria';

    private string $majorFontEastAsian = '';

    private string $majorFontComplexScript = '';

    private string $minorFontLatin = 'Calibri';

    private string $minorFontEastAsian = '';

    private string $minorFontComplexScript = '';

    /**
     * Map of Major (header) fonts to write.
     *
     * @var string[]
     */
    private array $majorFontSubstitutions = self::FONTS_TIMES_SUBSTITUTIONS;

    /**
     * Map of Minor (body) fonts to write.
     *
     * @var string[]
     */
    private array $minorFontSubstitutions = self::FONTS_ARIAL_SUBSTITUTIONS;

    public const FONTS_TIMES_SUBSTITUTIONS = [
        'Jpan' => 'ＭＳ Ｐゴシック',
        'Hang' => '맑은 고딕',
        'Hans' => '宋体',
        'Hant' => '新細明體',
        'Arab' => 'Times New Roman',
        'Hebr' => 'Times New Roman',
        'Thai' => 'Tahoma',
        'Ethi' => 'Nyala',
        'Beng' => 'Vrinda',
        'Gujr' => 'Shruti',
        'Khmr' => 'MoolBoran',
        'Knda' => 'Tunga',
        'Guru' => 'Raavi',
        'Cans' => 'Euphemia',
        'Cher' => 'Plantagenet Cherokee',
        'Yiii' => 'Microsoft Yi Baiti',
        'Tibt' => 'Microsoft Himalaya',
        'Thaa' => 'MV Boli',
        'Deva' => 'Mangal',
        'Telu' => 'Gautami',
        'Taml' => 'Latha',
        'Syrc' => 'Estrangelo Edessa',
        'Orya' => 'Kalinga',
        'Mlym' => 'Kartika',
        'Laoo' => 'DokChampa',
        'Sinh' => 'Iskoola Pota',
        'Mong' => 'Mongolian Baiti',
        'Viet' => 'Times New Roman',
        'Uigh' => 'Microsoft Uighur',
        'Geor' => 'Sylfaen',
    ];

    public const FONTS_ARIAL_SUBSTITUTIONS = [
        'Jpan' => 'ＭＳ Ｐゴシック',
        'Hang' => '맑은 고딕',
        'Hans' => '宋体',
        'Hant' => '新細明體',
        'Arab' => 'Arial',
        'Hebr' => 'Arial',
        'Thai' => 'Tahoma',
        'Ethi' => 'Nyala',
        'Beng' => 'Vrinda',
        'Gujr' => 'Shruti',
        'Khmr' => 'DaunPenh',
        'Knda' => 'Tunga',
        'Guru' => 'Raavi',
        'Cans' => 'Euphemia',
        'Cher' => 'Plantagenet Cherokee',
        'Yiii' => 'Microsoft Yi Baiti',
        'Tibt' => 'Microsoft Himalaya',
        'Thaa' => 'MV Boli',
        'Deva' => 'Mangal',
        'Telu' => 'Gautami',
        'Taml' => 'Latha',
        'Syrc' => 'Estrangelo Edessa',
        'Orya' => 'Kalinga',
        'Mlym' => 'Kartika',
        'Laoo' => 'DokChampa',
        'Sinh' => 'Iskoola Pota',
        'Mong' => 'Mongolian Baiti',
        'Viet' => 'Arial',
        'Uigh' => 'Microsoft Uighur',
        'Geor' => 'Sylfaen',
    ];

    /** @return string[] */
    public function getThemeColors(): array
    {
        return $this->themeColors;
    }

    public function setThemeColor(string $key, string $value): self
    {
        $this->themeColors[$key] = $value;

        return $this;
    }

    public function getThemeColorName(): string
    {
        return $this->themeColorName;
    }

    /** @param null|string[] $themeColors */
    public function setThemeColorName(string $name, ?array $themeColors = null, ?Spreadsheet $spreadsheet = null): self
    {
        if ($name === self::COLOR_SCHEME_2013_PLUS_NAME) {
            // Ensure against this value being found in
            // spreadsheets created while constant was public.
            $name = self::COLOR_SCHEME_2013_2022_NAME;
        }
        $this->themeColorName = $name;
        if ($name === self::COLOR_SCHEME_2007_2010_NAME) {
            $themeColors = $themeColors ?? self::COLOR_SCHEME_2007_2010;
            $this->majorFontLatin = 'Cambria';
            $this->minorFontLatin = 'Calibri';
        } elseif ($name === self::COLOR_SCHEME_2013_2022_NAME) {
            $themeColors = $themeColors ?? self::COLOR_SCHEME_2013_2022;
            $this->majorFontLatin = 'Calibri Light';
            $this->minorFontLatin = 'Calibri';
        } elseif ($name === self::COLOR_SCHEME_2023_PLUS_NAME) {
            $themeColors = $themeColors ?? self::COLOR_SCHEME_2023_PLUS;
            $this->majorFontLatin = 'Aptos Display';
            $this->minorFontLatin = 'Aptos Narrow';
        }
        if ($themeColors !== null) {
            $this->themeColors = $themeColors;
        }
        if ($spreadsheet !== null) {
            $spreadsheet->getDefaultStyle()->getFont()
                ->applyThemeFonts($this);
        }

        return $this;
    }

    public function getMajorFontLatin(): string
    {
        return $this->majorFontLatin;
    }

    public function getMajorFontEastAsian(): string
    {
        return $this->majorFontEastAsian;
    }

    public function getMajorFontComplexScript(): string
    {
        return $this->majorFontComplexScript;
    }

    /** @return string[] */
    public function getMajorFontSubstitutions(): array
    {
        return $this->majorFontSubstitutions;
    }

    /** @param null|string[] $substitutions */
    public function setMajorFontValues(?string $latin, ?string $eastAsian, ?string $complexScript, ?array $substitutions): self
    {
        if (!empty($latin)) {
            $this->majorFontLatin = $latin;
        }
        if ($eastAsian !== null) {
            $this->majorFontEastAsian = $eastAsian;
        }
        if ($complexScript !== null) {
            $this->majorFontComplexScript = $complexScript;
        }
        if ($substitutions !== null) {
            $this->majorFontSubstitutions = $substitutions;
        }

        return $this;
    }

    public function getMinorFontLatin(): string
    {
        return $this->minorFontLatin;
    }

    public function getMinorFontEastAsian(): string
    {
        return $this->minorFontEastAsian;
    }

    public function getMinorFontComplexScript(): string
    {
        return $this->minorFontComplexScript;
    }

    /** @return string[] */
    public function getMinorFontSubstitutions(): array
    {
        return $this->minorFontSubstitutions;
    }

    /** @param null|string[] $substitutions */
    public function setMinorFontValues(?string $latin, ?string $eastAsian, ?string $complexScript, ?array $substitutions): self
    {
        if (!empty($latin)) {
            $this->minorFontLatin = $latin;
        }
        if ($eastAsian !== null) {
            $this->minorFontEastAsian = $eastAsian;
        }
        if ($complexScript !== null) {
            $this->minorFontComplexScript = $complexScript;
        }
        if ($substitutions !== null) {
            $this->minorFontSubstitutions = $substitutions;
        }

        return $this;
    }

    public function getThemeFontName(): string
    {
        return $this->themeFontName;
    }

    public function setThemeFontName(?string $name): self
    {
        if (!empty($name)) {
            $this->themeFontName = $name;
        }

        return $this;
    }
}
