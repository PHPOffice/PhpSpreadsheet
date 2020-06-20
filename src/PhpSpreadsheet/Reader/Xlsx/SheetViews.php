<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use SimpleXMLElement;

class SheetViews extends BaseParserClass
{
    private $sheetViewXml;

    private $worksheet;

    public function __construct(SimpleXMLElement $sheetViewXml, Worksheet $workSheet)
    {
        $this->sheetViewXml = $sheetViewXml;
        $this->worksheet = $workSheet;
    }

    public function load(): void
    {
        $this->zoomScale();
        $this->view();
        $this->gridLines();
        $this->headers();
        $this->direction();
        $this->showZeros();

        if (isset($this->sheetViewXml->pane)) {
            $this->pane();
        }
        if (isset($this->sheetViewXml->selection, $this->sheetViewXml->selection['sqref'])) {
            $this->selection();
        }
    }

    private function zoomScale(): void
    {
        if (isset($this->sheetViewXml['zoomScale'])) {
            $zoomScale = (int) ($this->sheetViewXml['zoomScale']);
            if ($zoomScale <= 0) {
                // setZoomScale will throw an Exception if the scale is less than or equals 0
                // that is OK when manually creating documents, but we should be able to read all documents
                $zoomScale = 100;
            }

            $this->worksheet->getSheetView()->setZoomScale($zoomScale);
        }

        if (isset($this->sheetViewXml['zoomScaleNormal'])) {
            $zoomScaleNormal = (int) ($this->sheetViewXml['zoomScaleNormal']);
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
        if (isset($this->sheetViewXml['view'])) {
            $this->worksheet->getSheetView()->setView((string) $this->sheetViewXml['view']);
        }
    }

    private function gridLines(): void
    {
        if (isset($this->sheetViewXml['showGridLines'])) {
            $this->worksheet->setShowGridLines(
                self::boolean((string) $this->sheetViewXml['showGridLines'])
            );
        }
    }

    private function headers(): void
    {
        if (isset($this->sheetViewXml['showRowColHeaders'])) {
            $this->worksheet->setShowRowColHeaders(
                self::boolean((string) $this->sheetViewXml['showRowColHeaders'])
            );
        }
    }

    private function direction(): void
    {
        if (isset($this->sheetViewXml['rightToLeft'])) {
            $this->worksheet->setRightToLeft(
                self::boolean((string) $this->sheetViewXml['rightToLeft'])
            );
        }
    }

    private function showZeros(): void
    {
        if (isset($this->sheetViewXml['showZeros'])) {
            $this->worksheet->getSheetView()->setShowZeros(
                self::boolean((string) $this->sheetViewXml['showZeros'])
            );
        }
    }

    private function pane(): void
    {
        $xSplit = 0;
        $ySplit = 0;
        $topLeftCell = null;

        if (isset($this->sheetViewXml->pane['xSplit'])) {
            $xSplit = (int) ($this->sheetViewXml->pane['xSplit']);
        }

        if (isset($this->sheetViewXml->pane['ySplit'])) {
            $ySplit = (int) ($this->sheetViewXml->pane['ySplit']);
        }

        if (isset($this->sheetViewXml->pane['topLeftCell'])) {
            $topLeftCell = (string) $this->sheetViewXml->pane['topLeftCell'];
        }

        $this->worksheet->freezePane(
            Coordinate::stringFromColumnIndex($xSplit + 1) . ($ySplit + 1),
            $topLeftCell
        );
    }

    private function selection(): void
    {
        $sqref = (string) $this->sheetViewXml->selection['sqref'];
        $sqref = explode(' ', $sqref);
        $sqref = $sqref[0];

        $this->worksheet->setSelectedCells($sqref);
    }
}
