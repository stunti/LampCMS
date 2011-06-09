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


namespace Lampcms\Controllers;

use Lampcms\WebPage;
use Lampcms\Responder;

/**
 * This controller processes the "Select Tumblr blog" form
 * where user selects one Tumbl blog to be
 * the default blog connected to the user account.
 * 
 * That "Select" form appear only during "Connect Tumblr"
 * process (in a popup window) and only if it's detected
 * that user has more than one blog on Tumblr
 * 
 * 
 * @author Dmitri Snytkine
 *
 */
class Tumblrselect extends WebPage
{

	protected $requireToken = true;

	protected $bRequirePost = true;

	protected $bInitPageDoc = false;

	protected $JS = '
		var myclose = function(){
		window.close();
		}
		if(window.opener){
		setTimeout(myclose, 300); // give opener window time to process login and cancell intervals
		}else{
			alert("This is not a popup window or opener window gone away");
		}';



	protected function main(){
		$a = $this->oRegistry->Viewer->getTumblrBlogs();
		d('$a: '.print_r($a, 1));

		if(empty($a)){
			throw new \Exception('No blogs found for this user');
		}

		$selectedID = (int)substr($this->oRequest->get('blog'), 4);
		d('$selectedID: '. $selectedID);

		/**
		 * Pull the selected blog from array of user blogs
		 *
		 * @var unknown_type
		 */
		$aBlog = \array_splice($a, $selectedID, 1);
		d('$aBlog: '.print_r($aBlog, 1));
		d('a now: '.print_r($a, 1));

		/**
		 * Now stick this blog to the
		 * beginning of array. It will become
		 * the first element, pushing other blogs
		 * down in the array
		 * User's "Connected" blog is always
		 * the first blog in array!
		 *
		 */
		\array_unshift($a, $aBlog[0]);
		d('a after unshift: '.print_r($a, 1));

		$this->oRegistry->Viewer->setTumblrBlogs($a);
		/**
		 * Set b_tm to true which will result
		 * in "Post to Tumblr" checkbox to
		 * be checked. User can uncheck in later
		 */
		$this->oRegistry->Viewer['b_tm'] = true;
		$this->oRegistry->Viewer->save();

		$s = Responder::PAGE_OPEN. Responder::JS_OPEN.
		$this->JS.
		Responder::JS_CLOSE.
		'<div id="tools"><h2>You have successfully connected your Tumblr Blog. You should close this window now</h2><br>
		<input type="button"  class="btn-m" onClick="window.close();" value="&nbsp;Close&nbsp;"></div>'.
		Responder::PAGE_CLOSE;
		d('cp s: '.$s);
		echo $s;
		fastcgi_finish_request();
		exit;
	}

}
