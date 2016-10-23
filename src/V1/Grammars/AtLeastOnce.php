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

use GanbaroDigital\TextParser\V1\Lexer\Lexemes;

class AtLeastOnce implements Grammar
{
    /**
     * the grammar that must match at least once
     *
     * @var Grammar
     */
    private $buildingBlock;

    /**
     * what separates the items in the list that we are parsing?
     *
     * @var Grammar
     */
    private $separator;

    /**
     * how to evaluate our value
     * @var callable
     */
    private $evaluator;

    public function __construct(Grammar $buildingBlock, Grammar $separator, callable $evaluator = null)
    {
        $this->buildingBlock = $buildingBlock;
        $this->separator = $separator;
        $this->evaluator = $evaluator;
    }

    /**
     * return a (possibly empty) list of the grammars that this grammer
     * is built upon
     *
     * @return Grammar[]
     */
    public function getBuildingBlocks()
    {
        return [$this->buildingBlock, $this->separator];
    }

    /**
     * describe this grammar using BNF-like syntax
     *
     * @return string
     */
    public function getPseudoBNF()
    {
        $childBNF = $this->buildingBlock->getPseudoBNF();
        $sepBNF = $this->separator->getPseudoBNF();

        return "{$childBNF} [{$sepBNF} {$childBNF} ...]";
    }

    /**
     * does this grammar match against the provided text?
     *
     * @param  Grammars[] $grammars
     *         our dictionary of grammars
     * @param  string $lexemeName
     *         the name to assign to any lexeme we create
     * @param  string $text
     *         the text to match
     * @return array
     *         details about what happened
     */
    public function matchAgainst($grammars, $lexemeName, $text)
    {
        // keep track of what has matched
        $values = [];
        $hasMatched = false;

        $done = false;
        while(!$done) {
            $matches = $this->buildingBlock->matchAgainst($grammars, 'item', $text);
            if (!$matches['matched']) {
                break;
            }
            $hasMatched = true;
            if ($matches['hasValue']) {
                $values[] = $matches['value'];
            }
            $text = $matches['remaining'];

            $matches = $this->separator->matchAgainst($grammars, 'separator', $text);
            if (!$matches['matched']) {
                break;
            }

            if ($matches['hasValue']) {
                $values[] = $matches['value'];
            }
            $text = $matches['remaining'];
        }

        // did we match anything?
        if (count($values) > 0) {
            // yes we did
            return [
                'matched' => true,
                'hasValue' => true,
                'value' => new Lexemes($lexemeName, $values, $this->evaluator),
                'remaining' => $text
            ];
        }
        if ($hasMatched) {
            return [
                'matched' => true,
                'hasValue' => false,
                'value' => new Lexemes($lexemeName, [], $this->evaluator),
                'remaining' => $text
            ];
        }

        // if we get here, then nothing matched
        return ['matched' => false];
    }
}
