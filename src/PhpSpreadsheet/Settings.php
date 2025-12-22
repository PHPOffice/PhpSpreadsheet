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
        // We want phpstan to validate caller, but still need this test
        if (!is_a($rendererClassName, IRenderer::class, true)) { //* @phpstan-ignore-line
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
     * Set the HTTP client implementation to be used for network request.
     *
     * @deprecated 5.4.0 No replacement.
     *
     * @codeCoverageIgnore
     */
    public static function setHttpClient(mixed $httpClient, mixed $requestFactory): void
    {
        self::$httpClient = $httpClient;
        self::$requestFactory = $requestFactory;
    }

    /**
     * Unset the HTTP client configuration.
     *
     * @deprecated 5.4.0 No replacement.
     *
     * @codeCoverageIgnore
     */
    public static function unsetHttpClient(): void
    {
        self::$httpClient = null;
        self::$requestFactory = null;
    }

    /**
     * Get the HTTP client implementation to be used for network request.
     *
     * @deprecated 5.4.0 No replacement.
     *
     * @codeCoverageIgnore
     */
    public static function getHttpClient(): mixed
    {
        return self::$httpClient;
    }

    /**
     * Get the HTTP request factory.
     *
     * @deprecated 5.4.0 No replacement.
     *
     * @codeCoverageIgnore
     */
    public static function getRequestFactory(): mixed
    {
        return self::$requestFactory;
    }
}
