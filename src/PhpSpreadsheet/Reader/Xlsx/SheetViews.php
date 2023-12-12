<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Pane;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use SimpleXMLElement;

class SheetViews extends BaseParserClass
{
    private SimpleXMLElement $sheetViewXml;

    private SimpleXMLElement $sheetViewAttributes;

    private Worksheet $worksheet;

    private string $activePane = '';

    public function __construct(SimpleXMLElement $sheetViewXml, Worksheet $workSheet)
    {
        $this->sheetViewXml = $sheetViewXml;
        $this->sheetViewAttributes = Xlsx::testSimpleXml($sheetViewXml->attributes());
        $this->worksheet = $workSheet;
    }

    public function load(): void
    {
        $this->topLeft();
        $this->zoomScale();
        $this->view();
        $this->gridLines();
        $this->headers();
        $this->direction();
        $this->showZeros();

        $usesPanes = false;
        if (isset($this->sheetViewXml->pane)) {
            $this->pane();
            $usesPanes = true;
        }
        if (isset($this->sheetViewXml->selection)) {
            foreach ($this->sheetViewXml->selection as $selection) {
                $this->selection($selection, $usesPanes);
            }
        }
    }

    private function zoomScale(): void
    {
        if (isset($this->sheetViewAttributes->zoomScale)) {
            $zoomScale = (int) ($this->sheetViewAttributes->zoomScale);
            if ($zoomScale <= 0) {
                // setZoomScale will throw an Exception if the scale is less than or equals 0
                // that is OK when manually creating documents, but we should be able to read all documents
                $zoomScale = 100;
            }

            $this->worksheet->getSheetView()->setZoomScale($zoomScale);
        }

        if (isset($this->sheetViewAttributes->zoomScaleNormal)) {
            $zoomScaleNormal = (int) ($this->sheetViewAttributes->zoomScaleNormal);
            if ($zoomScaleNormal <= 0) {
                // setZoomScaleNormal will throw an Exception if the scale is less than or equals 0
                // that is OK when manually creating documents, but we should be able to read all documents
                $zoomScaleNormal = 100;
            }

            $this->worksheet->getSheetView()->setZoomScaleNormal($zoomScaleNormal);
        }
    }

    private function view(): void
    {
        if (isset($this->sheetViewAttributes->view)) {
            $this->worksheet->getSheetView()->setView((string) $this->sheetViewAttributes->view);
        }
    }

    private function topLeft(): void
    {
        if (isset($this->sheetViewAttributes->topLeftCell)) {
            $this->worksheet->setTopLeftCell($this->sheetViewAttributes->topLeftCell);
        }
    }

    private function gridLines(): void
    {
        if (isset($this->sheetViewAttributes->showGridLines)) {
            $this->worksheet->setShowGridLines(
                self::boolean((string) $this->sheetViewAttributes->showGridLines)
            );
        }
    }

    private function headers(): void
    {
        if (isset($this->sheetViewAttributes->showRowColHeaders)) {
            $this->worksheet->setShowRowColHeaders(
                self::boolean((string) $this->sheetViewAttributes->showRowColHeaders)
            );
        }
    }

    private function direction(): void
    {
        if (isset($this->sheetViewAttributes->rightToLeft)) {
            $this->worksheet->setRightToLeft(
                self::boolean((string) $this->sheetViewAttributes->rightToLeft)
            );
        }
    }

    private function showZeros(): void
    {
        if (isset($this->sheetViewAttributes->showZeros)) {
            $this->worksheet->getSheetView()->setShowZeros(
                self::boolean((string) $this->sheetViewAttributes->showZeros)
            );
        }
    }

    private function pane(): void
    {
        $xSplit = 0;
        $ySplit = 0;
        $topLeftCell = null;
        $paneAttributes = $this->sheetViewXml->pane->attributes();

        if (isset($paneAttributes->xSplit)) {
            $xSplit = (int) ($paneAttributes->xSplit);
            $this->worksheet->setXSplit($xSplit);
        }

        if (isset($paneAttributes->ySplit)) {
            $ySplit = (int) ($paneAttributes->ySplit);
            $this->worksheet->setYSplit($ySplit);
        }
        $paneState = isset($paneAttributes->state) ? ((string) $paneAttributes->state) : '';
        $this->worksheet->setPaneState($paneState);
        if (isset($paneAttributes->topLeftCell)) {
            $topLeftCell = (string) $paneAttributes->topLeftCell;
            $this->worksheet->setPaneTopLeftCell($topLeftCell);
            if ($paneState === Worksheet::PANE_FROZEN) {
                $this->worksheet->setTopLeftCell($topLeftCell);
            }
        }
        $activePane = isset($paneAttributes->activePane) ? ((string) $paneAttributes->activePane) : 'topLeft';
        $this->worksheet->setActivePane($activePane);
        $this->activePane = $activePane;
        if ($paneState === Worksheet::PANE_FROZEN || $paneState === Worksheet::PANE_FROZENSPLIT) {
            $this->worksheet->freezePane(
                Coordinate::stringFromColumnIndex($xSplit + 1) . ($ySplit + 1),
                $topLeftCell,
                $paneState === Worksheet::PANE_FROZENSPLIT
            );
        }
    }

    private function selection(?SimpleXMLElement $selection, bool $usesPanes): void
    {
        $attributes = ($selection === null) ? null : $selection->attributes();
        if ($attributes !== null) {
            $position = (string) $attributes->pane;
            if ($usesPanes && $position === '') {
                $position = 'topLeft';
            }
            $activeCell = (string) $attributes->activeCell;
            $sqref = (string) $attributes->sqref;
            $sqref = explode(' ', $sqref);
            $sqref = $sqref[0];
            if ($position === '') {
                $this->worksheet->setSelectedCells($sqref);
            } else {
                $pane = new Pane($position, $sqref, $activeCell);
                $this->worksheet->setPane($position, $pane);
                if ($position === $this->activePane && $sqref !== '') {
                    $this->worksheet->setSelectedCells($sqref);
                }
            }
        }
    }
}
