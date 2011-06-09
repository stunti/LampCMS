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
 *    the website's Questions/Answers functionality is powered by lampcms.com
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

use Lampcms\UserFacebook;
use Lampcms\WebPage;
use Lampcms\Request;
use Lampcms\Responder;
use Lampcms\Cookie;
use Lampcms\UserGfc;

class Logout extends WebPage
{

	const GFC_SIGNOUT = '
	<script src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
  google.load("friendconnect", "0.8");
</script>
<div id="tools"><p class="larger">Logging out. Please wait...</p></div>
	<script>
	google.friendconnect.container.loadOpenSocialApi({
  site: "%s",
  onload: function(securityToken) {
    if (!window.timesloaded) {
      window.timesloaded = 1;
      google.friendconnect.requestSignOut();
    } else {
      window.timesloaded++;
    }
    if (window.timesloaded > 1) {
      window.top.location.href = "/";
    }
  }
});
	</script>
	
	';

	/**
	 * Unsets all session variables and unsets some cookies
	 * This is all that is needed to logout
	 *
	 * @param array $arrParams array or GET or POST parameters
	 */
	public function main(){

		$this->oRegistry->Dispatcher->post($this, 'onBeforeUserLogout');


		/**
		 * Don't forget about the 'dnd' cookies
		 * that may have been set previosly
		 * Whith dnd set to 1 a user may register
		 * with external auth and will never
		 * be asked to provide email address
		 * This is designed so that a user may say, hey, don't
		 * bother me with this again, I don't want to provide
		 * an email address
		 *
		 * But once the user logges out
		 * treat them as another guest!
		 */
		$aDelete = array('uid', 'dnd');

		/**
		 * If current viewer is logged in
		 * with Google Friend Connect
		 * then the logout process is somewhat
		 * different: we need to delete user's fcauth cookie(s)
		 *
		 */
		if($this->oRegistry->Viewer instanceof UserGfc){

			$GfcSiteID = $this->oRegistry->Ini->GFC_ID;
			if(!empty($GfcSiteID)){
				$gfc = sprintf(self::GFC_SIGNOUT, $GfcSiteID);
				$gfc = Responder::PAGE_OPEN.$gfc.Responder::PAGE_CLOSE;
				d('sending out GFC Logout page: '.$gfc);

				$fcauthSession = 'fcauth'.$GfcSiteID.'-s';
				 $fcauthRegular = 'fcauth'.$GfcSiteID;
				 $aDelete[] = $fcauthSession;
				 $aDelete[] = $fcauthRegular;
			}
		}

		d('logging out Facebook User');
		$aFB = $this->oRegistry->Ini->getSection('FACEBOOK');
		if(!empty($aFB) && !empty($aFB['APP_ID'])){
			$fb_cookie = 'fbs_'.$aFB['APP_ID'];
			d('deleting Facebook cookie '.$fb_cookie.' len: '.strlen($fb_cookie));
			$aDelete[] = $fb_cookie;
		}

		d('Delete these cookies: '.print_r($aDelete, 1));

		Cookie::delete($aDelete);
		/**
		 * Get copy of user data
		 * because we going to need
		 * it's values AFTER the user loggs
		 * out and after the $this->oViewer has been destroyed
		 *
		 */
		$aUser = $this->oRegistry->Viewer->getArrayCopy();
		$this->oRegistry->Viewer = null;

		session_destroy();
		$_SESSION = array();

		$this->oRegistry->Dispatcher->post($this, 'onUserLogout', $aUser);

		d('Logged out SESSION: '.print_r($_SESSION, 1));

		/*if (Request::isAjax()) {
			$sLoginForm = \Lampcms\LoginForm::makeLoginForm($this->oRegistry);
			$arrJSON = array('message'=> $sLoginForm);
			d('sending json: '.$sLoginForm);
			Responder::sendJSON($arrJSON);
			}*/

		/**
		 * For Google Friend Connect sendout
		 * the html with logout JavaScript - that's
		 * the only right way to logout
		 */
		if(isset($gfc)){
			exit($gfc);
		}

		Responder::redirectToPage('/index.php?logout=1');
	}

}
