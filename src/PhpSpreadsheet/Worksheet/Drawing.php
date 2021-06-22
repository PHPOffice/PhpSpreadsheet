<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;

class Drawing extends BaseDrawing
{
    /**
     * Path.
     *
     * @var string
     */
    private $path;

    /**
     * Whether or not we are dealing with a URL.
     *
     * @var bool
     */
    private $isUrl;

    /**
     * Create a new Drawing.
     */
    public function __construct()
    {
        // Initialise values
        $this->path = '';
        $this->isUrl = false;

        // Initialize parent
        parent::__construct();
    }

    /**
     * Get Filename.
     *
     * @return string
     */
    public function getFilename()
    {
        return basename($this->path);
    }

    /**
     * Get indexed filename (using image index).
     *
     * @return string
     */
    public function getIndexedFilename()
    {
        $fileName = $this->getFilename();
        $fileName = str_replace(' ', '_', $fileName);

        return str_replace('.' . $this->getExtension(), '', $fileName) . $this->getImageIndex() . '.' . $this->getExtension();
    }

    /**
     * Get Extension.
     *
     * @return string
     */
    public function getExtension()
    {
        $exploded = explode('.', basename($this->path));

        return $exploded[count($exploded) - 1];
    }

    /**
     * Get Path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set Path.
     *
     * @param string $pValue File path
     * @param bool $pVerifyFile Verify file
     *
     * @return $this
     */
    public function setPath($pValue, $pVerifyFile = true)
    {
        if ($pVerifyFile) {
            // Check if a URL has been passed. https://stackoverflow.com/a/2058596/1252979
            if (filter_var($pValue, FILTER_VALIDATE_URL)) {
                $this->path = $pValue;
                // Implicit that it is a URL, rather store info than running check above on value in other places.
                $this->isUrl = true;
                $imageContents = file_get_contents($pValue);
                $filePath = tempnam(sys_get_temp_dir(), 'Drawing');
                if ($filePath) {
                    file_put_contents($filePath, $imageContents);
                    if (file_exists($filePath)) {
                        if ($this->width == 0 && $this->height == 0) {
                            // Get width/height
                            [$this->width, $this->height] = getimagesize($filePath);
                        }
                        unlink($filePath);
                    }
                }
            } elseif (file_exists($pValue)) {
                $this->path = $pValue;
                if ($this->width == 0 && $this->height == 0) {
                    // Get width/height
                    [$this->width, $this->height] = getimagesize($pValue);
                }
            } else {
                throw new PhpSpreadsheetException("File $pValue not found!");
            }
        } else {
            $this->path = $pValue;
        }

        return $this;
    }

    /**
     * Get isURL.
     */
    public function getIsURL(): bool
    {
        return $this->isUrl;
    }

    /**
     * Set isURL.
     *
     * @return $this
     */
    public function setIsURL(bool $isUrl): self
    {
        $this->isUrl = $isUrl;

        return $this;
    }

    /**
     * Get hash code.
     *
     * @return string Hash code
     */
    public function getHashCode()
    {
        return md5(
            $this->path .
            parent::getHashCode() .
            __CLASS__
        );
    }
}
