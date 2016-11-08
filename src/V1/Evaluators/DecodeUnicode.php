<?php

/**
 * Copyright (c) 2016-present Ganbaro Digital Ltd
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the names of the copyright holders nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  Libraries
 * @package   TextParser\V1\Evaluators
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2016-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://ganbarodigital.github.io/php-mv-text-parser
 */

namespace GanbaroDigital\TextParser\V1\Evaluators;

class DecodeUnicode
{
    /**
     * Decode Unicode Characters from \u0000 ASCII syntax.
     *
     * This algorithm was originally developed for the
     * Solar Framework by Paul M. Jones
     *
     * @link   http://solarphp.com/
     * @link   https://github.com/solarphp/core/blob/master/Solar/Json.php
     * @param  string $value
     * @return string
     */
    public function __invoke($value)
    {
        // robustness!
        if (!is_string($value)) {
            return $value;
        }

        // special case - any encodings to worry about?
        if (!preg_match('/\\\u[0-9A-Fa-f]{4}/', $value)) {
            return $value;
        }

        $utf8       = '';
        $strlenChrs = strlen($value);

        for ($i = 0; $i < $strlenChrs; $i++) {
            $ordChrsC = ord($value[$i]);

            switch (true) {
                case preg_match('/\\\u[0-9A-F]{4}/i', substr($value, $i, 6)):
                    // single, escaped unicode character
                    $utf16 = chr(hexdec(substr($value, ($i + 2), 2)))
                           . chr(hexdec(substr($value, ($i + 4), 2)));
                    $utf8char = $this->utf162utf8($utf16);
                    $search  = ['\\', "\n", "\t", "\r", chr(0x08), chr(0x0C), '"', '\'', '/'];
                    if (in_array($utf8char, $search)) {
                        $replace = ['\\\\', '\\n', '\\t', '\\r', '\\b', '\\f', '\\"', '\\\'', '\\/'];
                        $utf8char  = str_replace($search, $replace, $utf8char);
                    }
                    $utf8 .= $utf8char;
                    $i += 5;
                    break;
                case ($ordChrsC >= 0x20) && ($ordChrsC <= 0x7F):
                    $utf8 .= $value{$i};
                    break;
                case ($ordChrsC & 0xE0) == 0xC0:
                    // characters U-00000080 - U-000007FF, mask 110XXXXX
                    //see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                    $utf8 .= substr($value, $i, 2);
                    ++$i;
                    break;
                case ($ordChrsC & 0xF0) == 0xE0:
                    // characters U-00000800 - U-0000FFFF, mask 1110XXXX
                    // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                    $utf8 .= substr($value, $i, 3);
                    $i += 2;
                    break;
                case ($ordChrsC & 0xF8) == 0xF0:
                    // characters U-00010000 - U-001FFFFF, mask 11110XXX
                    // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                    $utf8 .= substr($value, $i, 4);
                    $i += 3;
                    break;
                case ($ordChrsC & 0xFC) == 0xF8:
                    // characters U-00200000 - U-03FFFFFF, mask 111110XX
                    // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                    $utf8 .= substr($value, $i, 5);
                    $i += 4;
                    break;
                case ($ordChrsC & 0xFE) == 0xFC:
                    // characters U-04000000 - U-7FFFFFFF, mask 1111110X
                    // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                    $utf8 .= substr($value, $i, 6);
                    $i += 5;
                    break;
            }
        }

        return $utf8;
    }

    /**
     * Convert a string from one UTF-16 char to one UTF-8 char.
     *
     * Normally should be handled by mb_convert_encoding, but provides a slower
     * PHP-only method for installations that lack the multibyte string
     * extension.
     *
     * This method is from the Solar Framework by Paul M. Jones.
     *
     * @link   http://solarphp.com
     * @param  string $utf16 UTF-16 character
     * @return string UTF-8 character
     */
    protected function utf162utf8($utf16)
    {
        // Check for mb extension otherwise do by hand.
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($utf16, 'UTF-8', 'UTF-16');
        }

        $bytes = (ord($utf16{0}) << 8) | ord($utf16{1});

        switch (true) {
            case ((0x7F & $bytes) == $bytes):
                // This case should never be reached, because we are in ASCII range;
                // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                return chr(0x7F & $bytes);

            case (0x07FF & $bytes) == $bytes:
                // Return a 2-byte UTF-8 character;
                // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                return chr(0xC0 | (($bytes >> 6) & 0x1F))
                    . chr(0x80 | ($bytes & 0x3F));

            case (0xFFFF & $bytes) == $bytes:
                // Return a 3-byte UTF-8 character;
                // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                return chr(0xE0 | (($bytes >> 12) & 0x0F))
                    . chr(0x80 | (($bytes >> 6) & 0x3F))
                    . chr(0x80 | ($bytes & 0x3F));
        }

        // ignoring UTF-32 for now, sorry
        return '';
    }
}
