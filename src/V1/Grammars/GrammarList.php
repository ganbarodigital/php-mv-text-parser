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
 * @package   TextParser\V1\Grammars
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2016-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://ganbarodigital.github.io/php-mv-text-parser
 */

namespace GanbaroDigital\TextParser\V1\Grammars;

use GanbaroDigital\TextParser\V1\Lexer\LexAdjuster;
use GanbaroDigital\TextParser\V1\Lexer\Lexemes;
use GanbaroDigital\TextParser\V1\Scanners\Scanner;

class GrammarList implements GrammarRule
{
    /**
     * a list of grammars that we match against
     * @var GrammarRule[]
     */
    private $buildingBlocks = [];

    /**
     * how to evaluate our values when we're done
     * @var callable|null
     */
    private $evaluator;

    public function __construct(array $list, callable $evaluator = null)
    {
        $this->buildingBlocks = $list;
        $this->evaluator = $evaluator;
    }

    /**
     * return a (possibly empty) list of the grammars that this grammer
     * is built upon
     *
     * @return GrammarRule[]
     */
    public function getBuildingBlocks()
    {
        return $this->buildingBlocks;
    }

    /**
     * describe this grammar using BNF-like syntax
     *
     * @return string
     */
    public function getPseudoBNF()
    {
        $childBNF = [];
        foreach ($this->buildingBlocks as $buildingBlock) {
            $childBNF[] = $buildingBlock->getPseudoBNF();
        }

        return implode(" ", $childBNF);
    }

    /**
     * does this grammar match against the provided text?
     *
     * @param  GrammarRule[] $grammars
     *         our dictionary of grammars
     * @param  string $lexemeName
     *         the name to assign to any lexeme we create
     * @param  Scanner $scanner
     *         the text to match
     * @param  LexAdjuster $adjuster
     *         modify the lexer behaviour to suit
     * @return array
     *         details about what happened
     */
    public function matchAgainst($grammars, $lexemeName, Scanner $scanner, LexAdjuster $adjuster)
    {
        // make any nececessary changes to the input stream
        $adjuster->adjustBeforeStartPosition($scanner);

        // remember where we started from
        $startPos = $scanner->getPosition();

        // keep track of the individual values that matched
        $values = [];

        // make sure the whole list matches
        foreach ($this->buildingBlocks as $blockName => $buildingBlock) {
            // match this block
            $matches = $buildingBlock->matchAgainst($grammars, $blockName, $scanner, $adjuster);
            if (!$matches['matched']) {
                // if one fails, all have failed
                $scanner->setPosition($startPos);
                return $matches;
            }

            // keep track of what we've found
            if ($matches['hasValue']) {
                $values[] = $matches['value'];
            }

            // make any necessary changes to the input stream
            $adjuster->adjustAfterMatch($scanner);
        }

        // if we get here, then everything in our list matched :)
        if (empty($values)) {
            return [
                'matched' => true,
                'hasValue' => false,
                'value' => null,
            ];
        }

        return [
            'matched' => true,
            'hasValue' => true,
            'value' => new Lexemes($lexemeName, $values, $this->evaluator),
        ];
    }
}
