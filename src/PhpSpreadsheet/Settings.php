<?php

namespace PhpOffice\PhpSpreadsheet;

use PhpOffice\PhpSpreadsheet\Collection\Memory;
use Psr\SimpleCache\CacheInterface;

/**
 * Copyright (c) 2006 - 2016 PhpSpreadsheet.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @category   PhpSpreadsheet
 *
 * @copyright  Copyright (c) 2006 - 2016 PhpSpreadsheet (https://github.com/PHPOffice/PhpSpreadsheet)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 */
class Settings
{
    /**    Optional Chart Rendering libraries */
    const CHART_RENDERER_JPGRAPH = 'JpGraph';

    /**    Optional PDF Rendering libraries */
    const PDF_RENDERER_TCPDF = 'TcPDF';
    const PDF_RENDERER_DOMPDF = 'DomPDF';
    const PDF_RENDERER_MPDF = 'MPDF';

    private static $chartRenderers = [
        self::CHART_RENDERER_JPGRAPH,
    ];
    private static $pdfRenderers = [
        self::PDF_RENDERER_TCPDF,
        self::PDF_RENDERER_DOMPDF,
        self::PDF_RENDERER_MPDF,
    ];

    /**
     * Name of the external Library used for rendering charts
     * e.g.
     *        jpgraph.
     *
     * @var string
     */
    private static $chartRendererName;

    /**
     * Directory Path to the external Library used for rendering charts.
     *
     * @var string
     */
    private static $chartRendererPath;

    /**
     * Name of the external Library used for rendering PDF files
     * e.g.
     *         mPDF.
     *
     * @var string
     */
    private static $pdfRendererName;

    /**
     * Default options for libxml loader.
     *
     * @var int
     */
    private static $libXmlLoaderOptions = null;

    /**
     * The cache implementation to be used for cell collection.
     *
     * @var CacheInterface
     */
    private static $cache;

    /**
     * Set the locale code to use for formula translations and any special formatting.
     *
     * @param string $locale The locale code to use (e.g. "fr" or "pt_br" or "en_uk")
     *
     * @return bool Success or failure
     */
    public static function setLocale($locale)
    {
        return Calculation::getInstance()->setLocale($locale);
    }

    /**
     * Set details of the external library that PhpSpreadsheet should use for rendering charts.
     *
     * @param string $libraryName Internal reference name of the library
     *    e.g. \PhpOffice\PhpSpreadsheet\Settings::CHART_RENDERER_JPGRAPH
     * @param string $libraryBaseDir Directory path to the library's base folder
     *
     * @return bool Success or failure
     */
    public static function setChartRenderer($libraryName, $libraryBaseDir)
    {
        if (!self::setChartRendererName($libraryName)) {
            return false;
        }

        return self::setChartRendererPath($libraryBaseDir);
    }

    /**
     * Identify to PhpSpreadsheet the external library to use for rendering charts.
     *
     * @param string $libraryName Internal reference name of the library
     *    e.g. \PhpOffice\PhpSpreadsheet\Settings::CHART_RENDERER_JPGRAPH
     *
     * @return bool Success or failure
     */
    public static function setChartRendererName($libraryName)
    {
        if (!in_array($libraryName, self::$chartRenderers)) {
            return false;
        }
        self::$chartRendererName = $libraryName;

        return true;
    }

    /**
     * Tell PhpSpreadsheet where to find the external library to use for rendering charts.
     *
     * @param string $libraryBaseDir Directory path to the library's base folder
     *
     * @return bool Success or failure
     */
    public static function setChartRendererPath($libraryBaseDir)
    {
        if ((file_exists($libraryBaseDir) === false) || (is_readable($libraryBaseDir) === false)) {
            return false;
        }
        self::$chartRendererPath = $libraryBaseDir;

        return true;
    }

    /**
     * Return the Chart Rendering Library that PhpSpreadsheet is currently configured to use (e.g. jpgraph).
     *
     * @return string|null Internal reference name of the Chart Rendering Library that PhpSpreadsheet is
     *    currently configured to use
     *    e.g. \PhpOffice\PhpSpreadsheet\Settings::CHART_RENDERER_JPGRAPH
     */
    public static function getChartRendererName()
    {
        return self::$chartRendererName;
    }

    /**
     * Return the directory path to the Chart Rendering Library that PhpSpreadsheet is currently configured to use.
     *
     * @return string|null Directory Path to the Chart Rendering Library that PhpSpreadsheet is
     *     currently configured to use
     */
    public static function getChartRendererPath()
    {
        return self::$chartRendererPath;
    }

    /**
     * Identify to PhpSpreadsheet the external library to use for rendering PDF files.
     *
     * @param string $libraryName Internal reference name of the library
     *     e.g. \PhpOffice\PhpSpreadsheet\Settings::PDF_RENDERER_TCPDF,
     *          \PhpOffice\PhpSpreadsheet\Settings::PDF_RENDERER_DOMPDF
     *       or \PhpOffice\PhpSpreadsheet\Settings::PDF_RENDERER_MPDF
     */
    public static function setPdfRendererName($libraryName)
    {
        if (!in_array($libraryName, self::$pdfRenderers)) {
            throw new Exception('"' . $libraryName . '" is not a valid PDF library name');
        }
        self::$pdfRendererName = $libraryName;
    }

    /**
     * Return the PDF Rendering Library that PhpSpreadsheet is currently configured to use (e.g. dompdf).
     *
     * @return string|null Internal reference name of the PDF Rendering Library that PhpSpreadsheet is
     *     currently configured to use
     * e.g. \PhpOffice\PhpSpreadsheet\Settings::PDF_RENDERER_TCPDF,
     *       \PhpOffice\PhpSpreadsheet\Settings::PDF_RENDERER_DOMPDF
     *    or \PhpOffice\PhpSpreadsheet\Settings::PDF_RENDERER_MPDF
     */
    public static function getPdfRendererName()
    {
        return self::$pdfRendererName;
    }

    /**
     * Set default options for libxml loader.
     *
     * @param int $options Default options for libxml loader
     */
    public static function setLibXmlLoaderOptions($options)
    {
        if (is_null($options) && defined('LIBXML_DTDLOAD')) {
            $options = LIBXML_DTDLOAD | LIBXML_DTDATTR;
        }
        self::$libXmlLoaderOptions = $options;
    }

    /**
     * Get default options for libxml loader.
     * Defaults to LIBXML_DTDLOAD | LIBXML_DTDATTR when not set explicitly.
     *
     * @return int Default options for libxml loader
     */
    public static function getLibXmlLoaderOptions()
    {
        if (is_null(self::$libXmlLoaderOptions) && defined('LIBXML_DTDLOAD')) {
            self::setLibXmlLoaderOptions(LIBXML_DTDLOAD | LIBXML_DTDATTR);
        } elseif (is_null(self::$libXmlLoaderOptions)) {
            self::$libXmlLoaderOptions = true;
        }

        return self::$libXmlLoaderOptions;
    }

    /**
     * Sets the implementation of cache that should be used for cell collection.
     *
     * @param CacheInterface $cache
     */
    public static function setCache(CacheInterface $cache)
    {
        self::$cache = $cache;
    }

    /**
     * Gets the implementation of cache that should be used for cell collection.
     *
     * @return CacheInterface
     */
    public static function getCache()
    {
        if (!self::$cache) {
            self::$cache = new Memory();
        }

        return self::$cache;
    }
}
