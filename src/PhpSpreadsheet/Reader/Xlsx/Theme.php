<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Theme
{
    /**
     * Create a new Theme.
     *
     * @param string[] $colourMap
     */
    public function __construct(
        /**
         * Theme Name.
         */
        private string $themeName,
        /**
         * Colour Scheme Name.
         */
        private string $colourSchemeName,
        /**
         * Colour Map.
         */
        private array $colourMap
    ) {
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
     */
    public function getColourByIndex(int $index): ?string
    {
        return $this->colourMap[$index] ?? null;
    }
}
