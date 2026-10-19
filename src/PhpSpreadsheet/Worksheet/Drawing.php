<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use Composer\Pcre\Preg;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Shared\Metafile;
use ZipArchive;

class Drawing extends BaseDrawing
{
    const IMAGE_TYPES_CONVERTION_MAP = [
        IMAGETYPE_GIF => IMAGETYPE_PNG,
        IMAGETYPE_JPEG => IMAGETYPE_JPEG,
        IMAGETYPE_PNG => IMAGETYPE_PNG,
        IMAGETYPE_BMP => IMAGETYPE_PNG,
    ];

    const UNSUPPORTED_IMAGE_TYPE_MESSAGE = 'Unsupported image type in comment background. Supported types: PNG, JPEG, BMP, GIF, WMF, EMF.';

    /**
     * Path.
     */
    private string $path;

    /**
     * Whether or not we are dealing with a URL.
     */
    private bool $isUrl;

    /**
     * Metafile type (Metafile::TYPE_WMF, Metafile::TYPE_EMF or Metafile::TYPE_EMFPLUS), null if not a metafile.
     */
    private ?string $metafileType = null;

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
        if (Preg::isMatch('~^data:image/([^;]+);base64,~', $this->path, $matches)) {
            return $matches[1];
        }
        if ($this->metafileType !== null) {
            return Metafile::getExtension($this->metafileType);
        }
        $exploded = explode('.', basename($this->path));

        return $exploded[count($exploded) - 1];
    }

    /**
     * Get full filepath to store drawing in zip archive.
     */
    public function getMediaFilename(): string
    {
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
     * @param null|callable(string):bool $isWhitelisted
     *
     * @return $this
     */
    public function setPath(string $path, bool $verifyFile = true, ?ZipArchive $zip = null, bool $allowExternal = true, ?callable $isWhitelisted = null): static
    {
        $this->isUrl = false;
        $this->metafileType = null;
        if (Preg::isMatch('~^data:image/[a-z]+;base64,~', $path)) {
            $this->path = $path;

            return $this;
        }

        $this->path = '';
        if ($zip instanceof ZipArchive) {
            $zipPath = explode('#', $path)[1];
            $locate = @$zip->locateName($zipPath);
            if ($locate !== false) {
                if ($this->isImage($path)) {
                    $this->path = $path;
                    $this->setSizesAndType($path);
                }
            }
        // Check if a URL has been passed. https://stackoverflow.com/a/2058596/1252979
        } elseif (
            filter_var($path, FILTER_VALIDATE_URL)
            || Preg::isMatch('~^phar://~i', $path)
            || (Preg::isMatch('/^([\w.\s\x00-\x1f]+):/', $path) && !Preg::isMatch('/^([\w.]+):/', $path))
        ) {
            if (!Preg::isMatch('/^(http|https|file|ftp|s3):/', $path)) {
                throw new PhpSpreadsheetException('Invalid protocol for linked drawing');
            }
            if (!$allowExternal) {
                return $this;
            }
            if ($isWhitelisted !== null && !$isWhitelisted($path)) {
                return $this;
            }
            // Implicit that it is a URL, rather store info than running check above on value in other places.
            $this->isUrl = true;
            $ctx = null;
            // https://github.com/php/php-src/issues/16023
            // https://github.com/php/php-src/issues/17121
            if (str_starts_with($path, 'https:') || str_starts_with($path, 'http:')) {
                $ctxArray = [
                    'http' => [
                        'follow_location' => 0,
                        'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
                        'header' => [
                            //'Connection: keep-alive', // unacceptable performance
                            'Accept: image/*;q=0.9,*/*;q=0.8',
                        ],
                    ],
                ];
                if (str_starts_with($path, 'https:')) {
                    $ctxArray['ssl'] = ['crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT];
                }
                $ctx = stream_context_create($ctxArray);
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

        if ($this->worksheet !== null) {
            if ($this->path !== '') {
                $this->worksheet->getCell($this->coordinates);
            }
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
        if (!$retVal) {
            $retVal = Metafile::detectFile($path) !== null;
        }

        return $retVal;
    }

    /**
     * Set Fact Sizes and Type of Image, rendering Windows Metafiles (WMF, EMF, EMF+) to know their sizes.
     */
    protected function setSizesAndType(string $path): void
    {
        $this->metafileType = Metafile::detectFile($path);
        if ($this->metafileType !== null && $this->imageWidth === 0 && $this->imageHeight === 0) {
            // If the metafile can not be rendered, its sizes stay unknown
            $image = Metafile::tryToGdImage((string) file_get_contents($path));
            if ($image !== null) {
                $this->imageWidth = imagesx($image);
                $this->imageHeight = imagesy($image);
            }
        }

        parent::setSizesAndType($path);
    }

    /**
     * Whether the image is a Windows Metafile (WMF, EMF or EMF+).
     */
    public function isMetafile(): bool
    {
        return $this->metafileType !== null;
    }

    /**
     * Get the Windows Metafile type (Metafile::TYPE_WMF, Metafile::TYPE_EMF or Metafile::TYPE_EMFPLUS), null if not a metafile.
     */
    public function getMetafileType(): ?string
    {
        return $this->metafileType;
    }

    /**
     * Get the contents of the image file.
     */
    public function getContents(): ?string
    {
        if ($this->path === '') {
            return null;
        }
        $contents = @file_get_contents($this->path);

        return ($contents === false) ? null : $contents;
    }

    /**
     * Whether the image can be saved as PNG, JPEG (comment background, ...).
     */
    public function isSupportedForSave(): bool
    {
        return $this->metafileType !== null || array_key_exists($this->type, self::IMAGE_TYPES_CONVERTION_MAP);
    }

    /**
     * Get isURL.
     */
    public function getIsURL(): bool
    {
        return $this->isUrl;
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
        // Windows Metafiles are converted to PNG
        if ($this->metafileType !== null) {
            return IMAGETYPE_PNG;
        }
        if (!array_key_exists($this->type, self::IMAGE_TYPES_CONVERTION_MAP)) {
            throw new PhpSpreadsheetException(self::UNSUPPORTED_IMAGE_TYPE_MESSAGE);
        }

        return self::IMAGE_TYPES_CONVERTION_MAP[$this->type];
    }

    /**
     * Get Image file extension for Save.
     */
    public function getImageFileExtensionForSave(bool $includeDot = true): string
    {
        $result = image_type_to_extension($this->getImageTypeForSave(), $includeDot);

        return "$result";
    }

    /**
     * Get Image mime type.
     */
    public function getImageMimeType(): string
    {
        return image_type_to_mime_type($this->getImageTypeForSave());
    }
}
