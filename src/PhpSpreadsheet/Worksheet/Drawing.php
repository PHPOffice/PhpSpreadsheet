<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;

class Drawing extends BaseDrawing
{
    const IMAGE_TYPES_CONVERTION_MAP = [
        IMAGETYPE_GIF => IMAGETYPE_PNG,
        IMAGETYPE_JPEG => IMAGETYPE_JPEG,
        IMAGETYPE_PNG => IMAGETYPE_PNG,
        IMAGETYPE_BMP => IMAGETYPE_PNG,
    ];

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
     */
    public function getIndexedFilename(): string
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
     * Get full filepath to store drawing in zip archive.
     *
     * @return string
     */
    public function getMediaFilename()
    {
        if (!array_key_exists($this->type, self::IMAGE_TYPES_CONVERTION_MAP)) {
            throw new PhpSpreadsheetException('Unsupported image type in comment background. Supported types: PNG, JPEG, BMP, GIF.');
        }

        $newImageType = self::IMAGE_TYPES_CONVERTION_MAP[$this->type];

        return sprintf('image%d%s', $this->getImageIndex(), image_type_to_extension($newImageType));
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
     * @param string $path File path
     * @param bool $verifyFile Verify file
     * @param \ZipArchive $zip Zip archive instance
     *
     * @return $this
     */
    public function setPath($path, $verifyFile = true, $zip = null)
    {
        if ($verifyFile) {
            // Check if a URL has been passed. https://stackoverflow.com/a/2058596/1252979
            if (filter_var($path, FILTER_VALIDATE_URL)) {
                $this->path = $path;
                // Implicit that it is a URL, rather store info than running check above on value in other places.
                $this->isUrl = true;
                $imageContents = file_get_contents($path);
                $filePath = tempnam(sys_get_temp_dir(), 'Drawing');
                if ($filePath) {
                    file_put_contents($filePath, $imageContents);
                    if (file_exists($filePath)) {
                        $this->setSizesAndType($filePath);
                        unlink($filePath);
                    }
                }
            } elseif (file_exists($path)) {
                $this->path = $path;
                $this->setSizesAndType($path);
            } elseif ($zip instanceof \ZipArchive) {
                $zipPath = explode('#', $path)[1];
                if ($zip->locateName($zipPath) !== false) {
                    $this->path = $path;
                    $this->setSizesAndType($path);
                }
            } else {
                throw new PhpSpreadsheetException("File $path not found!");
            }
        } else {
            $this->path = $path;
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
