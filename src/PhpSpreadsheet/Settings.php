<?php

namespace PhpOffice\PhpSpreadsheet;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Chart\Renderer\IRenderer;
use PhpOffice\PhpSpreadsheet\Collection\Memory;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\SimpleCache\CacheInterface;

class Settings
{
    /**
     * Class name of the chart renderer used for rendering charts
     * eg: PhpOffice\PhpSpreadsheet\Chart\Renderer\JpGraph.
     *
     * @var string
     */
    private static $chartRenderer;

    /**
     * Default options for libxml loader.
     *
     * @var int
     */
    private static $libXmlLoaderOptions;

    /**
     * The cache implementation to be used for cell collection.
     *
     * @var CacheInterface
     */
    private static $cache;

    /**
     * The HTTP client implementation to be used for network request.
     *
     * @var null|ClientInterface
     */
    private static $httpClient;

    /**
     * @var null|RequestFactoryInterface
     */
    private static $requestFactory;

    /**
     * Set the locale code to use for formula translations and any special formatting.
     *
     * @param string $locale The locale code to use (e.g. "fr" or "pt_br" or "en_uk")
     *
     * @return bool Success or failure
     */
    public static function setLocale(string $locale)
    {
        return Calculation::getInstance()->setLocale($locale);
    }

    public static function getLocale(): string
    {
        return Calculation::getInstance()->getLocale();
    }

    /**
     * Identify to PhpSpreadsheet the external library to use for rendering charts.
     *
     * @param string $rendererClassName Class name of the chart renderer
     *    eg: PhpOffice\PhpSpreadsheet\Chart\Renderer\JpGraph
     */
    public static function setChartRenderer(string $rendererClassName): void
    {
        if (!is_a($rendererClassName, IRenderer::class, true)) {
            throw new Exception('Chart renderer must implement ' . IRenderer::class);
        }

        self::$chartRenderer = $rendererClassName;
    }

    /**
     * Return the Chart Rendering Library that PhpSpreadsheet is currently configured to use.
     *
     * @return null|string Class name of the chart renderer
     *    eg: PhpOffice\PhpSpreadsheet\Chart\Renderer\JpGraph
     */
    public static function getChartRenderer(): ?string
    {
        return self::$chartRenderer;
    }

    public static function htmlEntityFlags(): int
    {
        return \ENT_COMPAT;
    }

    /**
     * Set default options for libxml loader.
     *
     * @param int $options Default options for libxml loader
     */
    public static function setLibXmlLoaderOptions($options): void
    {
        if ($options === null && defined('LIBXML_DTDLOAD')) {
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
    public static function getLibXmlLoaderOptions(): int
    {
        if (self::$libXmlLoaderOptions === null && defined('LIBXML_DTDLOAD')) {
            self::setLibXmlLoaderOptions(LIBXML_DTDLOAD | LIBXML_DTDATTR);
        } elseif (self::$libXmlLoaderOptions === null) {
            self::$libXmlLoaderOptions = 0;
        }

        return self::$libXmlLoaderOptions;
    }

    /**
     * Deprecated, has no effect.
     *
     * @param bool $state
     *
     * @deprecated will be removed without replacement as it is no longer necessary on PHP 7.3.0+
     */
    public static function setLibXmlDisableEntityLoader($state): void
    {
        // noop
    }

    /**
     * Deprecated, has no effect.
     *
     * @return bool $state
     *
     * @deprecated will be removed without replacement as it is no longer necessary on PHP 7.3.0+
     */
    public static function getLibXmlDisableEntityLoader(): bool
    {
        return true;
    }

    /**
     * Sets the implementation of cache that should be used for cell collection.
     */
    public static function setCache(CacheInterface $cache): void
    {
        self::$cache = $cache;
    }

    /**
     * Gets the implementation of cache that is being used for cell collection.
     */
    public static function getCache(): CacheInterface
    {
        if (!self::$cache) {
            self::$cache = new Memory();
        }

        return self::$cache;
    }

    /**
     * Set the HTTP client implementation to be used for network request.
     */
    public static function setHttpClient(ClientInterface $httpClient, RequestFactoryInterface $requestFactory): void
    {
        self::$httpClient = $httpClient;
        self::$requestFactory = $requestFactory;
    }

    /**
     * Unset the HTTP client configuration.
     */
    public static function unsetHttpClient(): void
    {
        self::$httpClient = null;
        self::$requestFactory = null;
    }

    /**
     * Get the HTTP client implementation to be used for network request.
     */
    public static function getHttpClient(): ClientInterface
    {
        self::assertHttpClient();

        return self::$httpClient;
    }

    /**
     * Get the HTTP request factory.
     */
    public static function getRequestFactory(): RequestFactoryInterface
    {
        self::assertHttpClient();

        return self::$requestFactory;
    }

    private static function assertHttpClient(): void
    {
        if (!self::$httpClient || !self::$requestFactory) {
            throw new Exception('HTTP client must be configured via Settings::setHttpClient() to be able to use WEBSERVICE function.');
        }
    }
}
