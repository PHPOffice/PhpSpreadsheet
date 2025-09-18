<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Pdf;

use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Pdf;

class Mpdf extends Pdf
{
    public const SIMULATED_BODY_START = '<!-- simulated body start -->';
    private const BODY_TAG = '<body>';

    /**
     * Gets the implementation of external PDF library that should be used.
     *
     * @param mixed[] $config Configuration array
     *
     * @return \Mpdf\Mpdf implementation
     */
    protected function createExternalWriterInstance(array $config): \Mpdf\Mpdf
    {
        return new \Mpdf\Mpdf($config);
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
        $paperSize = self::$paperSizes[$printPaperSize] ?? PageSetup::getPaperSizeDefault();

        //  Create PDF
        $config = ['tempDir' => $this->tempDir . '/mpdf'];
        $restoreHandler = false;
        if (PHP_VERSION_ID >= self::$temporaryVersionCheck) {
            // @codeCoverageIgnoreStart
            set_error_handler(self::specialErrorHandler(...));
            $restoreHandler = true;
            // @codeCoverageIgnoreEnd
        }
        $pdf = $this->createExternalWriterInstance($config);
        $ortmp = $orientation;
        $pdf->_setPageSize($paperSize, $ortmp);
        $pdf->DefOrientation = $orientation;
        $pdf->AddPageByArray([
            'orientation' => $orientation,
            'margin-left' => $this->inchesToMm($this->spreadsheet->getActiveSheet()->getPageMargins()->getLeft()),
            'margin-right' => $this->inchesToMm($this->spreadsheet->getActiveSheet()->getPageMargins()->getRight()),
            'margin-top' => $this->inchesToMm($this->spreadsheet->getActiveSheet()->getPageMargins()->getTop()),
            'margin-bottom' => $this->inchesToMm($this->spreadsheet->getActiveSheet()->getPageMargins()->getBottom()),
        ]);

        //  Document info
        $pdf->SetTitle($this->spreadsheet->getProperties()->getTitle());
        $pdf->SetAuthor($this->spreadsheet->getProperties()->getCreator());
        $pdf->SetSubject($this->spreadsheet->getProperties()->getSubject());
        $pdf->SetKeywords($this->spreadsheet->getProperties()->getKeywords());
        $pdf->SetCreator($this->spreadsheet->getProperties()->getCreator());

        $html = $this->generateHTMLAll();
        $bodyLocation = strpos($html, self::SIMULATED_BODY_START);
        if ($bodyLocation === false) {
            $bodyLocation = strpos($html, self::BODY_TAG);
            if ($bodyLocation !== false) {
                $bodyLocation += strlen(self::BODY_TAG);
            }
        }
        // Make sure first data presented to Mpdf includes body tag
        //   (and any htmlpageheader/htmlpagefooter tags)
        //   so that Mpdf doesn't parse it as content. Issue 2432.
        if ($bodyLocation !== false) {
            $pdf->WriteHTML(substr($html, 0, $bodyLocation));
            $html = substr($html, $bodyLocation);
        }
        foreach (explode("\n", $html) as $line) {
            $pdf->WriteHTML("$line\n");
        }

        //  Write to file
        /** @var string */
        $str = $pdf->Output('', 'S');
        fwrite($fileHandle, $str);

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
            if (preg_match('/Providing an empty string is deprecated/', $errstr) === 1) {
                return true;
            }
        }

        return false; // continue error handling
    }

    /**
     * Convert inches to mm.
     */
    private function inchesToMm(float $inches): float
    {
        return $inches * 25.4;
    }
}
