<?php

namespace PhpOffice\PhpSpreadsheet\Shared;

use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Worksheet\Protection;

class PasswordHasher
{
    /**
     * Get algorithm name for PHP.
     */
    private static function getAlgorithm(string $algorithmName): string
    {
        if (!$algorithmName) {
            return '';
        }

        // Mapping between algorithm name in Excel and algorithm name in PHP
        $mapping = [
            Protection::ALGORITHM_MD2 => 'md2',
            Protection::ALGORITHM_MD4 => 'md4',
            Protection::ALGORITHM_MD5 => 'md5',
            Protection::ALGORITHM_SHA_1 => 'sha1',
            Protection::ALGORITHM_SHA_256 => 'sha256',
            Protection::ALGORITHM_SHA_384 => 'sha384',
            Protection::ALGORITHM_SHA_512 => 'sha512',
            Protection::ALGORITHM_RIPEMD_128 => 'ripemd128',
            Protection::ALGORITHM_RIPEMD_160 => 'ripemd160',
            Protection::ALGORITHM_WHIRLPOOL => 'whirlpool',
        ];

        if (array_key_exists($algorithmName, $mapping)) {
            return $mapping[$algorithmName];
        }

        throw new Exception('Unsupported password algorithm: ' . $algorithmName);
    }

    /**
     * Create a password hash from a given string.
     *
     * This method is based on the algorithm provided by
     * Daniel Rentz of OpenOffice and the PEAR package
     * Spreadsheet_Excel_Writer by Xavier Noguer <xnoguer@rezebra.com>.
     *
     * @param string $pPassword Password to hash
     */
    private static function defaultHashPassword(string $pPassword): string
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
     * @param string $password Password to hash
     * @param string $algorithm Hash algorithm used to compute the password hash value
     * @param string $salt Pseudorandom string
     * @param int $spinCount Number of times to iterate on a hash of a password
     *
     * @return string Hashed password
     */
    public static function hashPassword(string $password, string $algorithm = '', string $salt = '', int $spinCount = 10000): string
    {
        $phpAlgorithm = self::getAlgorithm($algorithm);
        if (!$phpAlgorithm) {
            return self::defaultHashPassword($password);
        }

        $saltValue = base64_decode($salt);
        $encodedPassword = mb_convert_encoding($password, 'UCS-2LE', 'UTF-8');

        $hashValue = hash($phpAlgorithm, $saltValue . $encodedPassword, true);
        for ($i = 0; $i < $spinCount; ++$i) {
            $hashValue = hash($phpAlgorithm, $hashValue . pack('L', $i), true);
        }

        return base64_encode($hashValue);
    }
}
