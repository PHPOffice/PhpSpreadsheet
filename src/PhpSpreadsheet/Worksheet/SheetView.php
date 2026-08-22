<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;

class SheetView
{
    // Sheet View types
    const SHEETVIEW_NORMAL = 'normal';
    const SHEETVIEW_PAGE_LAYOUT = 'pageLayout';
    const SHEETVIEW_PAGE_BREAK_PREVIEW = 'pageBreakPreview';

    private const SHEET_VIEW_TYPES = [
        self::SHEETVIEW_NORMAL,
        self::SHEETVIEW_PAGE_LAYOUT,
        self::SHEETVIEW_PAGE_BREAK_PREVIEW,
    ];

    /**
     * ZoomScale.
     *
     * Valid values range from 10 to 400.
     */
    private ?int $zoomScale = 100;

    /**
     * ZoomScaleNormal.
     *
     * Valid values range from 10 to 400.
     */
    private ?int $zoomScaleNormal = 100;

    /**
     * ZoomScalePageLayoutView.
     *
     * Valid values range from 10 to 400.
     */
    private int $zoomScalePageLayoutView = 100;

    /**
     * ZoomScaleSheetLayoutView.
     *
     * Valid values range from 10 to 400.
     */
    private int $zoomScaleSheetLayoutView = 100;

    /**
     * ShowZeros.
     *
     * If true, "null" values from a calculation will be shown as "0". This is the default Excel behaviour and can be changed
     * with the advanced worksheet option "Show a zero in cells that have zero value"
     */
    private bool $showZeros = true;

    /**
     * View.
     *
     * Valid values range from 10 to 400.
     */
    private string $sheetviewType = self::SHEETVIEW_NORMAL;

    /**
     * Create a new SheetView.
     */
    public function __construct()
    {
    }

    /**
     * Get ZoomScale.
     */
    public function getZoomScale(): ?int
    {
        return $this->zoomScale;
    }

    /**
     * Set ZoomScale.
     * Valid values range from 10 to 400.
     *
     * @return $this
     */
    public function setZoomScale(?int $zoomScale): static
    {
        // Microsoft Office Excel 2007 only allows setting a scale between 10 and 400 via the user interface,
        // but it is apparently still able to handle any scale >= 1
        if ($zoomScale === null || $zoomScale >= 1) {
            $this->zoomScale = $zoomScale;
        } else {
            throw new PhpSpreadsheetException('Scale must be greater than or equal to 1.');
        }

        return $this;
    }

    /**
     * Get ZoomScaleNormal.
     */
    public function getZoomScaleNormal(): ?int
    {
        return $this->zoomScaleNormal;
    }

    /**
     * Set ZoomScale.
     * Valid values range from 10 to 400.
     *
     * @return $this
     */
    public function setZoomScaleNormal(?int $zoomScaleNormal): static
    {
        if ($zoomScaleNormal === null || $zoomScaleNormal >= 1) {
            $this->zoomScaleNormal = $zoomScaleNormal;
        } else {
            throw new PhpSpreadsheetException('Scale must be greater than or equal to 1.');
        }

        return $this;
    }

    public function getZoomScalePageLayoutView(): int
    {
        return $this->zoomScalePageLayoutView;
    }

    public function setZoomScalePageLayoutView(int $zoomScalePageLayoutView): static
    {
        if ($zoomScalePageLayoutView >= 1) {
            $this->zoomScalePageLayoutView = $zoomScalePageLayoutView;
        } else {
            throw new PhpSpreadsheetException('Scale must be greater than or equal to 1.');
        }

        return $this;
    }

    public function getZoomScaleSheetLayoutView(): int
    {
        return $this->zoomScaleSheetLayoutView;
    }

    public function setZoomScaleSheetLayoutView(int $zoomScaleSheetLayoutView): static
    {
        if ($zoomScaleSheetLayoutView >= 1) {
            $this->zoomScaleSheetLayoutView = $zoomScaleSheetLayoutView;
        } else {
            throw new PhpSpreadsheetException('Scale must be greater than or equal to 1.');
        }

        return $this;
    }

    /**
     * Set ShowZeroes setting.
     */
    public function setShowZeros(bool $showZeros): void
    {
        $this->showZeros = $showZeros;
    }

    public function getShowZeros(): bool
    {
        return $this->showZeros;
    }

    /**
     * Get View.
     */
    public function getView(): string
    {
        return $this->sheetviewType;
    }

    /**
     * Set View.
     *
     * Valid values are
     *        'normal'            self::SHEETVIEW_NORMAL
     *        'pageLayout'        self::SHEETVIEW_PAGE_LAYOUT
     *        'pageBreakPreview'  self::SHEETVIEW_PAGE_BREAK_PREVIEW
     *
     * @return $this
     */
    public function setView(?string $sheetViewType): static
    {
        // MS Excel 2007 allows setting the view to 'normal', 'pageLayout' or 'pageBreakPreview' via the user interface
        if ($sheetViewType === null) {
            $sheetViewType = self::SHEETVIEW_NORMAL;
        }
        if (in_array($sheetViewType, self::SHEET_VIEW_TYPES)) {
            $this->sheetviewType = $sheetViewType;
        } else {
            throw new PhpSpreadsheetException('Invalid sheetview layout type.');
        }

        return $this;
    }
}
