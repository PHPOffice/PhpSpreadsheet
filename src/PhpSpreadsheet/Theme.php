<?php

namespace PhpOffice\PhpSpreadsheet;

class Theme
{
    /** @var string */
    private $themeColorName = 'Office';

    public const COLOR_SCHEME_2013_PLUS_NAME = 'Office 2013+';
    public const COLOR_SCHEME_2013_PLUS = [
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

    /** @var string[] */
    private $themeColors = self::COLOR_SCHEME_2007_2010;

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

    public function setThemeColorName(string $name, ?array $themeColors = null): self
    {
        $this->themeColorName = $name;
        if ($name === self::COLOR_SCHEME_2007_2010_NAME) {
            $themeColors = $themeColors ?? self::COLOR_SCHEME_2007_2010;
        } elseif ($name === self::COLOR_SCHEME_2013_PLUS_NAME) {
            $themeColors = $themeColors ?? self::COLOR_SCHEME_2013_PLUS;
        }
        if ($themeColors !== null) {
            $this->themeColors = $themeColors;
        }

        return $this;
    }
}
