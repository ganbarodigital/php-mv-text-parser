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
 * @package   TextParser\V1\Scanners
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2016-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://ganbarodigital.github.io/php-mv-text-parser
 */

namespace GanbaroDigital\TextParser\V1\Scanners;

/**
 * the behaviour supported by all of our different scanners
 */
interface Scanner
{
    /**
     * read $size amount of bytes
     *
     * @param  int $size
     *         the amount of bytes to read from the scanner
     * @return string
     *         the bytes that have been read
     */
    public function readBytes($size);

    /**
     * read all of the bytes that are left in the input stream
     *
     * @return string
     *         the bytes that have been read
     */
    public function readRemainingBytes();

    /**
     * read $size amount of bytes, but do NOT move our current position
     * in the input stream
     *
     * @param  int $size
     *         the amount of bytes to read from the scanner
     * @return string
     *         the bytes that have been read
     */
    public function readBytesAhead($size);

    /**
     * move $amount bytes in the input stream
     *
     * this is the equivalent of calling $this->readBytes() but throwing away
     * the data
     *
     * @param  int $amount
     *         the amount to move (only positive is supported)
     * @return void
     */
    public function moveBytes($amount);

    /**
     * consume whitespace characters
     *
     * this is a separate method because it needs to be line-aware
     * we don't want to do that anywhere else, because it's CPU-intensive
     *
     * @return bool
     *         TRUE if the input stream position has moved
     *         FALSE otherwise
     */
    public function movePastWhitespaceOnCurrentLine();

    /**
     * consume whitespace characters
     *
     * this is a separate method because it needs to be line-aware
     * we don't want to do that anywhere else, because it's CPU-intensive
     *
     * @return bool
     *         TRUE if the input stream position has moved
     *         FALSE otherwise
     */
    public function movePastWhitespace();

    /**
     * are we at the end of the input?
     *
     * @return bool
     *         TRUE if we are at the end
     *         FALSE otherwise
     */
    public function isAtEndOfInput();

    /**
     * where are we in the current input stream?
     *
     * @return ScannerPosition
     */
    public function getPosition();

    /**
     * move to a given position in the input stream
     *
     * @param ScannerPosition $position
     *        the position to move to
     */
    public function setPosition(ScannerPosition $position);

    /**
     * return the whole input we're scanning as a string
     *
     * this will not change the scanner's position in the input stream
     *
     * @return string
     */
    public function __toString();

    /**
     * return all of the input from the current scanning position
     *
     * this will not change the scanner's position in the input stream
     *
     * @return string
     */
    public function readAheadRemainingBytes();

    /**
     * what is this scanner (or its underlying stream) called?
     *
     * @return string
     */
    public function getLabel();

    /**
     * returns a ScannerPosition that represents the start position of
     * this stream
     *
     * @return ScannerPosition
     */
    public function getStartPosition();
}
