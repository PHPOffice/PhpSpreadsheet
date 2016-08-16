<?php

namespace PhpSpreadsheet\Writer\PDF;

/*  Require DomPDF library */
$pdfRendererClassFile = \PhpSpreadsheet\Settings::getPdfRendererPath() . '/dompdf_config.inc.php';
if (file_exists($pdfRendererClassFile)) {
    require_once $pdfRendererClassFile;
} else {
    throw new \PhpSpreadsheet\Writer\Exception('Unable to load PDF Rendering library');
}

/**
 *  Copyright (c) 2006 - 2015 PhpSpreadsheet
 *
 *  This library is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU Lesser General Public
 *  License as published by the Free Software Foundation; either
 *  version 2.1 of the License, or (at your option) any later version.
 *
 *  This library is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 *  Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU Lesser General Public
 *  License along with this library; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 *  @category    PhpSpreadsheet
 *  @copyright   Copyright (c) 2006 - 2015 PhpSpreadsheet (https://github.com/PHPOffice/PhpSpreadsheet)
 *  @license     http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 *  @version     ##VERSION##, ##DATE##
 */
class DomPDF extends Core implements \PhpSpreadsheet\Writer\IWriter
{
    /**
     *  Create a new DomPDF Writer instance
     *
     *  @param   \PhpSpreadsheet\Spreadsheet    $spreadsheet    Spreadsheet object
     */
    public function __construct(\PhpSpreadsheet\Spreadsheet $spreadsheet)
    {
        parent::__construct($spreadsheet);
    }

    /**
     *  Save Spreadsheet to file
     *
     *  @param   string     $pFilename   Name of the file to save as
     *  @throws  \PhpSpreadsheet\Writer\Exception
     */
    public function save($pFilename = null)
    {
        $fileHandle = parent::prepareForSave($pFilename);

        //  Default PDF paper size
        $paperSize = 'LETTER';    //    Letter    (8.5 in. by 11 in.)

        //  Check for paper size and page orientation
        if (is_null($this->getSheetIndex())) {
            $orientation = ($this->phpSpreadsheet->getSheet(0)->getPageSetup()->getOrientation()
                == \PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE) ? 'L' : 'P';
            $printPaperSize = $this->phpSpreadsheet->getSheet(0)->getPageSetup()->getPaperSize();
            $printMargins = $this->phpSpreadsheet->getSheet(0)->getPageMargins();
        } else {
            $orientation = ($this->phpSpreadsheet->getSheet($this->getSheetIndex())->getPageSetup()->getOrientation()
                == \PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE) ? 'L' : 'P';
            $printPaperSize = $this->phpSpreadsheet->getSheet($this->getSheetIndex())->getPageSetup()->getPaperSize();
            $printMargins = $this->phpSpreadsheet->getSheet($this->getSheetIndex())->getPageMargins();
        }

        $orientation = ($orientation == 'L') ? 'landscape' : 'portrait';

        //  Override Page Orientation
        if (!is_null($this->getOrientation())) {
            $orientation = ($this->getOrientation() == \PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_DEFAULT)
                ? \PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT
                : $this->getOrientation();
        }
        //  Override Paper Size
        if (!is_null($this->getPaperSize())) {
            $printPaperSize = $this->getPaperSize();
        }

        if (isset(self::$paperSizes[$printPaperSize])) {
            $paperSize = self::$paperSizes[$printPaperSize];
        }

        //  Create PDF
        $pdf = new self();
        $pdf->set_paper(strtolower($paperSize), $orientation);

        $pdf->load_html(
            $this->generateHTMLHeader(false) .
            $this->generateSheetData() .
            $this->generateHTMLFooter()
        );
        $pdf->render();

        //  Write to file
        fwrite($fileHandle, $pdf->output());

        parent::restoreStateAfterSave($fileHandle);
    }
}
