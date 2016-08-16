<?php

namespace PhpSpreadsheet\Writer;

/**
 * Copyright (c) 2006 - 2015 PhpSpreadsheet
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PhpSpreadsheet
 * @copyright  Copyright (c) 2006 - 2015 PhpSpreadsheet (https://github.com/PHPOffice/PhpSpreadsheet)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */
class OpenDocument extends BaseWriter implements IWriter
{
    /**
     * Private writer parts
     *
     * @var OpenDocument\WriterPart[]
     */
    private $writerParts = array();

    /**
     * Private PhpSpreadsheet
     *
     * @var PhpSpreadsheet
     */
    private $spreadSheet;

    /**
     * Create a new OpenDocument
     *
     * @param \PhpSpreadsheet\SpreadSheet $spreadsheet
     */
    public function __construct(\PhpSpreadsheet\SpreadSheet $spreadsheet = null)
    {
        $this->setPhpSpreadsheet($spreadsheet);

        $writerPartsArray = array(
            'content'    => \PhpSpreadsheet\Writer\OpenDocument\Content::class,
            'meta'       => \PhpSpreadsheet\Writer\OpenDocument\Meta::class,
            'meta_inf'   => \PhpSpreadsheet\Writer\OpenDocument\MetaInf::class,
            'mimetype'   => \PhpSpreadsheet\Writer\OpenDocument\Mimetype::class,
            'settings'   => \PhpSpreadsheet\Writer\OpenDocument\Settings::class,
            'styles'     => \PhpSpreadsheet\Writer\OpenDocument\Styles::class,
            'thumbnails' => \PhpSpreadsheet\Writer\OpenDocument\Thumbnails::class,
        );

        foreach ($writerPartsArray as $writer => $class) {
            $this->writerParts[$writer] = new $class($this);
        }
    }

    /**
     * Get writer part
     *
     * @param  string  $pPartName  Writer part name
     * @return \PhpSpreadsheet\Writer\Excel2007\WriterPart
     */
    public function getWriterPart($pPartName = '')
    {
        if ($pPartName != '' && isset($this->writerParts[strtolower($pPartName)])) {
            return $this->writerParts[strtolower($pPartName)];
        } else {
            return null;
        }
    }

    /**
     * Save PhpSpreadsheet to file
     *
     * @param  string  $pFilename
     * @throws \PhpSpreadsheet\Writer\Exception
     */
    public function save($pFilename = null)
    {
        if (!$this->spreadSheet) {
            throw new \PhpSpreadsheet\Writer\Exception('PhpSpreadsheet object unassigned.');
        }

        // garbage collect
        $this->spreadSheet->garbageCollect();

        // If $pFilename is php://output or php://stdout, make it a temporary file...
        $originalFilename = $pFilename;
        if (strtolower($pFilename) == 'php://output' || strtolower($pFilename) == 'php://stdout') {
            $pFilename = @tempnam(\PhpSpreadsheet\Shared\File::sysGetTempDir(), 'phpxltmp');
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
            throw new \PhpSpreadsheet\Writer\Exception("Could not close zip file $pFilename.");
        }

        // If a temporary file was used, copy it to the correct file stream
        if ($originalFilename != $pFilename) {
            if (copy($pFilename, $originalFilename) === false) {
                throw new \PhpSpreadsheet\Writer\Exception("Could not copy temporary zip file $pFilename to $originalFilename.");
            }
            @unlink($pFilename);
        }
    }

    /**
     * Create zip object
     *
     * @param string $pFilename
     * @throws \PhpSpreadsheet\Writer\Exception
     * @return ZipArchive
     */
    private function createZip($pFilename)
    {
        // Create new ZIP file and open it for writing
        $zipClass = \PhpSpreadsheet\Settings::getZipClass();
        $objZip = new $zipClass();

        // Retrieve OVERWRITE and CREATE constants from the instantiated zip class
        // This method of accessing constant values from a dynamic class should work with all appropriate versions of PHP
        $ro = new ReflectionObject($objZip);
        $zipOverWrite = $ro->getConstant('OVERWRITE');
        $zipCreate = $ro->getConstant('CREATE');

        if (file_exists($pFilename)) {
            unlink($pFilename);
        }
        // Try opening the ZIP file
        if ($objZip->open($pFilename, $zipOverWrite) !== true) {
            if ($objZip->open($pFilename, $zipCreate) !== true) {
                throw new \PhpSpreadsheet\Writer\Exception("Could not open $pFilename for writing.");
            }
        }

        return $objZip;
    }

    /**
     * Get PhpSpreadsheet object
     *
     * @return PhpSpreadsheet
     * @throws \PhpSpreadsheet\Writer\Exception
     */
    public function getPhpSpreadsheet()
    {
        if ($this->spreadSheet !== null) {
            return $this->spreadSheet;
        } else {
            throw new \PhpSpreadsheet\Writer\Exception('No PhpSpreadsheet assigned.');
        }
    }

    /**
     * Set PhpSpreadsheet object
     *
     * @param  \PhpSpreadsheet\Spreadsheet $spreadsheet  PhpSpreadsheet object
     * @throws \PhpSpreadsheet\Writer\Exception
     * @return Excel2007
     */
    public function setPhpSpreadsheet(\PhpSpreadsheet\SpreadSheet $spreadsheet = null)
    {
        $this->spreadSheet = $spreadsheet;
        return $this;
    }
}
