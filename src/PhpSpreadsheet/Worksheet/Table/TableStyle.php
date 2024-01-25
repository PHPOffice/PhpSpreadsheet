<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet\Table;

use PhpOffice\PhpSpreadsheet\Worksheet\Table;

class TableStyle
{
    public const TABLE_STYLE_NONE = '';
    public const TABLE_STYLE_LIGHT1 = 'TableStyleLight1';
    public const TABLE_STYLE_LIGHT2 = 'TableStyleLight2';
    public const TABLE_STYLE_LIGHT3 = 'TableStyleLight3';
    public const TABLE_STYLE_LIGHT4 = 'TableStyleLight4';
    public const TABLE_STYLE_LIGHT5 = 'TableStyleLight5';
    public const TABLE_STYLE_LIGHT6 = 'TableStyleLight6';
    public const TABLE_STYLE_LIGHT7 = 'TableStyleLight7';
    public const TABLE_STYLE_LIGHT8 = 'TableStyleLight8';
    public const TABLE_STYLE_LIGHT9 = 'TableStyleLight9';
    public const TABLE_STYLE_LIGHT10 = 'TableStyleLight10';
    public const TABLE_STYLE_LIGHT11 = 'TableStyleLight11';
    public const TABLE_STYLE_LIGHT12 = 'TableStyleLight12';
    public const TABLE_STYLE_LIGHT13 = 'TableStyleLight13';
    public const TABLE_STYLE_LIGHT14 = 'TableStyleLight14';
    public const TABLE_STYLE_LIGHT15 = 'TableStyleLight15';
    public const TABLE_STYLE_LIGHT16 = 'TableStyleLight16';
    public const TABLE_STYLE_LIGHT17 = 'TableStyleLight17';
    public const TABLE_STYLE_LIGHT18 = 'TableStyleLight18';
    public const TABLE_STYLE_LIGHT19 = 'TableStyleLight19';
    public const TABLE_STYLE_LIGHT20 = 'TableStyleLight20';
    public const TABLE_STYLE_LIGHT21 = 'TableStyleLight21';
    public const TABLE_STYLE_MEDIUM1 = 'TableStyleMedium1';
    public const TABLE_STYLE_MEDIUM2 = 'TableStyleMedium2';
    public const TABLE_STYLE_MEDIUM3 = 'TableStyleMedium3';
    public const TABLE_STYLE_MEDIUM4 = 'TableStyleMedium4';
    public const TABLE_STYLE_MEDIUM5 = 'TableStyleMedium5';
    public const TABLE_STYLE_MEDIUM6 = 'TableStyleMedium6';
    public const TABLE_STYLE_MEDIUM7 = 'TableStyleMedium7';
    public const TABLE_STYLE_MEDIUM8 = 'TableStyleMedium8';
    public const TABLE_STYLE_MEDIUM9 = 'TableStyleMedium9';
    public const TABLE_STYLE_MEDIUM10 = 'TableStyleMedium10';
    public const TABLE_STYLE_MEDIUM11 = 'TableStyleMedium11';
    public const TABLE_STYLE_MEDIUM12 = 'TableStyleMedium12';
    public const TABLE_STYLE_MEDIUM13 = 'TableStyleMedium13';
    public const TABLE_STYLE_MEDIUM14 = 'TableStyleMedium14';
    public const TABLE_STYLE_MEDIUM15 = 'TableStyleMedium15';
    public const TABLE_STYLE_MEDIUM16 = 'TableStyleMedium16';
    public const TABLE_STYLE_MEDIUM17 = 'TableStyleMedium17';
    public const TABLE_STYLE_MEDIUM18 = 'TableStyleMedium18';
    public const TABLE_STYLE_MEDIUM19 = 'TableStyleMedium19';
    public const TABLE_STYLE_MEDIUM20 = 'TableStyleMedium20';
    public const TABLE_STYLE_MEDIUM21 = 'TableStyleMedium21';
    public const TABLE_STYLE_MEDIUM22 = 'TableStyleMedium22';
    public const TABLE_STYLE_MEDIUM23 = 'TableStyleMedium23';
    public const TABLE_STYLE_MEDIUM24 = 'TableStyleMedium24';
    public const TABLE_STYLE_MEDIUM25 = 'TableStyleMedium25';
    public const TABLE_STYLE_MEDIUM26 = 'TableStyleMedium26';
    public const TABLE_STYLE_MEDIUM27 = 'TableStyleMedium27';
    public const TABLE_STYLE_MEDIUM28 = 'TableStyleMedium28';
    public const TABLE_STYLE_DARK1 = 'TableStyleDark1';
    public const TABLE_STYLE_DARK2 = 'TableStyleDark2';
    public const TABLE_STYLE_DARK3 = 'TableStyleDark3';
    public const TABLE_STYLE_DARK4 = 'TableStyleDark4';
    public const TABLE_STYLE_DARK5 = 'TableStyleDark5';
    public const TABLE_STYLE_DARK6 = 'TableStyleDark6';
    public const TABLE_STYLE_DARK7 = 'TableStyleDark7';
    public const TABLE_STYLE_DARK8 = 'TableStyleDark8';
    public const TABLE_STYLE_DARK9 = 'TableStyleDark9';
    public const TABLE_STYLE_DARK10 = 'TableStyleDark10';
    public const TABLE_STYLE_DARK11 = 'TableStyleDark11';

    /**
     * Show First Column.
     */
    private bool $showFirstColumn = false;

    /**
     * Show Last Column.
     */
    private bool $showLastColumn = false;

    /**
     * Show Row Stripes.
     */
    private bool $showRowStripes = false;

    /**
     * Show Column Stripes.
     */
    private bool $showColumnStripes = false;

    /**
     * Table.
     */
    private ?Table $table = null;

    /**
     * Create a new Table Style.
     *
     * @param string $theme (e.g. TableStyle::TABLE_STYLE_MEDIUM2)
     */
    public function __construct(private string $theme = self::TABLE_STYLE_MEDIUM2)
    {
    }

    /**
     * Get theme.
     */
    public function getTheme(): string
    {
        return $this->theme;
    }

    /**
     * Set theme.
     */
    public function setTheme(string $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Get show First Column.
     */
    public function getShowFirstColumn(): bool
    {
        return $this->showFirstColumn;
    }

    /**
     * Set show First Column.
     */
    public function setShowFirstColumn(bool $showFirstColumn): self
    {
        $this->showFirstColumn = $showFirstColumn;

        return $this;
    }

    /**
     * Get show Last Column.
     */
    public function getShowLastColumn(): bool
    {
        return $this->showLastColumn;
    }

    /**
     * Set show Last Column.
     */
    public function setShowLastColumn(bool $showLastColumn): self
    {
        $this->showLastColumn = $showLastColumn;

        return $this;
    }

    /**
     * Get show Row Stripes.
     */
    public function getShowRowStripes(): bool
    {
        return $this->showRowStripes;
    }

    /**
     * Set show Row Stripes.
     */
    public function setShowRowStripes(bool $showRowStripes): self
    {
        $this->showRowStripes = $showRowStripes;

        return $this;
    }

    /**
     * Get show Column Stripes.
     */
    public function getShowColumnStripes(): bool
    {
        return $this->showColumnStripes;
    }

    /**
     * Set show Column Stripes.
     */
    public function setShowColumnStripes(bool $showColumnStripes): self
    {
        $this->showColumnStripes = $showColumnStripes;

        return $this;
    }

    /**
     * Get this Style's Table.
     */
    public function getTable(): ?Table
    {
        return $this->table;
    }

    /**
     * Set this Style's Table.
     */
    public function setTable(?Table $table = null): self
    {
        $this->table = $table;

        return $this;
    }
}
