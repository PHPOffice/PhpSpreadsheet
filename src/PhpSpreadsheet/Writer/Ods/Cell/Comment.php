<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Ods\Cell;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;

/**
 * @category   PhpSpreadsheet
 *
 * @copyright  Copyright (c) 2006 - 2015 PhpSpreadsheet (https://github.com/PHPOffice/PhpSpreadsheet)
 * @author     Alexander Pervakov <frost-nzcr4@jagmort.com>
 */
class Comment
{
    public static function write(XMLWriter $objWriter, Cell $cell)
    {
        $comments = $cell->getWorksheet()->getComments();
        if (!isset($comments[$cell->getCoordinate()])) {
            return;
        }
        $comment = $comments[$cell->getCoordinate()];

        $objWriter->startElement('office:annotation');
        $objWriter->writeAttribute('svg:width', $comment->getWidth());
        $objWriter->writeAttribute('svg:height', $comment->getHeight());
        $objWriter->writeAttribute('svg:x', $comment->getMarginLeft());
        $objWriter->writeAttribute('svg:y', $comment->getMarginTop());
        $objWriter->writeElement('dc:creator', $comment->getAuthor());
        $objWriter->writeElement('text:p', $comment->getText()->getPlainText());
        $objWriter->endElement();
    }
}
