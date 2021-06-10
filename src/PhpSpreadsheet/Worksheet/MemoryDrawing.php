<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use GdImage;
use PhpOffice\PhpSpreadsheet\Exception;

class MemoryDrawing extends BaseDrawing
{
    // Rendering functions
    const RENDERING_DEFAULT = 'imagepng';
    const RENDERING_PNG = 'imagepng';
    const RENDERING_GIF = 'imagegif';
    const RENDERING_JPEG = 'imagejpeg';

    // MIME types
    const MIMETYPE_DEFAULT = 'image/png';
    const MIMETYPE_PNG = 'image/png';
    const MIMETYPE_GIF = 'image/gif';
    const MIMETYPE_JPEG = 'image/jpeg';

    /**
     * Image resource.
     *
     * @var null|GdImage|resource
     */
    private $imageResource;

    /**
     * Rendering function.
     *
     * @var string
     */
    private $renderingFunction;

    /**
     * Mime type.
     *
     * @var string
     */
    private $mimeType;

    /**
     * Unique name.
     *
     * @var string
     */
    private $uniqueName;

    /**
     * Create a new MemoryDrawing.
     */
    public function __construct()
    {
        // Initialise values
        $this->renderingFunction = self::RENDERING_DEFAULT;
        $this->mimeType = self::MIMETYPE_DEFAULT;
        $this->uniqueName = md5(mt_rand(0, 9999) . time() . mt_rand(0, 9999));

        // Initialize parent
        parent::__construct();
    }

    public function __destruct()
    {
        if ($this->imageResource) {
            imagedestroy($this->imageResource);
            $this->imageResource = null;
        }
    }

    public function __clone()
    {
        parent::__clone();
        $this->cloneResource();
    }

    private function cloneResource(): void
    {
        if (!$this->imageResource) {
            return;
        }

        $width = imagesx($this->imageResource);
        $height = imagesy($this->imageResource);

        if (imageistruecolor($this->imageResource)) {
            $clone = imagecreatetruecolor($width, $height);
            if (!$clone) {
                throw new Exception('Could not clone image resource');
            }

            imagealphablending($clone, false);
            imagesavealpha($clone, true);
        } else {
            $clone = imagecreate($width, $height);
            if (!$clone) {
                throw new Exception('Could not clone image resource');
            }

            // If the image has transparency...
            $transparent = imagecolortransparent($this->imageResource);
            if ($transparent >= 0) {
                $rgb = imagecolorsforindex($this->imageResource, $transparent);
                if ($rgb === false) {
                    throw new Exception('Could not get image colors');
                }

                imagesavealpha($clone, true);
                $color = imagecolorallocatealpha($clone, $rgb['red'], $rgb['green'], $rgb['blue'], $rgb['alpha']);
                if ($color === false) {
                    throw new Exception('Could not get image alpha color');
                }

                imagefill($clone, 0, 0, $color);
            }
        }

        //Create the Clone!!
        imagecopy($clone, $this->imageResource, 0, 0, 0, 0, $width, $height);

        $this->imageResource = $clone;
    }

    /**
     * Get image resource.
     *
     * @return null|GdImage|resource
     */
    public function getImageResource()
    {
        return $this->imageResource;
    }

    /**
     * Set image resource.
     *
     * @param GdImage|resource $value
     *
     * @return $this
     */
    public function setImageResource($value)
    {
        $this->imageResource = $value;

        if ($this->imageResource !== null) {
            // Get width/height
            $this->width = imagesx($this->imageResource);
            $this->height = imagesy($this->imageResource);
        }

        return $this;
    }

    /**
     * Get rendering function.
     *
     * @return string
     */
    public function getRenderingFunction()
    {
        return $this->renderingFunction;
    }

    /**
     * Set rendering function.
     *
     * @param string $value see self::RENDERING_*
     *
     * @return $this
     */
    public function setRenderingFunction($value)
    {
        $this->renderingFunction = $value;

        return $this;
    }

    /**
     * Get mime type.
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Set mime type.
     *
     * @param string $value see self::MIMETYPE_*
     *
     * @return $this
     */
    public function setMimeType($value)
    {
        $this->mimeType = $value;

        return $this;
    }

    /**
     * Get indexed filename (using image index).
     *
     * @return string
     */
    public function getIndexedFilename()
    {
        $extension = strtolower($this->getMimeType());
        $extension = explode('/', $extension);
        $extension = $extension[1];

        return $this->uniqueName . $this->getImageIndex() . '.' . $extension;
    }

    /**
     * Get hash code.
     *
     * @return string Hash code
     */
    public function getHashCode()
    {
        return md5(
            $this->renderingFunction .
            $this->mimeType .
            $this->uniqueName .
            parent::getHashCode() .
            __CLASS__
        );
    }
}
