<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Ods;

use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;

class MetaInf extends WriterPart
{
    /** @var array<string, string> */
    private array $imageFiles = [];

    /** @param array<string, string> $imageFiles */
    public function setImageFiles(array $imageFiles): void
    {
        $this->imageFiles = $imageFiles;
    }

    /**
     * Write META-INF/manifest.xml to XML format.
     *
     * @return string XML Output
     */
    public function write(): string
    {
        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        }

        // XML header
        $objWriter->startDocument('1.0', 'UTF-8');

        // Manifest
        $objWriter->startElement('manifest:manifest');
        $objWriter->writeAttribute('xmlns:manifest', 'urn:oasis:names:tc:opendocument:xmlns:manifest:1.0');
        $objWriter->writeAttribute('manifest:version', '1.2');

        $objWriter->startElement('manifest:file-entry');
        $objWriter->writeAttribute('manifest:full-path', '/');
        $objWriter->writeAttribute('manifest:version', '1.2');
        $objWriter->writeAttribute('manifest:media-type', 'application/vnd.oasis.opendocument.spreadsheet');
        $objWriter->endElement();
        $objWriter->startElement('manifest:file-entry');
        $objWriter->writeAttribute('manifest:full-path', 'meta.xml');
        $objWriter->writeAttribute('manifest:media-type', 'text/xml');
        $objWriter->endElement();
        $objWriter->startElement('manifest:file-entry');
        $objWriter->writeAttribute('manifest:full-path', 'settings.xml');
        $objWriter->writeAttribute('manifest:media-type', 'text/xml');
        $objWriter->endElement();
        $objWriter->startElement('manifest:file-entry');
        $objWriter->writeAttribute('manifest:full-path', 'content.xml');
        $objWriter->writeAttribute('manifest:media-type', 'text/xml');
        $objWriter->endElement();
        $objWriter->startElement('manifest:file-entry');
        $objWriter->writeAttribute('manifest:full-path', 'Thumbnails/thumbnail.png');
        $objWriter->writeAttribute('manifest:media-type', 'image/png');
        $objWriter->endElement();
        $objWriter->startElement('manifest:file-entry');
        $objWriter->writeAttribute('manifest:full-path', 'styles.xml');
        $objWriter->writeAttribute('manifest:media-type', 'text/xml');
        $objWriter->endElement();

        // Add manifest entries for all image files
        foreach ($this->imageFiles as $imagePath => $imageContent) {
            $objWriter->startElement('manifest:file-entry');
            $objWriter->writeAttribute('manifest:full-path', $imagePath);

            // Determine MIME type based on extension
            $extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
            $mimeType = match ($extension) {
                'png' => 'image/png',
                'jpg', 'jpeg' => 'image/jpeg',
                'gif' => 'image/gif',
                default => 'application/octet-stream',
            };

            $objWriter->writeAttribute('manifest:media-type', $mimeType);
            $objWriter->endElement(); // manifest:file-entry
        }

        $objWriter->endElement();

        return $objWriter->getData();
    }
}
