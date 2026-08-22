<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

/**
 * <code>
 * Header/Footer Formatting Syntax taken from Office Open XML Part 4 - Markup Language Reference, page 1970:.
 *
 * There are a number of formatting codes that can be written inline with the actual header / footer text, which
 * affect the formatting in the header or footer.
 *
 * Example: This example shows the text "Center Bold Header" on the first line (center section), and the date on
 * the second line (center section).
 *         &CCenter &"-,Bold"Bold&"-,Regular"Header_x000A_&D
 *
 * General Rules:
 * There is no required order in which these codes must appear.
 *
 * The first occurrence of the following codes turns the formatting ON, the second occurrence turns it OFF again:
 * - strikethrough
 * - superscript
 * - subscript
 * Superscript and subscript cannot both be ON at same time. Whichever comes first wins and the other is ignored,
 * while the first is ON.
 * &L - code for "left section" (there are three header / footer locations, "left", "center", and "right"). When
 * two or more occurrences of this section marker exist, the contents from all markers are concatenated, in the
 * order of appearance, and placed into the left section.
 * &P - code for "current page #"
 * &N - code for "total pages"
 * &font size - code for "text font size", where font size is a font size in points.
 * &K - code for "text font color"
 * RGB Color is specified as RRGGBB
 * Theme Color is specified as TTSNN where TT is the theme color Id, S is either "+" or "-" of the tint/shade
 * value, NN is the tint/shade value.
 * &S - code for "text strikethrough" on / off
 * &X - code for "text super script" on / off
 * &Y - code for "text subscript" on / off
 * &C - code for "center section". When two or more occurrences of this section marker exist, the contents
 * from all markers are concatenated, in the order of appearance, and placed into the center section.
 *
 * &D - code for "date"
 * &T - code for "time"
 * &G - code for "picture as background"
 * &U - code for "text single underline"
 * &E - code for "double underline"
 * &R - code for "right section". When two or more occurrences of this section marker exist, the contents
 * from all markers are concatenated, in the order of appearance, and placed into the right section.
 * &Z - code for "this workbook's file path"
 * &F - code for "this workbook's file name"
 * &A - code for "sheet tab name"
 * &+ - code for add to page #.
 * &- - code for subtract from page #.
 * &"font name,font type" - code for "text font name" and "text font type", where font name and font type
 * are strings specifying the name and type of the font, separated by a comma. When a hyphen appears in font
 * name, it means "none specified". Both of font name and font type can be localized values.
 * &"-,Bold" - code for "bold font style"
 * &B - also means "bold font style".
 * &"-,Regular" - code for "regular font style"
 * &"-,Italic" - code for "italic font style"
 * &I - also means "italic font style"
 * &"-,Bold Italic" code for "bold italic font style"
 * &O - code for "outline style"
 * &H - code for "shadow style"
 * </code>
 */
class HeaderFooter
{
    // Header/footer image location
    const IMAGE_HEADER_LEFT = 'LH';
    const IMAGE_HEADER_LEFT_ODD = 'LH';
    const IMAGE_HEADER_LEFT_FIRST = 'LHFIRST';
    const IMAGE_HEADER_LEFT_EVEN = 'LHEVEN';
    const IMAGE_HEADER_CENTER = 'CH';
    const IMAGE_HEADER_CENTER_ODD = 'CH';
    const IMAGE_HEADER_CENTER_FIRST = 'CHFIRST';
    const IMAGE_HEADER_CENTER_EVEN = 'CHEVEN';
    const IMAGE_HEADER_RIGHT = 'RH';
    const IMAGE_HEADER_RIGHT_ODD = 'RH';
    const IMAGE_HEADER_RIGHT_FIRST = 'RHFIRST';
    const IMAGE_HEADER_RIGHT_EVEN = 'RHEVEN';
    const IMAGE_FOOTER_LEFT = 'LF';
    const IMAGE_FOOTER_LEFT_ODD = 'LF';
    const IMAGE_FOOTER_LEFT_FIRST = 'LFFIRST';
    const IMAGE_FOOTER_LEFT_EVEN = 'LFEVEN';
    const IMAGE_FOOTER_CENTER = 'CF';
    const IMAGE_FOOTER_CENTER_ODD = 'CF';
    const IMAGE_FOOTER_CENTER_FIRST = 'CFFIRST';
    const IMAGE_FOOTER_CENTER_EVEN = 'CFEVEN';
    const IMAGE_FOOTER_RIGHT = 'RF';
    const IMAGE_FOOTER_RIGHT_ODD = 'RF';
    const IMAGE_FOOTER_RIGHT_FIRST = 'RFFIRST';
    const IMAGE_FOOTER_RIGHT_EVEN = 'RFEVEN';

    /**
     * OddHeader.
     */
    private string $oddHeader = '';

    /**
     * OddFooter.
     */
    private string $oddFooter = '';

    /**
     * EvenHeader.
     */
    private string $evenHeader = '';

    /**
     * EvenFooter.
     */
    private string $evenFooter = '';

    /**
     * FirstHeader.
     */
    private string $firstHeader = '';

    /**
     * FirstFooter.
     */
    private string $firstFooter = '';

    /**
     * Different header for Odd/Even, defaults to false.
     */
    private bool $differentOddEven = false;

    /**
     * Different header for first page, defaults to false.
     */
    private bool $differentFirst = false;

    /**
     * Scale with document, defaults to true.
     */
    private bool $scaleWithDocument = true;

    /**
     * Align with margins, defaults to true.
     */
    private bool $alignWithMargins = true;

    /**
     * Header/footer images.
     *
     * @var HeaderFooterDrawing[]
     */
    private array $headerFooterImages = [];

    /**
     * Create a new HeaderFooter.
     */
    public function __construct()
    {
    }

    /**
     * Get OddHeader.
     */
    public function getOddHeader(): string
    {
        return $this->oddHeader;
    }

    /**
     * Set OddHeader.
     *
     * @return $this
     */
    public function setOddHeader(string $oddHeader): static
    {
        $this->oddHeader = $oddHeader;

        return $this;
    }

    /**
     * Get OddFooter.
     */
    public function getOddFooter(): string
    {
        return $this->oddFooter;
    }

    /**
     * Set OddFooter.
     *
     * @return $this
     */
    public function setOddFooter(string $oddFooter): static
    {
        $this->oddFooter = $oddFooter;

        return $this;
    }

    /**
     * Get EvenHeader.
     */
    public function getEvenHeader(): string
    {
        return $this->evenHeader;
    }

    /**
     * Set EvenHeader.
     *
     * @return $this
     */
    public function setEvenHeader(string $eventHeader): static
    {
        $this->evenHeader = $eventHeader;

        return $this;
    }

    /**
     * Get EvenFooter.
     */
    public function getEvenFooter(): string
    {
        return $this->evenFooter;
    }

    /**
     * Set EvenFooter.
     *
     * @return $this
     */
    public function setEvenFooter(string $evenFooter): static
    {
        $this->evenFooter = $evenFooter;

        return $this;
    }

    /**
     * Get FirstHeader.
     */
    public function getFirstHeader(): string
    {
        return $this->firstHeader;
    }

    /**
     * Set FirstHeader.
     *
     * @return $this
     */
    public function setFirstHeader(string $firstHeader): static
    {
        $this->firstHeader = $firstHeader;

        return $this;
    }

    /**
     * Get FirstFooter.
     */
    public function getFirstFooter(): string
    {
        return $this->firstFooter;
    }

    /**
     * Set FirstFooter.
     *
     * @return $this
     */
    public function setFirstFooter(string $firstFooter): static
    {
        $this->firstFooter = $firstFooter;

        return $this;
    }

    /**
     * Get DifferentOddEven.
     */
    public function getDifferentOddEven(): bool
    {
        return $this->differentOddEven;
    }

    /**
     * Set DifferentOddEven.
     *
     * @return $this
     */
    public function setDifferentOddEven(bool $differentOddEvent): static
    {
        $this->differentOddEven = $differentOddEvent;

        return $this;
    }

    /**
     * Get DifferentFirst.
     */
    public function getDifferentFirst(): bool
    {
        return $this->differentFirst;
    }

    /**
     * Set DifferentFirst.
     *
     * @return $this
     */
    public function setDifferentFirst(bool $differentFirst): static
    {
        $this->differentFirst = $differentFirst;

        return $this;
    }

    /**
     * Get ScaleWithDocument.
     */
    public function getScaleWithDocument(): bool
    {
        return $this->scaleWithDocument;
    }

    /**
     * Set ScaleWithDocument.
     *
     * @return $this
     */
    public function setScaleWithDocument(bool $scaleWithDocument): static
    {
        $this->scaleWithDocument = $scaleWithDocument;

        return $this;
    }

    /**
     * Get AlignWithMargins.
     */
    public function getAlignWithMargins(): bool
    {
        return $this->alignWithMargins;
    }

    /**
     * Set AlignWithMargins.
     *
     * @return $this
     */
    public function setAlignWithMargins(bool $alignWithMargins): static
    {
        $this->alignWithMargins = $alignWithMargins;

        return $this;
    }

    /**
     * Add header/footer image.
     *
     * @return $this
     */
    public function addImage(HeaderFooterDrawing $image, string $location = self::IMAGE_HEADER_LEFT): static
    {
        $this->headerFooterImages[$location] = $image;

        return $this;
    }

    /**
     * Remove header/footer image.
     *
     * @return $this
     */
    public function removeImage(string $location = self::IMAGE_HEADER_LEFT): static
    {
        if (isset($this->headerFooterImages[$location])) {
            unset($this->headerFooterImages[$location]);
        }

        return $this;
    }

    /**
     * Set header/footer images.
     *
     * @param HeaderFooterDrawing[] $images
     *
     * @return $this
     */
    public function setImages(array $images): static
    {
        $this->headerFooterImages = $images;

        return $this;
    }

    private const IMAGE_SORT_ORDER = [
        self::IMAGE_HEADER_LEFT,
        self::IMAGE_HEADER_LEFT_FIRST,
        self::IMAGE_HEADER_LEFT_EVEN,
        self::IMAGE_HEADER_CENTER,
        self::IMAGE_HEADER_CENTER_FIRST,
        self::IMAGE_HEADER_CENTER_EVEN,
        self::IMAGE_HEADER_RIGHT,
        self::IMAGE_HEADER_RIGHT_FIRST,
        self::IMAGE_HEADER_RIGHT_EVEN,
        self::IMAGE_FOOTER_LEFT,
        self::IMAGE_FOOTER_LEFT_FIRST,
        self::IMAGE_FOOTER_LEFT_EVEN,
        self::IMAGE_FOOTER_CENTER,
        self::IMAGE_FOOTER_CENTER_FIRST,
        self::IMAGE_FOOTER_CENTER_EVEN,
        self::IMAGE_FOOTER_RIGHT,
        self::IMAGE_FOOTER_RIGHT_FIRST,
        self::IMAGE_FOOTER_RIGHT_EVEN,
    ];

    /**
     * Get header/footer images.
     *
     * @return HeaderFooterDrawing[]
     */
    public function getImages(): array
    {
        // Sort array - not sure why needed
        $images = [];
        foreach (self::IMAGE_SORT_ORDER as $key) {
            if (isset($this->headerFooterImages[$key])) {
                $images[$key] = $this->headerFooterImages[$key];
            }
        }
        $this->headerFooterImages = $images;

        return $this->headerFooterImages;
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
}
