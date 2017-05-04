<?php

namespace PhpOffice\PhpSpreadsheet;

/**
 * Copyright (c) 2006 - 2016 PhpSpreadsheet.
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
 * @copyright  Copyright (c) 2006 - 2016 PhpSpreadsheet (https://github.com/PHPOffice/PhpSpreadsheet)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 */
class Comment implements IComparable
{
    /**
     * Author.
     *
     * @var string
     */
    private $author;

    /**
     * Rich text comment.
     *
     * @var RichText
     */
    private $text;

    /**
     * Comment width (CSS style, i.e. XXpx or YYpt).
     *
     * @var string
     */
    private $width = '96pt';

    /**
     * Left margin (CSS style, i.e. XXpx or YYpt).
     *
     * @var string
     */
    private $marginLeft = '59.25pt';

    /**
     * Top margin (CSS style, i.e. XXpx or YYpt).
     *
     * @var string
     */
    private $marginTop = '1.5pt';

    /**
     * Visible.
     *
     * @var bool
     */
    private $visible = false;

    /**
     * Comment height (CSS style, i.e. XXpx or YYpt).
     *
     * @var string
     */
    private $height = '55.5pt';

    /**
     * Comment fill color.
     *
     * @var Style\Color
     */
    private $fillColor;

    /**
     * Alignment.
     *
     * @var string
     */
    private $alignment;

    /**
     * Create a new Comment.
     *
     * @throws Exception
     */
    public function __construct()
    {
        // Initialise variables
        $this->author = 'Author';
        $this->text = new RichText();
        $this->fillColor = new Style\Color('FFFFFFE1');
        $this->alignment = Style\Alignment::HORIZONTAL_GENERAL;
    }

    /**
     * Get Author.
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set Author.
     *
     * @param string $author
     *
     * @return Comment
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get Rich text comment.
     *
     * @return RichText
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set Rich text comment.
     *
     * @param RichText $pValue
     *
     * @return Comment
     */
    public function setText(RichText $pValue)
    {
        $this->text = $pValue;

        return $this;
    }

    /**
     * Get comment width (CSS style, i.e. XXpx or YYpt).
     *
     * @return string
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set comment width (CSS style, i.e. XXpx or YYpt).
     *
     * @param string $width
     *
     * @return Comment
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get comment height (CSS style, i.e. XXpx or YYpt).
     *
     * @return string
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set comment height (CSS style, i.e. XXpx or YYpt).
     *
     * @param string $value
     *
     * @return Comment
     */
    public function setHeight($value)
    {
        $this->height = $value;

        return $this;
    }

    /**
     * Get left margin (CSS style, i.e. XXpx or YYpt).
     *
     * @return string
     */
    public function getMarginLeft()
    {
        return $this->marginLeft;
    }

    /**
     * Set left margin (CSS style, i.e. XXpx or YYpt).
     *
     * @param string $value
     *
     * @return Comment
     */
    public function setMarginLeft($value)
    {
        $this->marginLeft = $value;

        return $this;
    }

    /**
     * Get top margin (CSS style, i.e. XXpx or YYpt).
     *
     * @return string
     */
    public function getMarginTop()
    {
        return $this->marginTop;
    }

    /**
     * Set top margin (CSS style, i.e. XXpx or YYpt).
     *
     * @param string $value
     *
     * @return Comment
     */
    public function setMarginTop($value)
    {
        $this->marginTop = $value;

        return $this;
    }

    /**
     * Is the comment visible by default?
     *
     * @return bool
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set comment default visibility.
     *
     * @param bool $value
     *
     * @return Comment
     */
    public function setVisible($value)
    {
        $this->visible = $value;

        return $this;
    }

    /**
     * Get fill color.
     *
     * @return Style\Color
     */
    public function getFillColor()
    {
        return $this->fillColor;
    }

    /**
     * Set Alignment.
     *
     * @param string $alignment see Style\Alignment::HORIZONTAL_*
     *
     * @return Comment
     */
    public function setAlignment($alignment)
    {
        $this->alignment = $alignment;

        return $this;
    }

    /**
     * Get Alignment.
     *
     * @return string
     */
    public function getAlignment()
    {
        return $this->alignment;
    }

    /**
     * Get hash code.
     *
     * @return string Hash code
     */
    public function getHashCode()
    {
        return md5(
            $this->author .
            $this->text->getHashCode() .
            $this->width .
            $this->height .
            $this->marginLeft .
            $this->marginTop .
            ($this->visible ? 1 : 0) .
            $this->fillColor->getHashCode() .
            $this->alignment .
            __CLASS__
        );
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if (is_object($value)) {
                $this->$key = clone $value;
            } else {
                $this->$key = $value;
            }
        }
    }

    /**
     * Convert to string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->text->getPlainText();
    }
}
