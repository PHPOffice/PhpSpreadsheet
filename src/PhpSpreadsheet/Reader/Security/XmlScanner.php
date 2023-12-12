<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Security;

use PhpOffice\PhpSpreadsheet\Reader;

class XmlScanner
{
    private string $pattern;

    /** @var ?callable */
    private $callback;

    public function __construct(string $pattern = '<!DOCTYPE')
    {
        $this->pattern = $pattern;
    }

    public static function getInstance(Reader\IReader $reader): self
    {
        $pattern = ($reader instanceof Reader\Html) ? '<!ENTITY' : '<!DOCTYPE';

        return new self($pattern);
    }

    /**
     * @codeCoverageIgnore
     *
     * @deprecated this has no effect at all and always return false. All usages must be removed.
     */
    public static function threadSafeLibxmlDisableEntityLoaderAvailability(): bool
    {
        return false;
    }

    public function setAdditionalCallback(callable $callback): void
    {
        $this->callback = $callback;
    }

    private static function forceString(mixed $arg): string
    {
        return is_string($arg) ? $arg : '';
    }

    private function toUtf8(string $xml): string
    {
        $pattern = '/encoding="(.*?)"/';
        $result = preg_match($pattern, $xml, $matches);
        $charset = strtoupper($result ? $matches[1] : 'UTF-8');

        if ($charset !== 'UTF-8') {
            $xml = self::forceString(mb_convert_encoding($xml, 'UTF-8', $charset));

            $result = preg_match($pattern, $xml, $matches);
            $charset = strtoupper($result ? $matches[1] : 'UTF-8');
            if ($charset !== 'UTF-8') {
                throw new Reader\Exception('Suspicious Double-encoded XML, spreadsheet file load() aborted to prevent XXE/XEE attacks');
            }
        }

        return $xml;
    }

    /**
     * Scan the XML for use of <!ENTITY to prevent XXE/XEE attacks.
     *
     * @param false|string $xml
     */
    public function scan($xml): string
    {
        $xml = "$xml";

        $xml = $this->toUtf8($xml);

        // Don't rely purely on libxml_disable_entity_loader()
        $pattern = '/\\0?' . implode('\\0?', str_split($this->pattern)) . '\\0?/';

        if (preg_match($pattern, $xml)) {
            throw new Reader\Exception('Detected use of ENTITY in XML, spreadsheet file load() aborted to prevent XXE/XEE attacks');
        }

        if ($this->callback !== null) {
            $xml = call_user_func($this->callback, $xml);
        }

        return $xml;
    }

    /**
     * Scan theXML for use of <!ENTITY to prevent XXE/XEE attacks.
     */
    public function scanFile(string $filestream): string
    {
        return $this->scan(file_get_contents($filestream));
    }
}
