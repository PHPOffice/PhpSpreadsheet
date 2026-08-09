<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Ods;

use PhpOffice\PhpSpreadsheet\Helper\Dimension;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Worksheet\BaseDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing as WorksheetDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Drawing extends WriterPart
{
    /** @var array<string, string> Image files to be added to ODS */
    private array $imageFiles = [];

    /** @var int Counter for image numbering */
    private int $imageCounter = 0;

    /**
     * Required by WriterPart abstract class.
     */
    public function write(): string
    {
        return '';
    }

    /**
     * Reset the drawing writer state.
     */
    public function reset(): void
    {
        $this->imageFiles = [];
        $this->imageCounter = 0;
    }

    /**
     * Collect all drawings from a worksheet and prepare image files.
     *
     * @return array<string, string> Array of drawing image files [path => content]
     */
    public function collectDrawings(Worksheet $worksheet): array
    {
        $drawingCollection = $worksheet->getDrawingCollection();

        if (count($drawingCollection) === 0) {
            return [];
        }

        $drawings = [];
        foreach ($drawingCollection as $drawing) {
            ++$this->imageCounter;

            if ($drawing instanceof WorksheetDrawing) {
                $filename = $drawing->getPath();
                $filename = str_replace('\\', '/', $filename);
                $imageContents = @file_get_contents($filename);
                if ($imageContents !== false) {
                    $extension = $drawing->getExtension();
                    $imagePath = "Pictures/image{$this->imageCounter}.{$extension}";
                    $drawings[$imagePath] = $imageContents;
                    $this->imageFiles[$imagePath] = $imageContents;
                }
            } elseif ($drawing instanceof MemoryDrawing) {
                $extension = 'png';
                switch ($drawing->getRenderingFunction()) {
                    case MemoryDrawing::RENDERING_JPEG:
                        $extension = 'jpg';

                        break;
                    case MemoryDrawing::RENDERING_GIF:
                        $extension = 'gif';

                        break;
                }

                ob_start();
                $gdImage = $drawing->getImageResource();
                if ($gdImage !== null) {
                    switch ($drawing->getRenderingFunction()) {
                        case MemoryDrawing::RENDERING_JPEG:
                            imagejpeg($gdImage);

                            break;
                        case MemoryDrawing::RENDERING_GIF:
                            imagegif($gdImage);

                            break;
                        //case MemoryDrawing::RENDERING_PNG:
                        default:
                            imagepng($gdImage);

                            break;
                    }
                    $imageContents = ob_get_contents();
                    ob_end_clean();

                    if ($imageContents !== false && $imageContents !== '') {
                        $imagePath = "Pictures/image{$this->imageCounter}.{$extension}";
                        $drawings[$imagePath] = $imageContents;
                        $this->imageFiles[$imagePath] = $imageContents;
                    }
                } else {
                    ob_end_clean();
                }
            }
        }

        return $drawings;
    }

    /**
     * Get all collected image files.
     *
     * @return array<string, string>
     */
    public function getAllImageFiles(): array
    {
        return $this->imageFiles;
    }

    /**
     * Write drawing frames in content.xml within a table cell.
     */
    public function writeDrawingFrame(XMLWriter $objWriter, BaseDrawing $drawing, int $imageIndex, string $sheetTitle): void
    {
        $extension = 'png';
        if ($drawing instanceof WorksheetDrawing) {
            $extension = $drawing->getExtension();
        } elseif ($drawing instanceof MemoryDrawing) {
            switch ($drawing->getRenderingFunction()) {
                case MemoryDrawing::RENDERING_JPEG:
                    $extension = 'jpg';

                    break;
                case MemoryDrawing::RENDERING_GIF:
                    $extension = 'gif';

                    break;
            }
        }

        // Calculate dimensions in cm (ODS uses cm, PhpSpreadsheet uses pixels)
        // 1 pixel = 0.0264583333 cm (at 96 DPI)
        $widthCm = round($drawing->getWidth() / Dimension::ABSOLUTE_UNITS[Dimension::UOM_CENTIMETERS], 4);
        $heightCm = round($drawing->getHeight() / Dimension::ABSOLUTE_UNITS[Dimension::UOM_CENTIMETERS], 4);

        // Write draw:frame
        $objWriter->startElement('draw:frame');
        $objWriter->writeAttribute('draw:name', $drawing->getName() ?: "Image {$imageIndex}");
        $objWriter->writeAttribute('draw:z-index', (string) $imageIndex);
        $objWriter->writeAttribute('svg:width', "{$widthCm}cm");
        $objWriter->writeAttribute('svg:height', "{$heightCm}cm");
        $objWriter->writeAttribute('draw:style-name', 'gr1');

        // Write draw:image
        $objWriter->startElement('draw:image');
        $objWriter->writeAttribute('xlink:href', "Pictures/image{$imageIndex}.{$extension}");
        $objWriter->writeAttribute('xlink:type', 'simple');
        $objWriter->writeAttribute('xlink:show', 'embed');
        $objWriter->writeAttribute('xlink:actuate', 'onLoad');

        // Empty text element required by ODS spec
        $objWriter->startElement('text:p');
        $objWriter->endElement(); // text:p

        $objWriter->endElement(); // draw:image
        $objWriter->endElement(); // draw:frame
    }
}
