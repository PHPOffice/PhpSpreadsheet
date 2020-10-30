<?php

namespace PhpOffice\PhpSpreadsheet;

use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;

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
     * @var Color
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
     */
    public function __construct()
    {
        // Initialise variables
        $this->author = 'Author';
        $this->text = new RichText();
        $this->fillColor = new Color('FFFFFFE1');
        $this->alignment = Alignment::HORIZONTAL_GENERAL;
    }

    /**
     * Get Author.
     *
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * Set Author.
     *
     * @param string $author
     *
     * @return $this
     */
    public function setAuthor(string $author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get Rich text comment.
     *
     * @return RichText
     */
    public function getText(): RichText
    {
        return $this->text;
    }

    /**
     * Set Rich text comment.
     *
     * @param RichText $text
     *
     * @return $this
     */
    public function setText(RichText $text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get comment width (CSS style, i.e. XXpx or YYpt).
     *
     * @return string
     */
    public function getWidth(): string
    {
        return $this->width;
    }

    /**
     * Set comment width (CSS style, i.e. XXpx or YYpt).
     *
     * @param string $width including units (px or pt)
     *
     * @return $this
     */
    public function setWidth(string $width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get comment height (CSS style, i.e. XXpx or YYpt).
     *
     * @return string
     */
    public function getHeight(): string
    {
        return $this->height;
    }

    /**
     * Set comment height (CSS style, i.e. XXpx or YYpt).
     *
     * @param string $height including units (px or pt)
     *
     * @return $this
     */
    public function setHeight(string $height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get left margin (CSS style, i.e. XXpx or YYpt).
     *
     * @return string
     */
    public function getMarginLeft(): string
    {
        return $this->marginLeft;
    }

    /**
     * Set left margin (CSS style, i.e. XXpx or YYpt).
     *
     * @param string $margin including units (px or pt)
     *
     * @return $this
     */
    public function setMarginLeft(string $margin)
    {
        $this->marginLeft = $margin;

        return $this;
    }

    /**
     * Get top margin (CSS style, i.e. XXpx or YYpt).
     *
     * @return string
     */
    public function getMarginTop(): string
    {
        return $this->marginTop;
    }

    /**
     * Set top margin (CSS style, i.e. XXpx or YYpt).
     *
     * @param string $margin including units (px or pt)
     *
     * @return $this
     */
    public function setMarginTop(string $margin)
    {
        $this->marginTop = $margin;

        return $this;
    }

    /**
     * Is the comment visible by default?
     *
     * @return bool
     */
    public function getVisible(): bool
    {
        return $this->visible;
    }

    /**
     * Set comment default visibility.
     *
     * @param bool $visibility
     *
     * @return $this
     */
    public function setVisible(bool $visibility)
    {
        $this->visible = $visibility;

        return $this;
    }

    /**
     * Set fill color.
     *
     * @param Color $color
     *
     * @return $this
     */
    public function setFillColor(Color $color)
    {
        $this->fillColor = $color;

        return $this;
    }

    /**
     * Get fill color.
     *
     * @return Color
     */
    public function getFillColor(): Color
    {
        return $this->fillColor;
    }

    /**
     * Set Alignment.
     *
     * @param string $alignment see Alignment::HORIZONTAL_*
     *
     * @return $this
     */
    public function setAlignment(string $alignment): string
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
