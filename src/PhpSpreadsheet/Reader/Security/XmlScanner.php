<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Security;

use PhpOffice\PhpSpreadsheet\Reader;

class XmlScanner
{
    /**
     * Identifies whether the thread-safe libxmlDisableEntityLoader() function is available.
     *
     * @var bool
     */
    private $libxmlDisableEntityLoader = false;

    /**
     * Store the initial setting of libxmlDisableEntityLoader so that we can resore t later.
     *
     * @var bool
     */
    private $previousLibxmlDisableEntityLoaderValue;

    /**
     * String used to identify risky xml elements.
     *
     * @var string
     */
    private $pattern;

    private $callback;

    private function __construct($pattern = '<!DOCTYPE')
    {
        $this->pattern = $pattern;
        $this->libxmlDisableEntityLoader = $this->identifyLibxmlDisableEntityLoaderAvailability();

        if ($this->libxmlDisableEntityLoader) {
            $this->previousLibxmlDisableEntityLoaderValue = libxml_disable_entity_loader(true);
        }
    }

    public function __destruct()
    {
        if ($this->libxmlDisableEntityLoader) {
            libxml_disable_entity_loader($this->previousLibxmlDisableEntityLoaderValue);
        }
    }

    public static function getInstance(Reader\IReader $reader)
    {
        switch (true) {
            case $reader instanceof Reader\Html:
                return new self('<!ENTITY');
            case $reader instanceof Reader\Xlsx:
            case $reader instanceof Reader\Xml:
            case $reader instanceof Reader\Ods:
            case $reader instanceof Reader\Gnumeric:
                return new self('<!DOCTYPE');
            default:
                return new self('<!DOCTYPE');
        }
    }

    private function identifyLibxmlDisableEntityLoaderAvailability()
    {
        if (PHP_MAJOR_VERSION == 7) {
            switch (PHP_MINOR_VERSION) {
                case 2:
                    return PHP_RELEASE_VERSION >= 1;
                case 1:
                    return PHP_RELEASE_VERSION >= 13;
                case 0:
                    return PHP_RELEASE_VERSION >= 27;
            }

            return true;
        }

        return false;
    }

    public function setAdditionalCallback(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * Scan the XML for use of <!ENTITY to prevent XXE/XEE attacks.
     *
     * @param mixed $xml
     *
     * @throws Reader\Exception
     *
     * @return string
     */
    public function scan($xml)
    {
        $pattern = '/encoding="(.*?)"/';
        $result = preg_match($pattern, $xml, $matches);
        $charset = $result ? $matches[1] : 'UTF-8';

        if ($charset !== 'UTF-8') {
            $xml = mb_convert_encoding($xml, 'UTF-8', $charset);
        }

        // Don't rely purely on libxml_disable_entity_loader()
        $pattern = '/\\0?' . implode('\\0?', str_split($this->pattern)) . '\\0?/';
        if (preg_match($pattern, $xml)) {
            throw new Reader\Exception('Detected use of ENTITY in XML, spreadsheet file load() aborted to prevent XXE/XEE attacks');
        }

        if ($this->callback !== null && is_callable($this->callback)) {
            $xml = call_user_func($this->callback, $xml);
        }

        return $xml;
    }

    /**
     * Scan theXML for use of <!ENTITY to prevent XXE/XEE attacks.
     *
     * @param string $filestream
     *
     * @throws Reader\Exception
     *
     * @return string
     */
    public function scanFile($filestream)
    {
        return $this->scan(file_get_contents($filestream));
    }
}
