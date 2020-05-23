<?php

namespace PhpOffice\PhpSpreadsheet\Shared;

class PasswordHasher
{
    const ALGORITHM_MD2 = 'MD2';
    const ALGORITHM_MD4 = 'MD4';
    const ALGORITHM_MD5 = 'MD5';
    const ALGORITHM_SHA_1 = 'SHA-1';
    const ALGORITHM_SHA_256 = 'SHA-256';
    const ALGORITHM_SHA_384 = 'SHA-384';
    const ALGORITHM_SHA_512 = 'SHA-512';
    const ALGORITHM_RIPEMD_128 = 'RIPEMD-128';
    const ALGORITHM_RIPEMD_160 = 'RIPEMD-160';
    const ALGORITHM_WHIRLPOOL = 'WHIRLPOOL';

    /**
     * Mapping between algorithm name in Excel and algorithm name in PHP.
     *
     * @var array
     */
    private static $algorithmArray = [
        self::ALGORITHM_MD2 => 'md2',
        self::ALGORITHM_MD4 => 'md4',
        self::ALGORITHM_MD5 => 'md5',
        self::ALGORITHM_SHA_1 => 'sha1',
        self::ALGORITHM_SHA_256 => 'sha256',
        self::ALGORITHM_SHA_384 => 'sha384',
        self::ALGORITHM_SHA_512 => 'sha512',
        self::ALGORITHM_RIPEMD_128 => 'ripemd128',
        self::ALGORITHM_RIPEMD_160 => 'ripemd160',
        self::ALGORITHM_WHIRLPOOL => 'whirlpool',
    ];

    /**
     * Get algorithm from self::$algorithmArray.
     *
     * @param string $pAlgorithmName
     *
     * @return string
     */
    private static function getAlgorithm($pAlgorithmName)
    {
        if (array_key_exists($pAlgorithmName, self::$algorithmArray)) {
            return self::$algorithmArray[$pAlgorithmName];
        }

        return '';
    }

    /**
     * Create a password hash from a given string.
     *
     * This method is based on the algorithm provided by
     * Daniel Rentz of OpenOffice and the PEAR package
     * Spreadsheet_Excel_Writer by Xavier Noguer <xnoguer@rezebra.com>.
     *
     * @param string $pPassword Password to hash
     *
     * @return string Hashed password
     */
    public static function defaultHashPassword($pPassword)
    {
        $password = 0x0000;
        $charPos = 1; // char position

        // split the plain text password in its component characters
        $chars = preg_split('//', $pPassword, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($chars as $char) {
            $value = ord($char) << $charPos++; // shifted ASCII value
            $rotated_bits = $value >> 15; // rotated bits beyond bit 15
            $value &= 0x7fff; // first 15 bits
            $password ^= ($value | $rotated_bits);
        }

        $password ^= strlen($pPassword);
        $password ^= 0xCE4B;

        return strtoupper(dechex($password));
    }

    /**
     * Create a password hash from a given string by a specific algorithm.
     *
     * 2.4.2.4 ISO Write Protection Method
     *
     * @see https://docs.microsoft.com/en-us/openspecs/office_file_formats/ms-offcrypto/1357ea58-646e-4483-92ef-95d718079d6f
     *
     * @param string $pPassword Password to hash
     * @param string $pAlgorithmName Hash algorithm used to compute the password hash value
     * @param string $pSaltValue Pseudorandom string
     * @param string $pSpinCount Number of times to iterate on a hash of a password
     *
     * @return string Hashed password
     */
    public static function hashPassword($pPassword, $pAlgorithmName = '', $pSaltValue = '', $pSpinCount = 10000)
    {
        $algorithmName = self::getAlgorithm($pAlgorithmName);
        if (!$pAlgorithmName) {
            return self::defaultHashPassword($pPassword);
        }

        $saltValue = base64_decode($pSaltValue);
        $password = mb_convert_encoding($pPassword, 'UCS-2LE', 'UTF-8');

        $hashValue = hash($algorithmName, $saltValue . $password, true);
        for ($i = 0; $i < $pSpinCount; ++$i) {
            $hashValue = hash($algorithmName, $hashValue . pack('L', $i), true);
        }

        return base64_encode($hashValue);
    }

    /**
     * Create a pseudorandom string.
     *
     * @param int $pSize Length of the output string in bytes
     *
     * @return string Pseudorandom string
     */
    public static function generateSalt($pSize = 16)
    {
        return base64_encode(random_bytes($pSize));
    }
}
