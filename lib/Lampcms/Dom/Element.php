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


namespace Lampcms\Dom;

class Element extends \DOMElement implements \Lampcms\Interfaces\LampcmsObject
{

	/**
	 * Remove all next siblings of this node
	 *
	 * This works OK
	 *
	 * @param DOMNode $node do not pass this param manually
	 * it is here only for recursively calling this function
	 *
	 * @return object $this node
	 */
	public function removeNextSiblings(\DOMNode $node = null){
		$node = ($node) ? $node : $this->nextSibling;
		if($node){
			$next = $node->nextSibling;
			if($next){
				$this->removeNextSibling($next);
			}

			$node->parentNode->removeChild($node);
		}

		return $this;
	}


	/**
	 * Remove itself from DOM Tree
	 *
	 * This works fine
	 *
	 */
	public function remove(){
		$this->parentNode->removeChild($this);
	}


	/**
	 * Remove all child nodes of this node
	 * if child nodes exist
	 *
	 * This works fine
	 *
	 * @return object $this
	 */
	public function removeChildNodes(){
		$len = $this->childNodes->length;
		while($len > 0){
			$this->removeChild($this->firstChild);
			$len--;
		}

		return $this;
	}


	/**
	 * Append xml string to this
	 * Element
	 *
	 * @param string $xml
	 * @param bool $bReplace if true then remove
	 * all child nodes first. This will have an effect of
	 * replacing all child nodes with the content
	 * of the $xml string
	 *
	 * @return object $this
	 */
	public function appendXml($xml, $bReplace = false){
		if(!is_string($xml)){
			throw new \Lampcms\Dom\XMLException('param $xml must be a string. Was: '.gettype($xml));
		}

		$oDF = $this->ownerDocument->createDocumentFragment();
		if(false === $oDF->appendXML($xml)){
			throw new Exception('Unable to load raw XML as document fragment. check the input xml string: '.$xml);
		}

		if(true === $bReplace){
			$this->removeChildNodes();
		}

		$this->appendChild($oDF);

		return $this;
	}


	/**
	 *
	 * Append CData section using
	 * contents of $cdata as CData value
	 *
	 * @param string $cdata html string which will
	 * become the content of CDATA
	 * @param string $ns namespace
	 */
	public function addCData($cdata, $ns = 'http://purl.org/rss/1.0/modules/content/'){
		if(empty($cdata)){
			return $this;
		}

		$elCdata = $this->ownerDocument->createCDATASection($cdata);
		$this->appendChild($elCdata);

		return $this;
	}


	/**
	 * Sets attribute of this element
	 *
	 * @param string $name attribute name
	 * @param string $val attribute value
	 * @return object $this
	 */
	public function addAttribute($name, $val = null){

		$this->setAttribute($name, $val);

		return $this;
	}


	/**
	 * Adds array of attributes in one operation
	 *
	 * @param array $aAttributes must be assiciative array where
	 * keys are attribute keys and values are simple values
	 *
	 * @return object $this
	 */
	public function addAttributes(array $aAttributes){
		foreach($aAttributes as $name => $val){
			$this->setAttribute($name, $val);
		}

		return $this;
	}


	/**
	 * Strips all white space nodes
	 * from the node
	 *
	 * @param object $node
	 * object of type DOMNode
	 * if not provided, then the $this->oDomDoc is used
	 *
	 * @return object $this
	 */
	public function removeWhitespace(\DOMNode $node = null){
		$node = (null === $node) ? $this : $node;

		for ($i = 0; $i < $node->childNodes->length; $i += 1) {
			$childNode = $node->childNodes->item($i);
			if (($childNode->nodeType === XML_TEXT_NODE) &&
			($childNode->isWhitespaceInElementContent())
			) {
				$node->removeChild($node->childNodes->item($i));
				$i--;
			}
			if (XML_ELEMENT_NODE === $childNode->nodeType) {
				$this->removeWhitespace($childNode);
			}
		}

		return $this;
	}


	/**
	 * Returns the first instance of requested element.
	 * If element does not exist, one will be
	 * created
	 *
	 * @param string $name name of element
	 * @return object of type DOMNode
	 */
	public function getOne($name){
		$e = $this->getElementsByTagName($name);
		$el = ($e->length > 0) ? $e->item(0) : $this->addChild($name);

		return $el;
	}


	/**
	 * Appends child element under this node
	 * and sets its value
	 *
	 * @param string $name
	 * @param string $value
	 * @return object of type clsDomElement
	 * the newly added child element
	 */
	public function addChild($name, $value = null, $ns = null){
		return $this->appendChild(new static($name, $value, $ns));

	}


	/**
	 * (non-PHPdoc)
	 * @see Lampcms\Interfaces.LampcmsObject::hashCode()
	 */
	public function hashCode(){
		return spl_object_hash($this);
	}


	/**
	 * (non-PHPdoc)
	 * @see Lampcms\Interfaces.LampcmsObject::getClass()
	 */
	public function getClass(){
		return get_class($this);
	}


	/**
	 * (non-PHPdoc)
	 * @see Lampcms\Interfaces.LampcmsObject::__toString()
	 */
	public function __toString(){
		return 'Object of type '.$this->getClass().' nodeName: '.$this->nodeName.' value: '.$this->nodeValue;
	}




	/**
	 * Get the first element that matches the tagname
	 * if element does not exist and $create param is true,
	 * then create one and append it
	 * as child of this element
	 *
	 * 
	 * @param string $name
	 * @param bool $create
	 */
	public function getElementByTagName($name, $create = false){
		$e = $this->getElementsByTagName($name);
		if($e->length > 0){
			return $e->item(0);
		}

		return (true === (bool)$create) ? $this->addChild($name): null;
	}

}
