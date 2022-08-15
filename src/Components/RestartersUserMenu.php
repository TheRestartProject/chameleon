<?php
/**
 * File holding the PersonalTools class
 *
 * This file is part of the MediaWiki skin Chameleon.
 *
 * @copyright 2013 - 2018, Stephan Gambke
 * @license   GNU General Public License, version 3 (or any later version)
 *
 * The Chameleon skin is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by the Free
 * Software Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * The Chameleon skin is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @file
 * @ingroup   Skins
 */

namespace Skins\Chameleon\Components;

/**
 * The PersonalTools class.
 *
 * An unordered list of personal tools: <ul id="p-personal" >...
 *
 * @author Stephan Gambke
 * @since 1.0
 * @ingroup Skins
 */
class RestartersUserMenu extends Component {

	/**
	 * Builds the HTML code for this component
	 *
	 * @return String the HTML code
	 * @throws \MWException
	 */
	public function getHtml() {
        $restartersBaseUrl = $GLOBALS['rootLaravelDomain'] ?? 'https://restarters.net';
		$user = $this->getSkinTemplate()->getSkin()->getUser();

		$ret = $this->indent() . '<!-- user menu -->';

        if ($user->isLoggedIn()) {
            $ret .= '<ul class="nav-right">
        <li style="width:99px;text-align:right">';

            $ch = curl_init();

            // set url
            curl_setopt($ch, CURLOPT_URL, $restartersBaseUrl."/user/thumbnail?wiki_username=" . $user->mName);

            //return the transfer as a string
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            // $output contains the output string 
            $result = json_decode(trim(curl_exec($ch)));

            $ret .= '<li class="nav-item dropdown"  style="width:99px">
          <a href="#" id="navbarDropdown" class="nav-link dropdown-toggle" role="button" data-target="#account-nav" aria-controls="account-nav" data-toggle="collapse" aria-haspopup="true" aria-expanded="false" aria-label="Toggle account navigation">
                <img class="avatar" src="'. $result . '" />
          </a>

          <div id="account-nav" class="dropdown-menu collapse navbar-dropdown" aria-labelledby="navbarDropdown">


          <ul>';
            curl_setopt($ch, CURLOPT_URL, $restartersBaseUrl."/user/menus?wiki_username=" . $user->mName);

            //return the transfer as a string
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $result = json_decode(trim(curl_exec($ch)));

            if ($result) {
                foreach ($result as $key => $menu) {
                    $ret .= '<li><span>';
                    $ret .= $menu->svg;
                    $ret .= ' ' . $key;
                    $ret .= '</span>';
                    $ret .= '<ul>';
                    foreach ($menu->items as $key => $menuItem) {
                        $ret .= '<li>';
                        $ret .= '<a href="'.$menuItem.'">'.$key.'</a>';
                        $ret .= '</li>';
                    }
                    $ret .= '</ul></li>';
                }
            }

            $ret .= '</ul>';
            $ret .= '</li></ul>';
        } else {
            $ret .= '<ul class="nav-right">
              <li style="width:130px;"><a style="text-transform: initial;  background: white; color: black; margin-bottom: 10px; border: 2px solid black; width: 120px; height: 40px;" href="' . $restartersBaseUrl . '/login">Sign in</a></li>
      <li style="width:130px;"><a style="text-transform: initial; background: black; color: white; margin-bottom: 10px; width: 120px; height: 40px;" href="' . $restartersBaseUrl . '/about">Join</a></li>
      </ul>';
        }

		return $ret;
	}
}
