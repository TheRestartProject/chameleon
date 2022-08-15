<?php
/**
 * File holding the Container class
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
 * The Container class.
 *
 * It will wrap its content elements in a DIV.
 *
 * Supported attributes:
 * - class
 *
 * @author Stephan Gambke
 * @since 1.0
 * @ingroup Skins
 */
class RestartersGlobalNav extends Structure {

	/**
	 * Builds the HTML code for the main container
	 *
	 * @return String the HTML code
	 * @throws \MWException
	 */
	public function getHtml(){

		$ret = $this->indent() . \Html::openElement( 'nav', [ 'class' => $this->getClassString() ] );
		$this->indent( 1 );

		$html = parent::getHtml();

		// Slightly hackily, get translated values for the menu options.
        $tpl = $this->getSkinTemplate();
        $html = str_ireplace(">TALK<", ">" . $tpl->getMsg('navbar-talk') . "<", $html);
        $html = str_ireplace(">EVENTS<", ">" . $tpl->getMsg('navbar-events') . "<", $html);
        $html = str_ireplace(">GROUPS<", ">" . $tpl->getMsg('navbar-groups') . "<", $html);
        $html = str_ireplace(">FIXOMETER<", ">" . $tpl->getMsg('navbar-fixometer') . "<", $html);
        $html = str_ireplace(">WORKBENCH<", ">" . $tpl->getMsg('navbar-workbench') . "<", $html);

		$ret .= $html;

		$ret .= $this->indent( -1 ) . '</nav>';

		return $ret;
	}

}
