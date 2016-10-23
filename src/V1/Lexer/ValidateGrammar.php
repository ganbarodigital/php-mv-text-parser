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
 * @package   TextParser\V1\Lexer
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2016-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://ganbarodigital.github.io/php-mv-text-parser
 */

namespace GanbaroDigital\TextParser\V1\Lexer;

use GanbaroDigital\TextParser\V1\Grammars\Grammar;
use GanbaroDigital\TextParser\V1\Grammars\Reference;

class ValidateGrammar
{
    public static function check(array $grammar)
    {
        return self::checkRecursive($grammar, $grammar, 0);
    }

    private static function checkRecursive(array $grammar, array $toCheck, $depth)
    {
        // special case - too deep
        if ($depth >= 500) {
            throw new \Exception("grammar is too deep to validate");
        }

        // keep track of the problems we find
        $errors = [];

        foreach ($toCheck as $name => $clause) {
            // what children do we rely on?
            $children = $clause->getBuildingBlocks();

            // special case - a reference to another grammar
            if ($clause instanceof Reference) {
                if (!isset($grammar[$children[0]])) {
                    $errors[] = "{$name}: refers to unknown grammar {$children[0]}";
                }
            }
            else {
                // general case - a grammar that combines other grammars
                $errors = array_merge($errors, self::checkRecursive($grammar, $children, $depth+1));
            }
        }

        // all done
        return $errors;
    }
}
