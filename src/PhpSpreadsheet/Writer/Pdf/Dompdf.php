<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Pdf;

use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Pdf;

class Dompdf extends Pdf
{
    /**
     * embed images, or link to images.
     */
    protected bool $embedImages = true;

    /**
     * Gets the implementation of external PDF library that should be used.
     *
     * @return \Dompdf\Dompdf implementation
     */
    protected function createExternalWriterInstance(): \Dompdf\Dompdf
    {
        return new \Dompdf\Dompdf();
    }

    /**
     * Save Spreadsheet to file.
     *
     * @param string $filename Name of the file to save as
     */
    public function save($filename, int $flags = 0): void
    {
        $fileHandle = parent::prepareForSave($filename);

        //  Check for paper size and page orientation
        $setup = $this->spreadsheet->getSheet($this->getSheetIndex() ?? 0)->getPageSetup();
        $orientation = $this->getOrientation() ?? $setup->getOrientation();
        $orientation = ($orientation === PageSetup::ORIENTATION_LANDSCAPE) ? 'L' : 'P';
        $printPaperSize = $this->getPaperSize() ?? $setup->getPaperSize();
        $paperSize = self::$paperSizes[$printPaperSize] ?? self::$paperSizes[PageSetup::getPaperSizeDefault()] ?? 'LETTER';
        if (is_array($paperSize) && count($paperSize) === 2) {
            $paperSize = [0.0, 0.0, $paperSize[0], $paperSize[1]];
        }

        $orientation = ($orientation == 'L') ? 'landscape' : 'portrait';

        //  Create PDF
        $restoreHandler = false;
        if (PHP_VERSION_ID >= self::$temporaryVersionCheck) {
            // @codeCoverageIgnoreStart
            set_error_handler(self::specialErrorHandler(...));
            $restoreHandler = true;
            // @codeCoverageIgnoreEnd
        }
        $pdf = $this->createExternalWriterInstance();
        $pdf->setPaper($paperSize, $orientation);

        $pdf->loadHtml($this->generateHTMLAll());
        $pdf->render();

        //  Write to file
        fwrite($fileHandle, $pdf->output() ?? '');

        if ($restoreHandler) {
            restore_error_handler(); // @codeCoverageIgnore
        }
        parent::restoreStateAfterSave();
    }

    protected static int $temporaryVersionCheck = 80500;

    /**
     * Temporary handler for Php8.5 waiting for Dompdf release.
     *
     * @codeCoverageIgnore
     */
    public function specialErrorHandler(int $errno, string $errstr, string $filename, int $lineno): bool
    {
        if ($errno === E_DEPRECATED) {
            if (preg_match('/canonical|imagedestroy|http_get_last_response_headers/', $errstr) === 1) {
                return true;
            }
        }

        return false; // continue error handling
    }
}
