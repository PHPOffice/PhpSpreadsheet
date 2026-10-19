<?php

namespace PhpOffice\PhpSpreadsheet\Shared;

use GdImage;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\WMF\Reader\Detector;
use PhpOffice\WMF\Reader\EMF\GD as EmfReader;
use PhpOffice\WMF\Reader\WMF\GD as WmfReader;
use Throwable;

/**
 * Helper for Windows Metafiles (WMF, EMF and EMF+), based on phpoffice/wmf.
 */
class Metafile
{
    const TYPE_WMF = Detector::TYPE_WMF;
    const TYPE_EMF = Detector::TYPE_EMF;
    const TYPE_EMFPLUS = Detector::TYPE_EMFPLUS;

    const MIMETYPE_WMF = 'image/x-wmf';
    const MIMETYPE_EMF = 'image/x-emf';

    /**
     * Number of bytes needed to recognize a WMF or EMF header.
     */
    private const HEADER_LENGTH = 88;

    /**
     * Get the metafile type (TYPE_WMF, TYPE_EMF or TYPE_EMFPLUS) of some content, or null if it is not a metafile.
     */
    public static function detect(string $content): ?string
    {
        $type = Detector::detect($content);

        return $type === Detector::TYPE_UNKNOWN ? null : $type;
    }

    /**
     * Get the metafile type (TYPE_WMF, TYPE_EMF or TYPE_EMFPLUS) of a file, or null if it is not a metafile.
     *
     * Works with any path readable by file_get_contents, including zip:// paths.
     */
    public static function detectFile(string $path): ?string
    {
        $header = @file_get_contents($path, false, null, 0, self::HEADER_LENGTH);
        if ($header === false || (!Detector::isWMF($header) && !Detector::isEMF($header))) {
            return null;
        }
        $content = @file_get_contents($path);

        return ($content === false) ? null : self::detect($content);
    }

    /**
     * Get the file extension for a metafile type ("wmf" or "emf").
     */
    public static function getExtension(string $type): string
    {
        return $type === self::TYPE_WMF ? 'wmf' : 'emf';
    }

    /**
     * Get the mime type for a metafile type.
     */
    public static function getMimeType(string $type): string
    {
        return $type === self::TYPE_WMF ? self::MIMETYPE_WMF : self::MIMETYPE_EMF;
    }

    /**
     * Render a metafile as a GD image.
     *
     * @throws Exception
     */
    public static function toGdImage(string $content): GdImage
    {
        $reader = Detector::isWMF($content) ? new WmfReader() : new EmfReader();

        try {
            if ($reader->loadFromString($content)) {
                $image = $reader->getResource();
                if ($image instanceof GdImage) {
                    return $image;
                }
            }
        } catch (Throwable $e) {
            throw new Exception('Unable to render metafile: ' . $e->getMessage(), 0, $e);
        }

        throw new Exception('Unable to render metafile');
    }

    /**
     * Render a metafile as PNG data.
     *
     * @throws Exception
     */
    public static function toPng(string $content): string
    {
        return self::encodePng(self::toGdImage($content));
    }

    /**
     * Render a metafile as a GD image, or null if it can not be rendered.
     */
    public static function tryToGdImage(?string $content): ?GdImage
    {
        if ($content === null) {
            return null;
        }

        try {
            return self::toGdImage($content);
        } catch (Exception) {
            return null;
        }
    }

    /**
     * Render a metafile as PNG data, or null if it can not be rendered.
     */
    public static function tryToPng(?string $content): ?string
    {
        $image = self::tryToGdImage($content);

        return ($image === null) ? null : self::encodePng($image);
    }

    private static function encodePng(GdImage $image): string
    {
        ob_start();
        imagepng($image);

        return (string) ob_get_clean();
    }
}
