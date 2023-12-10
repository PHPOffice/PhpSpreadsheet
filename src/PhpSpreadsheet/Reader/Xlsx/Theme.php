<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Theme
{
    /**
     * Theme Name.
     */
    private string $themeName;

    /**
     * Colour Scheme Name.
     */
    private string $colourSchemeName;

    /**
     * Colour Map.
     *
     * @var string[]
     */
    private array $colourMap;

    /**
     * Create a new Theme.
     *
     * @param string $themeName
     * @param string $colourSchemeName
     * @param string[] $colourMap
     */
    public function __construct($themeName, $colourSchemeName, $colourMap)
    {
        // Initialise values
        $this->themeName = $themeName;
        $this->colourSchemeName = $colourSchemeName;
        $this->colourMap = $colourMap;
    }

    /**
     * Not called by Reader, never accessible any other time.
     *
     * @codeCoverageIgnore
     */
    public function getThemeName(): string
    {
        return $this->themeName;
    }

    /**
     * Not called by Reader, never accessible any other time.
     *
     * @codeCoverageIgnore
     */
    public function getColourSchemeName(): string
    {
        return $this->colourSchemeName;
    }

    /**
     * Get colour Map Value by Position.
     *
     * @param int $index
     */
    public function getColourByIndex($index): ?string
    {
        return $this->colourMap[$index] ?? null;
    }
}
