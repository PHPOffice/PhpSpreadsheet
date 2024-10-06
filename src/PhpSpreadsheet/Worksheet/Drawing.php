<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use ZipArchive;

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
     */
    private string $path;

    /**
     * Whether or not we are dealing with a URL.
     */
    private bool $isUrl;

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
     */
    public function getFilename(): string
    {
        return basename($this->path);
    }

    /**
     * Get indexed filename (using image index).
     */
    public function getIndexedFilename(): string
    {
        return md5($this->path) . '.' . $this->getExtension();
    }

    /**
     * Get Extension.
     */
    public function getExtension(): string
    {
        $exploded = explode('.', basename($this->path));

        return $exploded[count($exploded) - 1];
    }

    /**
     * Get full filepath to store drawing in zip archive.
     */
    public function getMediaFilename(): string
    {
        if (!array_key_exists($this->type, self::IMAGE_TYPES_CONVERTION_MAP)) {
            throw new PhpSpreadsheetException('Unsupported image type in comment background. Supported types: PNG, JPEG, BMP, GIF.');
        }

        return sprintf('image%d%s', $this->getImageIndex(), $this->getImageFileExtensionForSave());
    }

    /**
     * Get Path.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Set Path.
     *
     * @param string $path File path
     * @param bool $verifyFile Verify file
     * @param ?ZipArchive $zip Zip archive instance
     *
     * @return $this
     */
    public function setPath(string $path, bool $verifyFile = true, ?ZipArchive $zip = null): static
    {
        $this->isUrl = false;
        if (preg_match('~^data:image/[a-z]+;base64,~', $path) === 1) {
            $this->path = $path;

            return $this;
        }

        $this->path = '';
        // Check if a URL has been passed. https://stackoverflow.com/a/2058596/1252979
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            if (!preg_match('/^(http|https|file|ftp|s3):/', $path)) {
                throw new PhpSpreadsheetException('Invalid protocol for linked drawing');
            }
            // Implicit that it is a URL, rather store info than running check above on value in other places.
            $this->isUrl = true;
            $ctx = null;
            // https://github.com/php/php-src/issues/16023
            if (str_starts_with($path, 'https:')) {
                $ctx = stream_context_create(['ssl' => ['crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT]]);
            }
            $imageContents = @file_get_contents($path, false, $ctx);
            if ($imageContents !== false) {
                $filePath = tempnam(sys_get_temp_dir(), 'Drawing');
                if ($filePath) {
                    $put = @file_put_contents($filePath, $imageContents);
                    if ($put !== false) {
                        if ($this->isImage($filePath)) {
                            $this->path = $path;
                            $this->setSizesAndType($filePath);
                        }
                        unlink($filePath);
                    }
                }
            }
        } elseif ($zip instanceof ZipArchive) {
            $zipPath = explode('#', $path)[1];
            $locate = @$zip->locateName($zipPath);
            if ($locate !== false) {
                if ($this->isImage($path)) {
                    $this->path = $path;
                    $this->setSizesAndType($path);
                }
            }
        } else {
            $exists = @file_exists($path);
            if ($exists !== false && $this->isImage($path)) {
                $this->path = $path;
                $this->setSizesAndType($path);
            }
        }
        if ($this->path === '' && $verifyFile) {
            throw new PhpSpreadsheetException("File $path not found!");
        }

        return $this;
    }

    private function isImage(string $path): bool
    {
        $mime = (string) @mime_content_type($path);
        $retVal = false;
        if (str_starts_with($mime, 'image/')) {
            $retVal = true;
        } elseif ($mime === 'application/octet-stream') {
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            $retVal = in_array($extension, ['bin', 'emf'], true);
        }

        return $retVal;
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
    public function getHashCode(): string
    {
        return md5(
            $this->path
            . parent::getHashCode()
            . __CLASS__
        );
    }

    /**
     * Get Image Type for Save.
     */
    public function getImageTypeForSave(): int
    {
        if (!array_key_exists($this->type, self::IMAGE_TYPES_CONVERTION_MAP)) {
            throw new PhpSpreadsheetException('Unsupported image type in comment background. Supported types: PNG, JPEG, BMP, GIF.');
        }

        return self::IMAGE_TYPES_CONVERTION_MAP[$this->type];
    }

    /**
     * Get Image file extention for Save.
     */
    public function getImageFileExtensionForSave(bool $includeDot = true): string
    {
        if (!array_key_exists($this->type, self::IMAGE_TYPES_CONVERTION_MAP)) {
            throw new PhpSpreadsheetException('Unsupported image type in comment background. Supported types: PNG, JPEG, BMP, GIF.');
        }

        $result = image_type_to_extension(self::IMAGE_TYPES_CONVERTION_MAP[$this->type], $includeDot);

        return "$result";
    }

    /**
     * Get Image mime type.
     */
    public function getImageMimeType(): string
    {
        if (!array_key_exists($this->type, self::IMAGE_TYPES_CONVERTION_MAP)) {
            throw new PhpSpreadsheetException('Unsupported image type in comment background. Supported types: PNG, JPEG, BMP, GIF.');
        }

        return image_type_to_mime_type(self::IMAGE_TYPES_CONVERTION_MAP[$this->type]);
    }
}
