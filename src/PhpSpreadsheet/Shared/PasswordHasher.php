<?php

namespace PhpOffice\PhpSpreadsheet\Shared;

class PasswordHasher
{
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
    public static function hashPassword($pPassword)
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
}
