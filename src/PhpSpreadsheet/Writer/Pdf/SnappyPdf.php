<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Pdf;

use Exception;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Pdf;

class SnappyPdf extends Pdf
{
    const DEFAULT_BINARY_PATH = '/usr/bin/wkhtmltopdf';

    private $binaryPath;

    /**
     * Gets the implementation of external PDF library that should be used.
     *
     * @return \Knp\Snappy\Pdf implementation
     */
    protected function createExternalWriterInstance()
    {
        return new \Knp\Snappy\Pdf($this->getBinaryPath());
    }

    /**
     * @param string $binaryPath
     */
    public function setBinaryPath($binaryPath): void
    {
        $this->binaryPath = $binaryPath;
    }

    private function getBinaryPath()
    {
        if (null === $this->binaryPath && file_exists(self::DEFAULT_BINARY_PATH)) {
            return $this->binaryPath = self::DEFAULT_BINARY_PATH;
        }

        //Try to detect from path
        if (null === $this->binaryPath && @exec('echo EXEC') == 'EXEC') {
            $this->binaryPath = exec('which wkhtmltopdf');
        }

        //Use if h4cc/wkhtmltopdf-amd64 package installed
        if (empty($this->binaryPath) && class_exists('\h4cc\WKHTMLToPDF\WKHTMLToPDF')) {
            $this->binaryPath = \h4cc\WKHTMLToPDF\WKHTMLToPDF::PATH;
        }

        if (empty($this->binaryPath) || !file_exists($this->binaryPath)) {
            throw new Exception('"wkhtmltopdf" binary not found');
        }

        return $this->binaryPath;
    }

    /**
     * Save Spreadsheet to file.
     *
     * @param string $pFilename Name of the file to save as
     */
    public function save($pFilename): void
    {
        $fileHandle = parent::prepareForSave($pFilename);

        //  Default PDF paper size
        $paperSize = 'LETTER'; //    Letter    (8.5 in. by 11 in.)

        //  Check for paper size and page orientation
        if ($this->getSheetIndex() === null) {
            $orientation = ($this->spreadsheet->getSheet(0)->getPageSetup()->getOrientation()
                == PageSetup::ORIENTATION_LANDSCAPE) ? 'L' : 'P';
            $printPaperSize = $this->spreadsheet->getSheet(0)->getPageSetup()->getPaperSize();
        } else {
            $orientation = ($this->spreadsheet->getSheet($this->getSheetIndex())->getPageSetup()->getOrientation()
                == PageSetup::ORIENTATION_LANDSCAPE) ? 'L' : 'P';
            $printPaperSize = $this->spreadsheet->getSheet($this->getSheetIndex())->getPageSetup()->getPaperSize();
        }

        $orientation = ($orientation == 'L') ? 'landscape' : 'portrait';

        //  Override Page Orientation
        if ($this->getOrientation() !== null) {
            $orientation = ($this->getOrientation() == PageSetup::ORIENTATION_DEFAULT)
                ? PageSetup::ORIENTATION_PORTRAIT
                : $this->getOrientation();
        }
        //  Override Paper Size
        if ($this->getPaperSize() !== null) {
            $printPaperSize = $this->getPaperSize();
        }

        if (isset(self::$paperSizes[$printPaperSize])) {
            $paperSize = self::$paperSizes[$printPaperSize];
        }

        //  Create PDF
        $pdf = $this->createExternalWriterInstance();

        $pdf->setOptions(
            [
                'page-size' => strtoupper($paperSize),
                'orientation' => $orientation,
                'margin-left' => $this->inchesToMm($this->spreadsheet->getActiveSheet()->getPageMargins()->getLeft()),
                'margin-right' => $this->inchesToMm($this->spreadsheet->getActiveSheet()->getPageMargins()->getRight()),
                'margin-top' => $this->inchesToMm($this->spreadsheet->getActiveSheet()->getPageMargins()->getTop()),
                'margin-bottom' => $this->inchesToMm(
                    $this->spreadsheet->getActiveSheet()->getPageMargins()->getBottom()
                ),
                'title' => $this->spreadsheet->getProperties()->getTitle(),
            ]
        );

        //  Write to file
        fwrite($fileHandle, $pdf->getOutputFromHtml($this->generateHTMLAll()));

        parent::restoreStateAfterSave();
    }

    /**
     * Convert inches to mm.
     *
     * @param float $inches
     *
     * @return float
     */
    private function inchesToMm($inches)
    {
        return $inches * 25.4;
    }
}
