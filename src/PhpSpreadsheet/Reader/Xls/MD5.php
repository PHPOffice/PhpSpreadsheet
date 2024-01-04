<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xls;

class MD5
{
    private int $a;

    private int $b;

    private int $c;

    private int $d;

    private static int $allOneBits;

    /**
     * MD5 stream constructor.
     */
    public function __construct()
    {
        self::$allOneBits = self::signedInt(0xFFFFFFFF);
        $this->reset();
    }

    /**
     * Reset the MD5 stream context.
     */
    public function reset(): void
    {
        $this->a = 0x67452301;
        $this->b = self::signedInt(0xEFCDAB89);
        $this->c = self::signedInt(0x98BADCFE);
        $this->d = 0x10325476;
    }

    /**
     * Get MD5 stream context.
     */
    public function getContext(): string
    {
        $s = '';
        foreach (['a', 'b', 'c', 'd'] as $i) {
            $v = $this->{$i};
            $s .= chr($v & 0xFF);
            $s .= chr(($v >> 8) & 0xFF);
            $s .= chr(($v >> 16) & 0xFF);
            $s .= chr(($v >> 24) & 0xFF);
        }

        return $s;
    }

    /**
     * Add data to context.
     *
     * @param string $data Data to add
     */
    public function add(string $data): void
    {
        // @phpstan-ignore-next-line
        $words = array_values(unpack('V16', $data));

        $A = $this->a;
        $B = $this->b;
        $C = $this->c;
        $D = $this->d;

        $F = [self::class, 'f'];
        $G = [self::class, 'g'];
        $H = [self::class, 'h'];
        $I = [self::class, 'i'];

        // ROUND 1
        self::step($F, $A, $B, $C, $D, $words[0], 7, 0xD76AA478);
        self::step($F, $D, $A, $B, $C, $words[1], 12, 0xE8C7B756);
        self::step($F, $C, $D, $A, $B, $words[2], 17, 0x242070DB);
        self::step($F, $B, $C, $D, $A, $words[3], 22, 0xC1BDCEEE);
        self::step($F, $A, $B, $C, $D, $words[4], 7, 0xF57C0FAF);
        self::step($F, $D, $A, $B, $C, $words[5], 12, 0x4787C62A);
        self::step($F, $C, $D, $A, $B, $words[6], 17, 0xA8304613);
        self::step($F, $B, $C, $D, $A, $words[7], 22, 0xFD469501);
        self::step($F, $A, $B, $C, $D, $words[8], 7, 0x698098D8);
        self::step($F, $D, $A, $B, $C, $words[9], 12, 0x8B44F7AF);
        self::step($F, $C, $D, $A, $B, $words[10], 17, 0xFFFF5BB1);
        self::step($F, $B, $C, $D, $A, $words[11], 22, 0x895CD7BE);
        self::step($F, $A, $B, $C, $D, $words[12], 7, 0x6B901122);
        self::step($F, $D, $A, $B, $C, $words[13], 12, 0xFD987193);
        self::step($F, $C, $D, $A, $B, $words[14], 17, 0xA679438E);
        self::step($F, $B, $C, $D, $A, $words[15], 22, 0x49B40821);

        // ROUND 2
        self::step($G, $A, $B, $C, $D, $words[1], 5, 0xF61E2562);
        self::step($G, $D, $A, $B, $C, $words[6], 9, 0xC040B340);
        self::step($G, $C, $D, $A, $B, $words[11], 14, 0x265E5A51);
        self::step($G, $B, $C, $D, $A, $words[0], 20, 0xE9B6C7AA);
        self::step($G, $A, $B, $C, $D, $words[5], 5, 0xD62F105D);
        self::step($G, $D, $A, $B, $C, $words[10], 9, 0x02441453);
        self::step($G, $C, $D, $A, $B, $words[15], 14, 0xD8A1E681);
        self::step($G, $B, $C, $D, $A, $words[4], 20, 0xE7D3FBC8);
        self::step($G, $A, $B, $C, $D, $words[9], 5, 0x21E1CDE6);
        self::step($G, $D, $A, $B, $C, $words[14], 9, 0xC33707D6);
        self::step($G, $C, $D, $A, $B, $words[3], 14, 0xF4D50D87);
        self::step($G, $B, $C, $D, $A, $words[8], 20, 0x455A14ED);
        self::step($G, $A, $B, $C, $D, $words[13], 5, 0xA9E3E905);
        self::step($G, $D, $A, $B, $C, $words[2], 9, 0xFCEFA3F8);
        self::step($G, $C, $D, $A, $B, $words[7], 14, 0x676F02D9);
        self::step($G, $B, $C, $D, $A, $words[12], 20, 0x8D2A4C8A);

        // ROUND 3
        self::step($H, $A, $B, $C, $D, $words[5], 4, 0xFFFA3942);
        self::step($H, $D, $A, $B, $C, $words[8], 11, 0x8771F681);
        self::step($H, $C, $D, $A, $B, $words[11], 16, 0x6D9D6122);
        self::step($H, $B, $C, $D, $A, $words[14], 23, 0xFDE5380C);
        self::step($H, $A, $B, $C, $D, $words[1], 4, 0xA4BEEA44);
        self::step($H, $D, $A, $B, $C, $words[4], 11, 0x4BDECFA9);
        self::step($H, $C, $D, $A, $B, $words[7], 16, 0xF6BB4B60);
        self::step($H, $B, $C, $D, $A, $words[10], 23, 0xBEBFBC70);
        self::step($H, $A, $B, $C, $D, $words[13], 4, 0x289B7EC6);
        self::step($H, $D, $A, $B, $C, $words[0], 11, 0xEAA127FA);
        self::step($H, $C, $D, $A, $B, $words[3], 16, 0xD4EF3085);
        self::step($H, $B, $C, $D, $A, $words[6], 23, 0x04881D05);
        self::step($H, $A, $B, $C, $D, $words[9], 4, 0xD9D4D039);
        self::step($H, $D, $A, $B, $C, $words[12], 11, 0xE6DB99E5);
        self::step($H, $C, $D, $A, $B, $words[15], 16, 0x1FA27CF8);
        self::step($H, $B, $C, $D, $A, $words[2], 23, 0xC4AC5665);

        // ROUND 4
        self::step($I, $A, $B, $C, $D, $words[0], 6, 0xF4292244);
        self::step($I, $D, $A, $B, $C, $words[7], 10, 0x432AFF97);
        self::step($I, $C, $D, $A, $B, $words[14], 15, 0xAB9423A7);
        self::step($I, $B, $C, $D, $A, $words[5], 21, 0xFC93A039);
        self::step($I, $A, $B, $C, $D, $words[12], 6, 0x655B59C3);
        self::step($I, $D, $A, $B, $C, $words[3], 10, 0x8F0CCC92);
        self::step($I, $C, $D, $A, $B, $words[10], 15, 0xFFEFF47D);
        self::step($I, $B, $C, $D, $A, $words[1], 21, 0x85845DD1);
        self::step($I, $A, $B, $C, $D, $words[8], 6, 0x6FA87E4F);
        self::step($I, $D, $A, $B, $C, $words[15], 10, 0xFE2CE6E0);
        self::step($I, $C, $D, $A, $B, $words[6], 15, 0xA3014314);
        self::step($I, $B, $C, $D, $A, $words[13], 21, 0x4E0811A1);
        self::step($I, $A, $B, $C, $D, $words[4], 6, 0xF7537E82);
        self::step($I, $D, $A, $B, $C, $words[11], 10, 0xBD3AF235);
        self::step($I, $C, $D, $A, $B, $words[2], 15, 0x2AD7D2BB);
        self::step($I, $B, $C, $D, $A, $words[9], 21, 0xEB86D391);

        $this->a = ($this->a + $A) & self::$allOneBits;
        $this->b = ($this->b + $B) & self::$allOneBits;
        $this->c = ($this->c + $C) & self::$allOneBits;
        $this->d = ($this->d + $D) & self::$allOneBits;
    }

    private static function f(int $X, int $Y, int $Z): int
    {
        return ($X & $Y) | ((~$X) & $Z); // X AND Y OR NOT X AND Z
    }

    private static function g(int $X, int $Y, int $Z): int
    {
        return ($X & $Z) | ($Y & (~$Z)); // X AND Z OR Y AND NOT Z
    }

    private static function h(int $X, int $Y, int $Z): int
    {
        return $X ^ $Y ^ $Z; // X XOR Y XOR Z
    }

    private static function i(int $X, int $Y, int $Z): int
    {
        return $Y ^ ($X | (~$Z)); // Y XOR (X OR NOT Z)
    }

    /** @param float|int $t may be float on 32-bit system */
    private static function step(callable $func, int &$A, int $B, int $C, int $D, int $M, int $s, $t): void
    {
        $t = self::signedInt($t);
        $A = (int) ($A + call_user_func($func, $B, $C, $D) + $M + $t) & self::$allOneBits;
        $A = self::rotate($A, $s);
        $A = (int) ($B + $A) & self::$allOneBits;
    }

    /** @param float|int $result may be float on 32-bit system */
    private static function signedInt($result): int
    {
        return is_int($result) ? $result : (int) (PHP_INT_MIN + $result - 1 - PHP_INT_MAX);
    }

    private static function rotate(int $decimal, int $bits): int
    {
        $binary = str_pad(decbin($decimal), 32, '0', STR_PAD_LEFT);

        return self::signedInt(bindec(substr($binary, $bits) . substr($binary, 0, $bits)));
    }
}
