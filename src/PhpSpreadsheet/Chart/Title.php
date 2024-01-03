<?php

namespace PhpOffice\PhpSpreadsheet\Chart;

use PhpOffice\PhpSpreadsheet\RichText\RichText;

class Title
{
    /**
     * Title Caption.
     *
     * @var array<RichText|string>|RichText|string
     */
    private $caption;

    /**
     * Allow overlay of other elements?
     */
    private bool $overlay = true;

    /**
     * Title Layout.
     */
    private ?Layout $layout;

    /**
     * Create a new Title.
     */
    public function __construct(array|RichText|string $caption = '', ?Layout $layout = null, bool $overlay = false)
    {
        $this->caption = $caption;
        $this->layout = $layout;
        $this->setOverlay($overlay);
    }

    /**
     * Get caption.
     */
    public function getCaption(): array|RichText|string
    {
        return $this->caption;
    }

    public function getCaptionText(): string
    {
        $caption = $this->caption;
        if (is_string($caption)) {
            return $caption;
        }
        if ($caption instanceof RichText) {
            return $caption->getPlainText();
        }
        $retVal = '';
        foreach ($caption as $textx) {
            /** @var RichText|string $text */
            $text = $textx;
            if ($text instanceof RichText) {
                $retVal .= $text->getPlainText();
            } else {
                $retVal .= $text;
            }
        }

        return $retVal;
    }

    /**
     * Set caption.
     *
     * @return $this
     */
    public function setCaption(array|RichText|string $caption): static
    {
        $this->caption = $caption;

        return $this;
    }

    /**
     * Get allow overlay of other elements?
     */
    public function getOverlay(): bool
    {
        return $this->overlay;
    }

    /**
     * Set allow overlay of other elements?
     */
    public function setOverlay(bool $overlay): void
    {
        $this->overlay = $overlay;
    }

    public function getLayout(): ?Layout
    {
        return $this->layout;
    }
}
