<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Ods;

/**
 * Copyright (c) 2006 - 2015 PhpSpreadsheet.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @category   PhpSpreadsheet
 *
 * @copyright  Copyright (c) 2006 - 2015 PhpSpreadsheet (https://github.com/PHPOffice/PhpSpreadsheet)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 */
class Meta extends WriterPart
{
    /**
     * Write meta.xml to XML format.
     *
     * @param \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     *
     * @return string XML Output
     */
    public function write(\PhpOffice\PhpSpreadsheet\SpreadSheet $spreadsheet = null)
    {
        if (!$spreadsheet) {
            $spreadsheet = $this->getParentWriter()->getSpreadsheet();
        }

        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new \PhpOffice\PhpSpreadsheet\Shared\XMLWriter(\PhpOffice\PhpSpreadsheet\Shared\XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new \PhpOffice\PhpSpreadsheet\Shared\XMLWriter(\PhpOffice\PhpSpreadsheet\Shared\XMLWriter::STORAGE_MEMORY);
        }

        // XML header
        $objWriter->startDocument('1.0', 'UTF-8');

        // Meta
        $objWriter->startElement('office:document-meta');

        $objWriter->writeAttribute('xmlns:office', 'urn:oasis:names:tc:opendocument:xmlns:office:1.0');
        $objWriter->writeAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');
        $objWriter->writeAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');
        $objWriter->writeAttribute('xmlns:meta', 'urn:oasis:names:tc:opendocument:xmlns:meta:1.0');
        $objWriter->writeAttribute('xmlns:ooo', 'http://openoffice.org/2004/office');
        $objWriter->writeAttribute('xmlns:grddl', 'http://www.w3.org/2003/g/data-view#');
        $objWriter->writeAttribute('office:version', '1.2');

        $objWriter->startElement('office:meta');

        $objWriter->writeElement('meta:initial-creator', $spreadsheet->getProperties()->getCreator());
        $objWriter->writeElement('dc:creator', $spreadsheet->getProperties()->getCreator());
        $objWriter->writeElement('meta:creation-date', date(DATE_W3C, $spreadsheet->getProperties()->getCreated()));
        $objWriter->writeElement('dc:date', date(DATE_W3C, $spreadsheet->getProperties()->getCreated()));
        $objWriter->writeElement('dc:title', $spreadsheet->getProperties()->getTitle());
        $objWriter->writeElement('dc:description', $spreadsheet->getProperties()->getDescription());
        $objWriter->writeElement('dc:subject', $spreadsheet->getProperties()->getSubject());
        $keywords = explode(' ', $spreadsheet->getProperties()->getKeywords());
        foreach ($keywords as $keyword) {
            $objWriter->writeElement('meta:keyword', $keyword);
        }

        //<meta:document-statistic meta:table-count="XXX" meta:cell-count="XXX" meta:object-count="XXX"/>
        $objWriter->startElement('meta:user-defined');
        $objWriter->writeAttribute('meta:name', 'Company');
        $objWriter->writeRaw($spreadsheet->getProperties()->getCompany());
        $objWriter->endElement();

        $objWriter->startElement('meta:user-defined');
        $objWriter->writeAttribute('meta:name', 'category');
        $objWriter->writeRaw($spreadsheet->getProperties()->getCategory());
        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        return $objWriter->getData();
    }
}
