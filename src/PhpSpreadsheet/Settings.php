<?php

namespace PhpOffice\PhpSpreadsheet;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Chart\Renderer\IRenderer;
use PhpOffice\PhpSpreadsheet\Collection\Memory;
use Psr\SimpleCache\CacheInterface;
use ReflectionClass;

class Settings
{
    /**
     * Class name of the chart renderer used for rendering charts
     * eg: PhpOffice\PhpSpreadsheet\Chart\Renderer\JpGraph.
     *
     * @var null|class-string<IRenderer>
     */
    private static ?string $chartRenderer = null;

    /**
     * Default options for libxml loader.
     */
    private static ?int $libXmlLoaderOptions = null;

    /**
     * The cache implementation to be used for cell collection.
     */
    private static ?CacheInterface $cache = null;

    private static mixed $httpClient = null;

    private static mixed $requestFactory = null;

    /**
     * Set the locale code to use for formula translations and any special formatting.
     *
     * @param string $locale The locale code to use (e.g. "fr" or "pt_br" or "en_uk")
     *
     * @return bool Success or failure
     */
    public static function setLocale(string $locale): bool
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
     * @param class-string<IRenderer> $rendererClassName Class name of the chart renderer
     *    eg: PhpOffice\PhpSpreadsheet\Chart\Renderer\JpGraph
     */
    public static function setChartRenderer(string $rendererClassName): void
    {
        if (!is_a($rendererClassName, IRenderer::class, true)) {
            throw new Exception('Chart renderer must implement ' . IRenderer::class);
        }

        self::$chartRenderer = $rendererClassName;
    }

    public static function unsetChartRenderer(): void
    {
        self::$chartRenderer = null;
    }

    /**
     * Return the Chart Rendering Library that PhpSpreadsheet is currently configured to use.
     *
     * @return null|class-string<IRenderer> Class name of the chart renderer
     *    eg: PhpOffice\PhpSpreadsheet\Chart\Renderer\JpGraph
     */
    public static function getChartRenderer(): ?string
    {
        return self::$chartRenderer;
    }

    public static function htmlEntityFlags(): int
    {
        return ENT_COMPAT;
    }

    /**
     * Set default options for libxml loader.
     *
     * @param ?int $options Default options for libxml loader
     *
     * @deprecated 3.5.0 no longer needed
     */
    public static function setLibXmlLoaderOptions(?int $options): int
    {
        if ($options === null) {
            $options = defined('LIBXML_DTDLOAD') ? (LIBXML_DTDLOAD | LIBXML_DTDATTR) : 0;
        }
        self::$libXmlLoaderOptions = $options;

        return $options;
    }

    /**
     * Get default options for libxml loader.
     * Defaults to LIBXML_DTDLOAD | LIBXML_DTDATTR when not set explicitly.
     *
     * @return int Default options for libxml loader
     *
     * @deprecated 3.5.0 no longer needed
     */
    public static function getLibXmlLoaderOptions(): int
    {
        return self::$libXmlLoaderOptions ?? (defined('LIBXML_DTDLOAD') ? (LIBXML_DTDLOAD | LIBXML_DTDATTR) : 0);
    }

    /**
     * Sets the implementation of cache that should be used for cell collection.
     */
    public static function setCache(?CacheInterface $cache): void
    {
        self::$cache = $cache;
    }

    /**
     * Gets the implementation of cache that is being used for cell collection.
     */
    public static function getCache(): CacheInterface
    {
        if (!self::$cache) {
            self::$cache = self::useSimpleCacheVersion3() ? new Memory\SimpleCache3() : new Memory\SimpleCache1();
        }

        return self::$cache;
    }

    public static function useSimpleCacheVersion3(): bool
    {
        return (new ReflectionClass(CacheInterface::class))->getMethod('get')->getReturnType() !== null;
    }

    /**
     * @deprecated 2.4.3 No replacement.
     *
     * @codeCoverageIgnore
     */
    public static function setHttpClient(mixed $httpClient, mixed $requestFactory): void
    {
        self::$httpClient = $httpClient;
        self::$requestFactory = $requestFactory;
    }

    /**
     * @deprecated 2.4.3 No replacement.
     *
     * @codeCoverageIgnore
     */
    public static function unsetHttpClient(): void
    {
        self::$httpClient = null;
        self::$requestFactory = null;
    }

    /**
     * @deprecated 2.4.3 No replacement.
     *
     * @codeCoverageIgnore
     */
    public static function getHttpClient(): mixed
    {
        return self::$httpClient;
    }

    /**
     * @deprecated 2.4.3 No replacement.
     *
     * @codeCoverageIgnore
     */
    public static function getRequestFactory(): mixed
    {
        return self::$requestFactory;
    }
}
