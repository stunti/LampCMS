<?php
/**
 *
 * License, TERMS and CONDITIONS
 *
 * This software is lisensed under the GNU LESSER GENERAL PUBLIC LICENSE (LGPL) version 3
 * Please read the license here : http://www.gnu.org/licenses/lgpl-3.0.txt
 *
 *  Redistribution and use in source and binary forms, with or without
 *  modification, are permitted provided that the following conditions are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 * 3. The name of the author may not be used to endorse or promote products
 *    derived from this software without specific prior written permission.
 *
 * ATTRIBUTION REQUIRED
 * 4. All web pages generated by the use of this software, or at least
 * 	  the page that lists the recent questions (usually home page) must include
 *    a link to the http://www.lampcms.com and text of the link must indicate that
 *    the website\'s Questions/Answers functionality is powered by lampcms.com
 *    An example of acceptable link would be "Powered by <a href="http://www.lampcms.com">LampCMS</a>"
 *    The location of the link is not important, it can be in the footer of the page
 *    but it must not be hidden by style attibutes
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR "AS IS" AND ANY EXPRESS OR IMPLIED
 * WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE FREEBSD PROJECT OR CONTRIBUTORS BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
 * THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This product includes GeoLite data created by MaxMind,
 *  available from http://www.maxmind.com/
 *
 *
 * @author     Dmitri Snytkine <cms@lampcms.com>
 * @copyright  2005-2011 (or current year) ExamNotes.net inc.
 * @license    http://www.gnu.org/licenses/lgpl-3.0.txt GNU LESSER GENERAL PUBLIC LICENSE (LGPL) version 3
 * @link       http://www.lampcms.com   Lampcms.com project
 * @version    Release: @package_version@
 *
 *
 */


namespace Lampcms\String;

/**
 * String tokenizer
 * Minics the behaviour
 * or java.unil.StringTokenizer class
 *
 * If you need to just get array
 * of tokens and not bother with
 * the nextToken() methods you can
 * just call getArrayCopy() on this object
 * and get array of tokens
 *
 * @author Dmitri Snytkine
 *
 */
class Tokenizer extends \ArrayObject
{
	/**
	 * By default the string will be split
	 * buy one or more space or tab or new line char
	 *
	 * @var string Regex pattern
	 */
	protected $delim = '/([\s]+)/';

	protected $origString;

	protected $iterator;

	/**
	 * @param string $string Original string to be tokenized
	 * @param string $delim must be a valid PCRE pattern!
	 * It's recommended to add the /u switch to pattern to treat
	 * chars in pattern as UTF-8 chars
	 */
	public function __construct($string, $delim = '/([\s,]+)/u'){
		if(!is_string($string) || !is_string($delim)){
			throw new \InvalidArgimentException('$string and $delim params must be string');
		}

		parent::__construct(array());
		$this->origString = $string;
		$this->delim = $delim;

		$this->exchangeArray($this->parse($string));
		$this->iterator = $this->getIterator();
	}


	public function getDelim(){
		return $this->delim;
	}


	public function getOrigString(){
		return $this->origString;
	}


	/**
	 * Parse string into tokens
	 * Sub-class may override this to
	 * tokenize any other way.
	 * For example, may also apply a filter
	 * like array_unique or filter agains stopwords list
	 *
	 * @return array array of tokens (individual parts of string)
	 */
	public function parse(){
		$a = \preg_split($this->delim, $this->origString, -1, PREG_SPLIT_NO_EMPTY);
		d('tokenized: '.print_r($a, 1));

		return $a;
	}


	/**
	 * Tests if there are more tokens available
	 * from this tokenizer's string
	 *
	 * @return bool true if there are more tokens
	 *
	 */
	public function hasMoveTokens(){
		return $this->iterator->valid();
	}


	/**
	 *
	 * Returns the next token from this string tokenizer
	 *
	 * @return string
	 */
	public function nextToken(){
		$s = $this->iterator->current();
		$this->iterator->next();

		return $s;
	}


	/**
	 *
	 * Calculates the number of times that this tokenizer's
	 * nextToken method can be called
	 * before it generates an exception.
	 *
	 * @return int number of tokens in the origString
	 */
	public function countTokens(){
		return $this->count();
	}

}
