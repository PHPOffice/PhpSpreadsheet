<?php

namespace PhpOffice\PhpSpreadsheet\Writer;

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
     * Private PhpSpreadsheet.
     *
     * @var Spreadsheet
     */
    private $spreadSheet;

    /**
     * @var Content
     */
    private $writerPartContent;

    /**
     * @var Meta
     */
    private $writerPartMeta;

    /**
     * @var MetaInf
     */
    private $writerPartMetaInf;

    /**
     * @var Mimetype
     */
    private $writerPartMimetype;

    /**
     * @var Settings
     */
    private $writerPartSettings;

    /**
     * @var Styles
     */
    private $writerPartStyles;

    /**
     * @var Thumbnails
     */
    private $writerPartThumbnails;

    /**
     * Create a new Ods.
     */
    public function __construct(Spreadsheet $spreadsheet)
    {
        $this->setSpreadsheet($spreadsheet);

        $this->writerPartContent = new Content($this);
        $this->writerPartMeta = new Meta($this);
        $this->writerPartMetaInf = new MetaInf($this);
        $this->writerPartMimetype = new Mimetype($this);
        $this->writerPartSettings = new Settings($this);
        $this->writerPartStyles = new Styles($this);
        $this->writerPartThumbnails = new Thumbnails($this);
    }

    public function getWriterPartContent(): Content
    {
        return $this->writerPartContent;
    }

    public function getWriterPartMeta(): Meta
    {
        return $this->writerPartMeta;
    }

    public function getWriterPartMetaInf(): MetaInf
    {
        return $this->writerPartMetaInf;
    }

    public function getWriterPartMimetype(): Mimetype
    {
        return $this->writerPartMimetype;
    }

    public function getWriterPartSettings(): Settings
    {
        return $this->writerPartSettings;
    }

    public function getWriterPartStyles(): Styles
    {
        return $this->writerPartStyles;
    }

    public function getWriterPartThumbnails(): Thumbnails
    {
        return $this->writerPartThumbnails;
    }

    /**
     * Save PhpSpreadsheet to file.
     *
     * @param resource|string $pFilename
     */
    public function save($pFilename, int $flags = 0): void
    {
        if (!$this->spreadSheet) {
            throw new WriterException('PhpSpreadsheet object unassigned.');
        }

        $this->processFlags($flags);

        // garbage collect
        $this->spreadSheet->garbageCollect();

        $this->openFileHandle($pFilename);

        $zip = $this->createZip();

        $zip->addFile('META-INF/manifest.xml', $this->getWriterPartMetaInf()->write());
        $zip->addFile('Thumbnails/thumbnail.png', $this->getWriterPartthumbnails()->write());
        $zip->addFile('content.xml', $this->getWriterPartcontent()->write());
        $zip->addFile('meta.xml', $this->getWriterPartmeta()->write());
        $zip->addFile('mimetype', $this->getWriterPartmimetype()->write());
        $zip->addFile('settings.xml', $this->getWriterPartsettings()->write());
        $zip->addFile('styles.xml', $this->getWriterPartstyles()->write());

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
