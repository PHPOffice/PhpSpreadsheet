<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Comment;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Namespaces;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class Comments extends WriterPart
{
    private const VALID_HORIZONTAL_ALIGNMENT = [
        Alignment::HORIZONTAL_CENTER,
        Alignment::HORIZONTAL_DISTRIBUTED,
        Alignment::HORIZONTAL_JUSTIFY,
        Alignment::HORIZONTAL_LEFT,
        Alignment::HORIZONTAL_RIGHT,
    ];

    /**
     * Write comments to XML format.
     *
     * @return string XML Output
     */
    public function writeComments(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet): string
    {
        // Create XML writer
        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        }

        // XML header
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        // Comments cache
        $comments = $worksheet->getComments();

        // Authors cache
        $authors = [];
        $authorId = 0;
        foreach ($comments as $comment) {
            if (!isset($authors[$comment->getAuthor()])) {
                $authors[$comment->getAuthor()] = $authorId++;
            }
        }

        // comments
        $objWriter->startElement('comments');
        $objWriter->writeAttribute('xmlns', Namespaces::MAIN);

        // Loop through authors
        $objWriter->startElement('authors');
        foreach ($authors as $author => $index) {
            $objWriter->writeElement('author', $author);
        }
        $objWriter->endElement();

        // Loop through comments
        $objWriter->startElement('commentList');
        foreach ($comments as $key => $value) {
            $this->writeComment($objWriter, $key, $value, $authors);
        }
        $objWriter->endElement();

        $objWriter->endElement();

        // Return
        return $objWriter->getData();
    }

    /**
     * Write comment to XML format.
     *
     * @param string $cellReference Cell reference
     * @param Comment $comment Comment
     * @param array $authors Array of authors
     */
    private function writeComment(XMLWriter $objWriter, string $cellReference, Comment $comment, array $authors): void
    {
        // comment
        $objWriter->startElement('comment');
        $objWriter->writeAttribute('ref', $cellReference);
        $objWriter->writeAttribute('authorId', $authors[$comment->getAuthor()]);

        // text
        $objWriter->startElement('text');
        $this->getParentWriter()->getWriterPartstringtable()->writeRichText($objWriter, $comment->getText());
        $objWriter->endElement();

        $objWriter->endElement();
    }

    /**
     * Write VML comments to XML format.
     *
     * @return string XML Output
     */
    public function writeVMLComments(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet): string
    {
        // Create XML writer
        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        }

        // XML header
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        // Comments cache
        $comments = $worksheet->getComments();

        // xml
        $objWriter->startElement('xml');
        $objWriter->writeAttribute('xmlns:v', Namespaces::URN_VML);
        $objWriter->writeAttribute('xmlns:o', Namespaces::URN_MSOFFICE);
        $objWriter->writeAttribute('xmlns:x', Namespaces::URN_EXCEL);

        // o:shapelayout
        $objWriter->startElement('o:shapelayout');
        $objWriter->writeAttribute('v:ext', 'edit');

        // o:idmap
        $objWriter->startElement('o:idmap');
        $objWriter->writeAttribute('v:ext', 'edit');
        $objWriter->writeAttribute('data', '1');
        $objWriter->endElement();

        $objWriter->endElement();

        // v:shapetype
        $objWriter->startElement('v:shapetype');
        $objWriter->writeAttribute('id', '_x0000_t202');
        $objWriter->writeAttribute('coordsize', '21600,21600');
        $objWriter->writeAttribute('o:spt', '202');
        $objWriter->writeAttribute('path', 'm,l,21600r21600,l21600,xe');

        // v:stroke
        $objWriter->startElement('v:stroke');
        $objWriter->writeAttribute('joinstyle', 'miter');
        $objWriter->endElement();

        // v:path
        $objWriter->startElement('v:path');
        $objWriter->writeAttribute('gradientshapeok', 't');
        $objWriter->writeAttribute('o:connecttype', 'rect');
        $objWriter->endElement();

        $objWriter->endElement();

        // Loop through comments
        foreach ($comments as $key => $value) {
            $this->writeVMLComment($objWriter, $key, $value);
        }

        $objWriter->endElement();

        // Return
        return $objWriter->getData();
    }

    /**
     * Write VML comment to XML format.
     *
     * @param string $cellReference Cell reference, eg: 'A1'
     * @param Comment $comment Comment
     */
    private function writeVMLComment(XMLWriter $objWriter, string $cellReference, Comment $comment): void
    {
        // Metadata
        [$column, $row] = Coordinate::indexesFromString($cellReference);
        $id = 1024 + $column + $row;
        $id = substr("$id", 0, 4);

        // v:shape
        $objWriter->startElement('v:shape');
        $objWriter->writeAttribute('id', '_x0000_s' . $id);
        $objWriter->writeAttribute('type', '#_x0000_t202');
        $objWriter->writeAttribute('style', 'position:absolute;margin-left:' . $comment->getMarginLeft() . ';margin-top:' . $comment->getMarginTop() . ';width:' . $comment->getWidth() . ';height:' . $comment->getHeight() . ';z-index:1;visibility:' . ($comment->getVisible() ? 'visible' : 'hidden'));
        $objWriter->writeAttribute('fillcolor', '#' . $comment->getFillColor()->getRGB());
        $objWriter->writeAttribute('o:insetmode', 'auto');

        // v:fill
        $objWriter->startElement('v:fill');
        $objWriter->writeAttribute('color2', '#' . $comment->getFillColor()->getRGB());
        if ($comment->hasBackgroundImage()) {
            $bgImage = $comment->getBackgroundImage();
            $objWriter->writeAttribute('o:relid', 'rId' . $bgImage->getImageIndex());
            $objWriter->writeAttribute('o:title', $bgImage->getName());
            $objWriter->writeAttribute('type', 'frame');
        }
        $objWriter->endElement();

        // v:shadow
        $objWriter->startElement('v:shadow');
        $objWriter->writeAttribute('on', 't');
        $objWriter->writeAttribute('color', 'black');
        $objWriter->writeAttribute('obscured', 't');
        $objWriter->endElement();

        // v:path
        $objWriter->startElement('v:path');
        $objWriter->writeAttribute('o:connecttype', 'none');
        $objWriter->endElement();

        // v:textbox
        $textBoxArray = [Comment::TEXTBOX_DIRECTION_RTL => 'rtl', Comment::TEXTBOX_DIRECTION_LTR => 'ltr'];
        $textboxRtl = $textBoxArray[strtolower($comment->getTextBoxDirection())] ?? 'auto';
        $objWriter->startElement('v:textbox');
        $objWriter->writeAttribute('style', "mso-direction-alt:$textboxRtl");

        // div
        $objWriter->startElement('div');
        $objWriter->writeAttribute('style', ($textboxRtl === 'rtl' ? 'text-align:right;direction:rtl' : 'text-align:left'));
        $objWriter->endElement();

        $objWriter->endElement();

        // x:ClientData
        $objWriter->startElement('x:ClientData');
        $objWriter->writeAttribute('ObjectType', 'Note');

        // x:MoveWithCells
        $objWriter->writeElement('x:MoveWithCells', '');

        // x:SizeWithCells
        $objWriter->writeElement('x:SizeWithCells', '');

        // x:AutoFill
        $objWriter->writeElement('x:AutoFill', 'False');

        // x:TextHAlign horizontal alignment of text
        $alignment = strtolower($comment->getAlignment());
        if (in_array($alignment, self::VALID_HORIZONTAL_ALIGNMENT, true)) {
            $objWriter->writeElement('x:TextHAlign', ucfirst($alignment));
        }

        // x:Row
        $objWriter->writeElement('x:Row', (string) ($row - 1));

        // x:Column
        $objWriter->writeElement('x:Column', (string) ($column - 1));

        $objWriter->endElement();

        $objWriter->endElement();
    }
}
