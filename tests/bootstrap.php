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
 */

// make sure composer autoloading is there
require_once(__DIR__ . '/../vendor/autoload.php');

// any extra test code, we have to load it ourselves
require_once(__DIR__ . '/V1/Terminals/BaseTestCase.php');
require_once(__DIR__ . '/V1/Terminals/CallTrackingAdjuster.php');

function getTerminalDataSet()
{
    $retval = [
        "empty" => [ "" ],
        "integer_zero" => [ "0" ],
        "integer_one" => [ "1" ],
        "integer_positive" => [ "100" ],
        "integer_positive_max" => [ (string)PHP_INT_MAX ],
        "integer_positive_signed" => [ "+100" ],
        "integer_negative" => [ "-100" ],
        "integer_negative_min" => [ (string)PHP_INT_MIN ],
        "float_zero" => [ "0.0" ],
        "float_positive" => [ "3.1415927" ],
        "float_positive_signed" => [ "+100.10" ],
        "float_negative" => [ "-100.10" ],
        "double_quoted_string" => [ '"@100"' ],
        "single_quoted_string" => [ "'@100'" ],
        "integer_8bit_max" => [ "255" ],
        "integer_8bit_max_plus_one" => [ "256" ],
    ];

    // add entries for all individual characters, and their repetitions
    $individualChars = [
        'pling' => '!',
        'double_quote' => '"',
        'sterling_ascii' => chr(156),
        'sterling_utf8' => '£',
        'dollar' => '$',
        'percent' => '%',
        'circumflex' => '^',
        'ampersand' => '&',
        'asterisk' => '*',
        'open_bracket' => '(',
        'close_bracket' => ')',
        'underscore' => '_',
        'minus' => '-',
        'plus' => '+',
        'equals' => '=',
        'open_brace' => '{',
        'close_brace' => '}',
        'open_square_bracket' => '[',
        'close_square_bracket' => ']',
        'colon' => ':',
        'semi_colon' => ';',
        'at' => '@',
        'single_quote' => "'",
        'tilde' => '~',
        'hash' => '#',
        'less_than' => '<',
        'comma' => ',',
        'greater_than' => '>',
        'period' => '.',
        'question_mark' => '?',
        'forward_slash' => '/',
        'backslash' => '\\',
        'pipe' => '|',
        'backtick' => '`',
        'less_than_or_equal_to' => '<=',
        'greater_than_or_equal_to' => '>=',
        'greater_than_or_less_than' => '<>',
    ];

    foreach ($individualChars as $name => $chars) {
        foreach([1,2,3,4,10] as $repetition) {
            $retval[$repetition . '_' . $name] = [str_repeat($chars, $repetition)];
        }
    }

    return $retval;
}
