<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet\Table;

use PhpOffice\PhpSpreadsheet\Worksheet\Table;

class TableStyle
{
    const TABLE_STYLE_NONE = '';
    const TABLE_STYLE_LIGHT1 = 'TableStyleLight1';
    const TABLE_STYLE_LIGHT2 = 'TableStyleLight2';
    const TABLE_STYLE_LIGHT3 = 'TableStyleLight3';
    const TABLE_STYLE_LIGHT4 = 'TableStyleLight4';
    const TABLE_STYLE_LIGHT5 = 'TableStyleLight5';
    const TABLE_STYLE_LIGHT6 = 'TableStyleLight6';
    const TABLE_STYLE_LIGHT7 = 'TableStyleLight7';
    const TABLE_STYLE_LIGHT8 = 'TableStyleLight8';
    const TABLE_STYLE_LIGHT9 = 'TableStyleLight9';
    const TABLE_STYLE_LIGHT10 = 'TableStyleLight10';
    const TABLE_STYLE_LIGHT11 = 'TableStyleLight11';
    const TABLE_STYLE_LIGHT12 = 'TableStyleLight12';
    const TABLE_STYLE_LIGHT13 = 'TableStyleLight13';
    const TABLE_STYLE_LIGHT14 = 'TableStyleLight14';
    const TABLE_STYLE_LIGHT15 = 'TableStyleLight15';
    const TABLE_STYLE_LIGHT16 = 'TableStyleLight16';
    const TABLE_STYLE_LIGHT17 = 'TableStyleLight17';
    const TABLE_STYLE_LIGHT18 = 'TableStyleLight18';
    const TABLE_STYLE_LIGHT19 = 'TableStyleLight19';
    const TABLE_STYLE_LIGHT20 = 'TableStyleLight20';
    const TABLE_STYLE_LIGHT21 = 'TableStyleLight21';
    const TABLE_STYLE_MEDIUM1 = 'TableStyleMedium1';
    const TABLE_STYLE_MEDIUM2 = 'TableStyleMedium2';
    const TABLE_STYLE_MEDIUM3 = 'TableStyleMedium3';
    const TABLE_STYLE_MEDIUM4 = 'TableStyleMedium4';
    const TABLE_STYLE_MEDIUM5 = 'TableStyleMedium5';
    const TABLE_STYLE_MEDIUM6 = 'TableStyleMedium6';
    const TABLE_STYLE_MEDIUM7 = 'TableStyleMedium7';
    const TABLE_STYLE_MEDIUM8 = 'TableStyleMedium8';
    const TABLE_STYLE_MEDIUM9 = 'TableStyleMedium9';
    const TABLE_STYLE_MEDIUM10 = 'TableStyleMedium10';
    const TABLE_STYLE_MEDIUM11 = 'TableStyleMedium11';
    const TABLE_STYLE_MEDIUM12 = 'TableStyleMedium12';
    const TABLE_STYLE_MEDIUM13 = 'TableStyleMedium13';
    const TABLE_STYLE_MEDIUM14 = 'TableStyleMedium14';
    const TABLE_STYLE_MEDIUM15 = 'TableStyleMedium15';
    const TABLE_STYLE_MEDIUM16 = 'TableStyleMedium16';
    const TABLE_STYLE_MEDIUM17 = 'TableStyleMedium17';
    const TABLE_STYLE_MEDIUM18 = 'TableStyleMedium18';
    const TABLE_STYLE_MEDIUM19 = 'TableStyleMedium19';
    const TABLE_STYLE_MEDIUM20 = 'TableStyleMedium20';
    const TABLE_STYLE_MEDIUM21 = 'TableStyleMedium21';
    const TABLE_STYLE_MEDIUM22 = 'TableStyleMedium22';
    const TABLE_STYLE_MEDIUM23 = 'TableStyleMedium23';
    const TABLE_STYLE_MEDIUM24 = 'TableStyleMedium24';
    const TABLE_STYLE_MEDIUM25 = 'TableStyleMedium25';
    const TABLE_STYLE_MEDIUM26 = 'TableStyleMedium26';
    const TABLE_STYLE_MEDIUM27 = 'TableStyleMedium27';
    const TABLE_STYLE_MEDIUM28 = 'TableStyleMedium28';
    const TABLE_STYLE_DARK1 = 'TableStyleDark1';
    const TABLE_STYLE_DARK2 = 'TableStyleDark2';
    const TABLE_STYLE_DARK3 = 'TableStyleDark3';
    const TABLE_STYLE_DARK4 = 'TableStyleDark4';
    const TABLE_STYLE_DARK5 = 'TableStyleDark5';
    const TABLE_STYLE_DARK6 = 'TableStyleDark6';
    const TABLE_STYLE_DARK7 = 'TableStyleDark7';
    const TABLE_STYLE_DARK8 = 'TableStyleDark8';
    const TABLE_STYLE_DARK9 = 'TableStyleDark9';
    const TABLE_STYLE_DARK10 = 'TableStyleDark10';
    const TABLE_STYLE_DARK11 = 'TableStyleDark11';

    /**
     * Theme.
     *
     * @var string
     */
    private $theme;

    /**
     * Show First Column.
     *
     * @var bool
     */
    private $showFirstColumn = false;

    /**
     * Show Last Column.
     *
     * @var bool
     */
    private $showLastColumn = false;

    /**
     * Show Row Stripes.
     *
     * @var bool
     */
    private $showRowStripes = false;

    /**
     * Show Column Stripes.
     *
     * @var bool
     */
    private $showColumnStripes = false;

    /**
     * Table.
     *
     * @var null|Table
     */
    private $table;

    /**
     * Create a new Table Style.
     *
     * @param string $theme (e.g. TableStyle::TABLE_STYLE_MEDIUM2)
     */
    public function __construct(string $theme = self::TABLE_STYLE_MEDIUM2)
    {
        $this->theme = $theme;
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
