<?php

namespace PhpOffice\PhpSpreadsheet\Writer;

use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Content;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Meta;
use PhpOffice\PhpSpreadsheet\Writer\Ods\MetaInf;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Mimetype;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Settings;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Styles;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Thumbnails;
use ZipStream\Exception\OverflowException;
use ZipStream\Option\Archive;
use ZipStream\ZipStream;

class Ods extends BaseWriter
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
     * @var Spreadsheet
     */
    private $spreadSheet;

    /**
     * Create a new Ods.
     */
    public function __construct(Spreadsheet $spreadsheet)
    {
        $this->setSpreadsheet($spreadsheet);

        $writerPartsArray = [
            'content' => Content::class,
            'meta' => Meta::class,
            'meta_inf' => MetaInf::class,
            'mimetype' => Mimetype::class,
            'settings' => Settings::class,
            'styles' => Styles::class,
            'thumbnails' => Thumbnails::class,
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
     * @return null|Ods\WriterPart
     */
    public function getWriterPart($pPartName)
    {
        if ($pPartName != '' && isset($this->writerParts[strtolower($pPartName)])) {
            return $this->writerParts[strtolower($pPartName)];
        }

        return null;
    }

    /**
     * Save PhpSpreadsheet to file.
     *
     * @param resource|string $pFilename
     */
    public function save($pFilename): void
    {
        if (!$this->spreadSheet) {
            throw new WriterException('PhpSpreadsheet object unassigned.');
        }

        // garbage collect
        $this->spreadSheet->garbageCollect();

        $this->openFileHandle($pFilename);

        $zip = $this->createZip();

        $zip->addFile('META-INF/manifest.xml', $this->getWriterPart('meta_inf')->writeManifest());
        $zip->addFile('Thumbnails/thumbnail.png', $this->getWriterPart('thumbnails')->writeThumbnail());
        $zip->addFile('content.xml', $this->getWriterPart('content')->write());
        $zip->addFile('meta.xml', $this->getWriterPart('meta')->write());
        $zip->addFile('mimetype', $this->getWriterPart('mimetype')->write());
        $zip->addFile('settings.xml', $this->getWriterPart('settings')->write());
        $zip->addFile('styles.xml', $this->getWriterPart('styles')->write());

        // Close file
        try {
            $zip->finish();
        } catch (OverflowException $e) {
            throw new WriterException('Could not close resource.');
        }

        $this->maybeCloseFileHandle();
    }

    /**
     * Create zip object.
     *
     * @return ZipStream
     */
    private function createZip()
    {
        // Try opening the ZIP file
        if (!is_resource($this->fileHandle)) {
            throw new WriterException('Could not open resource for writing.');
        }

        // Create new ZIP stream
        $options = new Archive();
        $options->setEnableZip64(false);
        $options->setOutputStream($this->fileHandle);

        return new ZipStream(null, $options);
    }

    /**
     * Get Spreadsheet object.
     *
     * @return Spreadsheet
     */
    public function getSpreadsheet()
    {
        if ($this->spreadSheet !== null) {
            return $this->spreadSheet;
        }

        throw new WriterException('No PhpSpreadsheet assigned.');
    }

    /**
     * Set Spreadsheet object.
     *
     * @param Spreadsheet $spreadsheet PhpSpreadsheet object
     *
     * @return $this
     */
    public function setSpreadsheet(Spreadsheet $spreadsheet)
    {
        $this->spreadSheet = $spreadsheet;

        return $this;
    }
}
