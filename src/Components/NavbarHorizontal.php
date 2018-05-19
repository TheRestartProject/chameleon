<?php
/**
 * File holding the NavbarHorizontal class
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

use DOMElement;
use Skins\Chameleon\IdRegistry;

/**
 * The NavbarHorizontal class.
 *
 * A horizontal navbar containing the sidebar items.
 * Does not include standard items (toolbox, search, language links). They need
 * to be added to the page elsewhere
 *
 * The navbar is a list of lists wrapped in a nav element: <nav
 * role="navigation" id="p-navbar" >
 *
 * @author  Stephan Gambke
 * @since   1.0
 * @ingroup Skins
 */
class NavbarHorizontal extends Component {

	private $mHtml = null;
	private $htmlId = null;

	/**
	 * Builds the HTML code for this component
	 *
	 * @return String the HTML code
	 * @throws \MWException
	 */
	public function getHtml() {

		if ( $this->mHtml === null ) {
			$this->buildHtml();
		}

		return $this->mHtml;
	}

	/**
	 * @throws \MWException
	 */
	protected function buildHtml() {

		if ( $this->getDomElement() === null ) {
			$this->mHtml = '';
			return;
		}

		$this->mHtml =
			//$this->buildFixedNavBarIfRequested() . // FIXME: Put fixed navbar back in
			$this->buildNavBarOpeningTags() .
			$this->buildNavBarComponents() .
			$this->buildNavBarClosingTags();
	}

	/**
	 * @return string
	 * @throws \MWException
	 */
	protected function buildFixedNavBarIfRequested() {
		// if a fixed navbar is requested
		if ( filter_var( $this->getDomElement()->getAttribute( 'fixed' ), FILTER_VALIDATE_BOOLEAN ) === true ||
			$this->getDomElement()->getAttribute( 'position' ) === 'fixed'
		) {

			// first build the actual navbar and set a class so it will be fixed
			$this->getDomElement()->setAttribute( 'fixed', '0' );
			$this->getDomElement()->setAttribute( 'position', '' );

			$realNav = new self( $this->getSkinTemplate(), $this->getDomElement(), $this->getIndent() );
			$realNav->setClasses( $this->getClassString() . ' navbar-fixed-top' );

			// then add an invisible copy of the nav bar that will act as a spacer
			$this->addClasses( 'navbar-static-top invisible' );

			return $realNav->getHtml();
		} else {
			return '';
		}
	}

	/**
	 * @return string
	 * @throws \MWException
	 */
	protected function buildNavBarOpeningTags() {
		$openingTags =
			$this->indent() . '<!-- navigation bar -->' .
			$this->indent() . \Html::openElement( 'nav', [
					'class' => 'p-navbar' . $this->getClassString(),
					'role'  => 'navigation',
					'id'    => $this->getHtmlId() // FIXME: ID to be repeated in classes
				]
			);

		$this->indent( 1 );

		return $openingTags;
	}

	/**
	 * @return string
	 */
	private function getHtmlId() {
		if ( $this->htmlId === null ) {
			$this->htmlId = IdRegistry::getRegistry()->getId( 'mw-navigation' );
		}
		return $this->htmlId;
	}

	/**
	 * @return string
	 * @throws \MWException
	 */
	protected function buildNavBarComponents() {

		$elements = $this->buildNavBarElementsFromDomTree();

		if ( !empty( $elements[ 'right' ] ) ) {

			$elements[ 'left' ][ ] =
				$this->indent( 1 ) . '<div class="navbar-right-aligned">' .
				implode( $elements[ 'right' ] ) .
				$this->indent() . '</div> <!-- navbar-right-aligned -->';

			$this->indent( -1 );
		}

		return
			$this->buildHead( $elements[ 'head' ] ) .
			$this->buildTail( $elements[ 'left' ] );
	}

	/**
	 * @return string[][]
	 * @throws \MWException
	 */
	protected function buildNavBarElementsFromDomTree() {

		$elements = [
			'head'  => [],
			'left'  => [],
			'right' => [],
		];

		/** @var \DOMElement[] $children */
		$children = $this->getDomElement()->hasChildNodes() ? $this->getDomElement()->childNodes : [];

		// add components
		foreach ( $children as $node ) {
			$this->buildAndCollectNavBarElementFromDomElement( $node, $elements );
		}
		return $elements;
	}

	/**
	 * @param DOMElement $node
	 * @param $elements
	 *
	 * @throws \MWException
	 */
	protected function buildAndCollectNavBarElementFromDomElement( $node, &$elements ) {

		if ( $node instanceof DOMElement && $node->tagName === 'component' && $node->hasAttribute( 'type' ) ) {

			$position = $node->getAttribute( 'position' );

			if ( !array_key_exists( $position, $elements ) ) {
				$position = 'left';
			}

			$indentation = ( $position === 'right' ) ? 2 : 1;

			$this->indent( $indentation );
			$html = $this->buildNavBarElementFromDomElement( $node );
			$this->indent( -$indentation );

			$elements[ $position ][ ] = $html;

		// } else {
			// TODO: Warning? Error?
		}
	}

	/**
	 * @param \DomElement $node
	 *
	 * @return string
	 * @throws \MWException
	 */
	protected function buildNavBarElementFromDomElement( $node ) {
		return $this->getSkin()->getComponentFactory()->getComponent( $node, $this->getIndent() )->getHtml();
	}

	/**
	 * @param string[] $headElements
	 *
	 * @return string
	 * @throws \MWException
	 */
	protected function buildHead( $headElements ) {

		$head =
			$this->indent() . "<button type=\"button\" class=\"toggler\" data-toggle=\"collapse\" data-target=\"#" . $this->getHtmlId() . "-collapse\">" .
			$this->indent( 1 ) . "<span class=\"cmln-navbar-toggler-icon\"></span>" .
			$this->indent( -1 ) . "</button>\n" .
			implode( '', $headElements ) . "\n";

		return $head;
	}

	/**
	 * @param string[] $tailElements
	 *
	 * @return string
	 * @throws \MWException
	 */
	protected function buildTail( $tailElements ) {

		return
			$this->indent() . '<div class="collapse navbar-collapse" id="' . $this->getHtmlId() . '-collapse">' . // FIXME: ID to be repeated in classes
			implode( '', $tailElements ) .
			$this->indent() . '</div><!-- /.navbar-collapse -->';
	}

	/**
	 * @return string
	 * @throws \MWException
	 */
	protected function buildNavBarClosingTags() {
		return
			$this->indent( -1 ) . '</nav>' . "\n";
	}

}
