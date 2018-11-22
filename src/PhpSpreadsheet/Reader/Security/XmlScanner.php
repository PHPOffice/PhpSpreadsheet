<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Security;

use PhpOffice\PhpSpreadsheet\Reader\Exception;

class XmlScanner
{
    /**
     * Identifies whether the thread-safe libxmlDisableEntityLoader() function is available.
     *
     * @var bool
     */
    private $libxmlDisableEntityLoader = false;

    private $pattern;

    public function __construct($pattern = '<!DOCTYPE')
    {
        $this->pattern = $pattern;
        $this->libxmlDisableEntityLoader = $this->identifyLibxmlDisableEntityLoaderAvailability();

        if ($this->libxmlDisableEntityLoader) {
            libxml_disable_entity_loader(true);
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

    /**
     * Scan the XML for use of <!ENTITY to prevent XXE/XEE attacks.
     *
     * @param mixed $xml
     *
     * @throws Exception
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
            throw new Exception('Detected use of ENTITY in XML, spreadsheet file load() aborted to prevent XXE/XEE attacks');
        }

        return $xml;
    }

    /**
     * Scan theXML for use of <!ENTITY to prevent XXE/XEE attacks.
     *
     * @param string $filestream
     *
     * @throws Exception
     *
     * @return string
     */
    public function scanFile($filestream)
    {
        return $this->scan(file_get_contents($filestream));
    }
}
