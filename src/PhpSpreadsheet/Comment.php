<?php

namespace PhpOffice\PhpSpreadsheet;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Helper\Size;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared\Drawing as SharedDrawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Stringable;

class Comment implements IComparable, Stringable
{
    /**
     * Author.
     */
    private string $author;

    /**
     * Rich text comment.
     */
    private RichText $text;

    /**
     * Comment width (CSS style, i.e. XXpx or YYpt).
     */
    private string $width = '96pt';

    /**
     * Left margin (CSS style, i.e. XXpx or YYpt).
     */
    private string $marginLeft = '59.25pt';

    /**
     * Top margin (CSS style, i.e. XXpx or YYpt).
     */
    private string $marginTop = '1.5pt';

    /**
     * Visible.
     *
     * @var bool
     */
    private $visible = false;

    /**
     * Comment height (CSS style, i.e. XXpx or YYpt).
     */
    private string $height = '55.5pt';

    /**
     * Comment fill color.
     */
    private Color $fillColor;

    /**
     * Alignment.
     */
    private string $alignment;

    /**
     * Background image in comment.
     */
    private Drawing $backgroundImage;

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
        $this->backgroundImage = new Drawing();
    }

    /**
     * Get Author.
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * Set Author.
     */
    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get Rich text comment.
     */
    public function getText(): RichText
    {
        return $this->text;
    }

    /**
     * Set Rich text comment.
     */
    public function setText(RichText $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get comment width (CSS style, i.e. XXpx or YYpt).
     */
    public function getWidth(): string
    {
        return $this->width;
    }

    /**
     * Set comment width (CSS style, i.e. XXpx or YYpt). Default unit is pt.
     */
    public function setWidth(string $width): self
    {
        $width = new Size($width);
        if ($width->valid()) {
            $this->width = (string) $width;
        }

        return $this;
    }

    /**
     * Get comment height (CSS style, i.e. XXpx or YYpt).
     */
    public function getHeight(): string
    {
        return $this->height;
    }

    /**
     * Set comment height (CSS style, i.e. XXpx or YYpt). Default unit is pt.
     */
    public function setHeight(string $height): self
    {
        $height = new Size($height);
        if ($height->valid()) {
            $this->height = (string) $height;
        }

        return $this;
    }

    /**
     * Get left margin (CSS style, i.e. XXpx or YYpt).
     */
    public function getMarginLeft(): string
    {
        return $this->marginLeft;
    }

    /**
     * Set left margin (CSS style, i.e. XXpx or YYpt). Default unit is pt.
     */
    public function setMarginLeft(string $margin): self
    {
        $margin = new Size($margin);
        if ($margin->valid()) {
            $this->marginLeft = (string) $margin;
        }

        return $this;
    }

    /**
     * Get top margin (CSS style, i.e. XXpx or YYpt).
     */
    public function getMarginTop(): string
    {
        return $this->marginTop;
    }

    /**
     * Set top margin (CSS style, i.e. XXpx or YYpt). Default unit is pt.
     */
    public function setMarginTop(string $margin): self
    {
        $margin = new Size($margin);
        if ($margin->valid()) {
            $this->marginTop = (string) $margin;
        }

        return $this;
    }

    /**
     * Is the comment visible by default?
     */
    public function getVisible(): bool
    {
        return $this->visible;
    }

    /**
     * Set comment default visibility.
     */
    public function setVisible(bool $visibility): self
    {
        $this->visible = $visibility;

        return $this;
    }

    /**
     * Set fill color.
     */
    public function setFillColor(Color $color): self
    {
        $this->fillColor = $color;

        return $this;
    }

    /**
     * Get fill color.
     */
    public function getFillColor(): Color
    {
        return $this->fillColor;
    }

    /**
     * Set Alignment.
     */
    public function setAlignment(string $alignment): self
    {
        $this->alignment = $alignment;

        return $this;
    }

    /**
     * Get Alignment.
     */
    public function getAlignment(): string
    {
        return $this->alignment;
    }

    /**
     * Get hash code.
     */
    public function getHashCode(): string
    {
        return md5(
            $this->author
            . $this->text->getHashCode()
            . $this->width
            . $this->height
            . $this->marginLeft
            . $this->marginTop
            . ($this->visible ? 1 : 0)
            . $this->fillColor->getHashCode()
            . $this->alignment
            . ($this->hasBackgroundImage() ? $this->backgroundImage->getHashCode() : '')
            . __CLASS__
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
     */
    public function __toString(): string
    {
        return $this->text->getPlainText();
    }

    /**
     * Check is background image exists.
     */
    public function hasBackgroundImage(): bool
    {
        $path = $this->backgroundImage->getPath();

        if (empty($path)) {
            return false;
        }

        return getimagesize($path) !== false;
    }

    /**
     * Returns background image.
     */
    public function getBackgroundImage(): Drawing
    {
        return $this->backgroundImage;
    }

    /**
     * Sets background image.
     */
    public function setBackgroundImage(Drawing $objDrawing): self
    {
        if (!array_key_exists($objDrawing->getType(), Drawing::IMAGE_TYPES_CONVERTION_MAP)) {
            throw new PhpSpreadsheetException('Unsupported image type in comment background. Supported types: PNG, JPEG, BMP, GIF.');
        }
        $this->backgroundImage = $objDrawing;

        return $this;
    }

    /**
     * Sets size of comment as size of background image.
     */
    public function setSizeAsBackgroundImage(): self
    {
        if ($this->hasBackgroundImage()) {
            $this->setWidth(SharedDrawing::pixelsToPoints($this->backgroundImage->getWidth()) . 'pt');
            $this->setHeight(SharedDrawing::pixelsToPoints($this->backgroundImage->getHeight()) . 'pt');
        }

        return $this;
    }
}
