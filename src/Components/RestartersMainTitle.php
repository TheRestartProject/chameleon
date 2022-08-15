<?php
/**
 * File holding the MainContent class
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

use Skins\Chameleon\IdRegistry;

/**
 * The MainContent class.
 *
 * FIXME: Extract into separate modules/allow as plugins: TOC, CategoryLinks, NewtalkNotifier, Indicators
 *
 * @author Stephan Gambke
 * @since 1.0
 * @ingroup Skins
 */
class RestartersMainTitle extends Component
{

    /**
     * Builds the HTML code for this component
     *
     * @return String the HTML code
     * @throws \MWException
     */
    public function getHtml()
    {
        $idRegistry = IdRegistry::getRegistry();

        $dom = $this->getDomElement();

        $attributes = [];

        foreach ($dom->attributes as $attribute_name => $attribute_value) {
            if (in_array($attribute_name, ['type', 'element'])) {
                continue;
            }

            $attributes[$attribute_name] = $dom->getAttribute($attribute_name);
        }
        $attributes['data-title'] = $this->getSkinTemplate()->get('title');

        $ret = $this->indent().\Html::rawElement('h1', $attributes, $this->getSkinTemplate()->get('title'));
        $this->indent(1);

        return $ret;
    }
}

