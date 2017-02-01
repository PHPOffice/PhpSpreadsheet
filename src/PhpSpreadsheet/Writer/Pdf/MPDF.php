<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Pdf;

/**
 *  Copyright (c) 2006 - 2015 PhpSpreadsheet.
 *
 *  This library is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU Lesser General Public
 *  License as published by the Free Software Foundation; either
 *  version 2.1 of the License, or (at your option) any later version.
 *
 *  This library is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 *  Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU Lesser General Public
 *  License along with this library; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 *
 *  @category    PhpSpreadsheet
 *
 *  @copyright   Copyright (c) 2006 - 2015 PhpSpreadsheet (https://github.com/PHPOffice/PhpSpreadsheet)
 *  @license     http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 */
class MPDF extends Core implements \PhpOffice\PhpSpreadsheet\Writer\IWriter
{
    /**
     * Save Spreadsheet to file.
     *
     * @param string $pFilename Name of the file to save as
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function save($pFilename = null)
    {
        $fileHandle = parent::prepareForSave($pFilename);

        //  Default PDF paper size
        $paperSize = 'LETTER'; //    Letter    (8.5 in. by 11 in.)

        //  Check for paper size and page orientation
        if (null === $this->getSheetIndex()) {
            $orientation = ($this->spreadsheet->getSheet(0)->getPageSetup()->getOrientation()
                == \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE) ? 'L' : 'P';
            $printPaperSize = $this->spreadsheet->getSheet(0)->getPageSetup()->getPaperSize();
            $printMargins = $this->spreadsheet->getSheet(0)->getPageMargins();
        } else {
            $orientation = ($this->spreadsheet->getSheet($this->getSheetIndex())->getPageSetup()->getOrientation()
                == \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE) ? 'L' : 'P';
            $printPaperSize = $this->spreadsheet->getSheet($this->getSheetIndex())->getPageSetup()->getPaperSize();
            $printMargins = $this->spreadsheet->getSheet($this->getSheetIndex())->getPageMargins();
        }
        $this->setOrientation($orientation);

        //  Override Page Orientation
        if (null !== $this->getOrientation()) {
            $orientation = ($this->getOrientation() == \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_DEFAULT)
                ? \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT
                : $this->getOrientation();
        }
        $orientation = strtoupper($orientation);

        //  Override Paper Size
        if (null !== $this->getPaperSize()) {
            $printPaperSize = $this->getPaperSize();
        }

        if (isset(self::$paperSizes[$printPaperSize])) {
            $paperSize = self::$paperSizes[$printPaperSize];
        }

        //  Create PDF
        $pdf = new \mPDF();
        $ortmp = $orientation;
        $pdf->_setPageSize(strtoupper($paperSize), $ortmp);
        $pdf->DefOrientation = $orientation;
        $pdf->AddPage($orientation);

        //  Document info
        $pdf->SetTitle($this->spreadsheet->getProperties()->getTitle());
        $pdf->SetAuthor($this->spreadsheet->getProperties()->getCreator());
        $pdf->SetSubject($this->spreadsheet->getProperties()->getSubject());
        $pdf->SetKeywords($this->spreadsheet->getProperties()->getKeywords());
        $pdf->SetCreator($this->spreadsheet->getProperties()->getCreator());

        $pdf->WriteHTML(
            $this->generateHTMLHeader(false) .
            $this->generateSheetData() .
            $this->generateHTMLFooter()
        );

        //  Write to file
        fwrite($fileHandle, $pdf->Output('', 'S'));

        parent::restoreStateAfterSave($fileHandle);
    }
}
