<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Theme
{
    /**
     * Theme Name.
     *
     * @var string
     */
    private $themeName;

    /**
     * Colour Scheme Name.
     *
     * @var string
     */
    private $colourSchemeName;

    /**
     * Colour Map.
     *
     * @var string[]
     */
    private $colourMap;

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
     * @return string
     *
     * @codeCoverageIgnore
     */
    public function getThemeName()
    {
        return $this->themeName;
    }

    /**
     * Not called by Reader, never accessible any other time.
     *
     * @return string
     *
     * @codeCoverageIgnore
     */
    public function getColourSchemeName()
    {
        return $this->colourSchemeName;
    }

    /**
     * Get colour Map Value by Position.
     *
     * @param int $index
     *
     * @return null|string
     */
    public function getColourByIndex($index)
    {
        return $this->colourMap[$index] ?? null;
    }
}
