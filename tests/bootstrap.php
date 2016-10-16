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


function getTokenDataSet()
{
    return [
        "number" => [ "123456789" ],
        "string" => [ '"@100"' ],
        "pling" => [ "!" ],
        "logical_not" => [ "!" ],
        "double_quotes" => [ '"' ],
        "sterling" => [ '£' ],
        'dollar' => [ '$' ],
        'double_dollar' => [ '$$' ],
        'percentage' => [ '%' ],
        'circumflex' => [ '^' ],
        'logical_xor' => [ '^' ],
        'ampersand' => [ '&' ],
        'logical_ampersand' => [ '&&' ],
        'asterix' => [ '*' ],
        'exponential' => [ '**' ],
        'open_bracket' => [ '(' ],
        'close_bracket' => [ ')' ],
        'minus' => [ '-' ],
        'decrement' => [ '--' ],
        'minus_equals' => [ '-=' ],
        'plus' => [ '+' ],
        'increment' => [ '++'] ,
        'plus_equals' => [ '+=' ],
        'underscore' => [ '_' ],
        'assignment' => [ '=' ],
        'assignment_append' => [ '.=' ],
        'equals' => [ '==' ],
        'strict_equals' => [ '===' ],
        'not_equals' => [ '!=' ],
        'strict_not_equals' => [ '!==' ],
        'double_at' => [ '@@' ],
        'open_brace' => [ '{' ],
        'close_brace' => [ '}' ],
        'open_square_bracket' => [ '[' ],
        'close_square_bracket' => [ ']' ],
        'colon' => [ ':' ],
        'semi-colon' => [ ';' ],
        'at' => [ '@' ],
        'double_at' => [ '@@' ],
        'single_quote' => [ "'" ],
        'tilde' => [ '~' ],
        'logical_not' => [ '~' ],
        'hash' => [ '#' ],
        'less_than' => [ '<' ],
        'less_than_or_equal_to' => [ '<=' ],
        'left_shift' => [ '<<' ],
        'here_doc' => [ '<<<' ],
        'greater_than' => [ '>' ],
        'greater_than_or_equal_to' => [ '>=' ],
        'right_shift' => [ '>>' ],
        'spaceship' => [ '<=>' ],
        'greater_than_or_less_than' => [ '<>' ],
        'comma' => [ ',' ],
        'period' => [ '.' ],
        'question_mark' => [ '?' ],
        'null_coalescing' => [ '??' ],
        'pipe' => [ '|' ],
        'logical_or' => [ '||' ],
        'backtick' => [ '`'],
    ];
}
