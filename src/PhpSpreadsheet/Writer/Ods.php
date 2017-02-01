<?php

namespace PhpOffice\PhpSpreadsheet\Writer;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

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
class Ods extends BaseWriter implements IWriter
{
    /**
     * Private writer parts.
     *
     * @var Ods\WriterPart[]
     */
    private $writerParts = [];

    /**
     * Private PhpSpreadsheet.
     *
     * @var PhpSpreadsheet
     */
    private $spreadSheet;

    /**
     * Create a new Ods.
     *
     * @param \PhpOffice\PhpSpreadsheet\SpreadSheet $spreadsheet
     */
    public function __construct(\PhpOffice\PhpSpreadsheet\SpreadSheet $spreadsheet = null)
    {
        $this->setSpreadsheet($spreadsheet);

        $writerPartsArray = [
            'content' => \PhpOffice\PhpSpreadsheet\Writer\Ods\Content::class,
            'meta' => \PhpOffice\PhpSpreadsheet\Writer\Ods\Meta::class,
            'meta_inf' => \PhpOffice\PhpSpreadsheet\Writer\Ods\MetaInf::class,
            'mimetype' => \PhpOffice\PhpSpreadsheet\Writer\Ods\Mimetype::class,
            'settings' => \PhpOffice\PhpSpreadsheet\Writer\Ods\Settings::class,
            'styles' => \PhpOffice\PhpSpreadsheet\Writer\Ods\Styles::class,
            'thumbnails' => \PhpOffice\PhpSpreadsheet\Writer\Ods\Thumbnails::class,
        ];

        foreach ($writerPartsArray as $writer => $class) {
            $this->writerParts[$writer] = new $class($this);
        }
    }

    /**
     * Get writer part.
     *
     * @param string $pPartName Writer part name
     *
     * @return Ods\WriterPart|null
     */
    public function getWriterPart($pPartName = '')
    {
        if ($pPartName != '' && isset($this->writerParts[strtolower($pPartName)])) {
            return $this->writerParts[strtolower($pPartName)];
        }

        return null;
    }

    /**
     * Save PhpSpreadsheet to file.
     *
     * @param string $pFilename
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function save($pFilename = null)
    {
        if (!$this->spreadSheet) {
            throw new \PhpOffice\PhpSpreadsheet\Writer\Exception('PhpSpreadsheet object unassigned.');
        }

        // garbage collect
        $this->spreadSheet->garbageCollect();

        // If $pFilename is php://output or php://stdout, make it a temporary file...
        $originalFilename = $pFilename;
        if (strtolower($pFilename) == 'php://output' || strtolower($pFilename) == 'php://stdout') {
            $pFilename = @tempnam(\PhpOffice\PhpSpreadsheet\Shared\File::sysGetTempDir(), 'phpxltmp');
            if ($pFilename == '') {
                $pFilename = $originalFilename;
            }
        }

        $objZip = $this->createZip($pFilename);

        $objZip->addFromString('META-INF/manifest.xml', $this->getWriterPart('meta_inf')->writeManifest());
        $objZip->addFromString('Thumbnails/thumbnail.png', $this->getWriterPart('thumbnails')->writeThumbnail());
        $objZip->addFromString('content.xml', $this->getWriterPart('content')->write());
        $objZip->addFromString('meta.xml', $this->getWriterPart('meta')->write());
        $objZip->addFromString('mimetype', $this->getWriterPart('mimetype')->write());
        $objZip->addFromString('settings.xml', $this->getWriterPart('settings')->write());
        $objZip->addFromString('styles.xml', $this->getWriterPart('styles')->write());

        // Close file
        if ($objZip->close() === false) {
            throw new \PhpOffice\PhpSpreadsheet\Writer\Exception("Could not close zip file $pFilename.");
        }

        // If a temporary file was used, copy it to the correct file stream
        if ($originalFilename != $pFilename) {
            if (copy($pFilename, $originalFilename) === false) {
                throw new \PhpOffice\PhpSpreadsheet\Writer\Exception("Could not copy temporary zip file $pFilename to $originalFilename.");
            }
            @unlink($pFilename);
        }
    }

    /**
     * Create zip object.
     *
     * @param string $pFilename
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     *
     * @return ZipArchive
     */
    private function createZip($pFilename)
    {
        // Create new ZIP file and open it for writing
        $zipClass = \PhpOffice\PhpSpreadsheet\Settings::getZipClass();
        $objZip = new $zipClass();

        // Retrieve OVERWRITE and CREATE constants from the instantiated zip class
        // This method of accessing constant values from a dynamic class should work with all appropriate versions of PHP
        $ro = new \ReflectionObject($objZip);
        $zipOverWrite = $ro->getConstant('OVERWRITE');
        $zipCreate = $ro->getConstant('CREATE');

        if (file_exists($pFilename)) {
            unlink($pFilename);
        }
        // Try opening the ZIP file
        if ($objZip->open($pFilename, $zipOverWrite) !== true) {
            if ($objZip->open($pFilename, $zipCreate) !== true) {
                throw new \PhpOffice\PhpSpreadsheet\Writer\Exception("Could not open $pFilename for writing.");
            }
        }

        return $objZip;
    }

    /**
     * Get Spreadsheet object.
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     *
     * @return Spreadsheet
     */
    public function getSpreadsheet()
    {
        if ($this->spreadSheet !== null) {
            return $this->spreadSheet;
        }
        throw new \PhpOffice\PhpSpreadsheet\Writer\Exception('No PhpSpreadsheet assigned.');
    }

    /**
     * Set Spreadsheet object.
     *
     * @param \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet PhpSpreadsheet object
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     *
     * @return self
     */
    public function setSpreadsheet(\PhpOffice\PhpSpreadsheet\SpreadSheet $spreadsheet = null)
    {
        $this->spreadSheet = $spreadsheet;

        return $this;
    }
}
