<?php
/**
 * Custom component for the tools the Restart Project requires on a page.  Not part of the original Chameleon theme.
 */

namespace Skins\Chameleon\Components;

use Action;
use MediaWiki\MediaWikiServices;
use MWNamespace;
use Skins\Chameleon\ChameleonTemplate;
use Skins\Chameleon\IdRegistry;

class RestartersPageTools extends Component
{

    /**
     * RestartersPageTools constructor.
     *
     * @param ChameleonTemplate $template
     * @param \DOMElement|null $domElement
     * @param int $indent
     *
     * @throws \MWException
     */
    public function __construct(ChameleonTemplate $template, \DOMElement $domElement = null, $indent = 0)
    {
        parent::__construct($template, $domElement, $indent);
        $this->addClasses('pagetools');
    }

    /**
     * Builds the HTML code for this component
     *
     * @return string the HTML code
     */
    public function getHtml()
    {
        $tabs = $this->getTabs();

        if ($tabs === []) {
            return '';
        }

        // Turn the tabs into a ul.
        return
            IdRegistry::getRegistry()->element(
                'ul',
                ['class' => 'nav nav-tabs nav-tabs-block', 'id' => 'p-contentnavigation'],
                join($tabs)
            );
    }

    /**
     * @return string[]
     */
    protected function getTabs()
    {
        // Get the list of actions on this page.
        $contentNavigation = $this->getPageToolsStructure();

        // The actions come in different categories, but for our purposes we don't care because we are restructuring
        // them, so just iterate across the categories and collect the tabs and dropdown actions we need.
        $tabs = [];
        $actions = [];

        foreach ($contentNavigation as $category => $actionList) {
            list ($newTabs, $newActions) = $this->getTabsAndActions($category, $actionList);
            $tabs = array_merge($tabs, $newTabs);
            $actions = array_merge($actions, $newActions);
        }

        // Construct the last tab, which is a dropdown list of the actions, with this structure:
        // <li>
        //   <a>Actions</a>
        //   <div>
        //      <a>...action...</a>
        //   </div>
        $tabs[] = IdRegistry::getRegistry()->element(
            'li',
            ['class' => 'nav-item bg-white dropdown'],

            join(' ', [
                IdRegistry::getRegistry()->element(
                    'a',
                    [
                        'class' => "nav-link bg-white",
                        'href' => '#',
                        'role' => "button",
                        'aria-haspopup' => "true",
                        'aria-expanded' => "false",
                        'data-toggle' => "dropdown"
                    ],
                    'Other'
                ),
                IdRegistry::getRegistry()->element(
                    'div',
                    [
                        'class' => "dropdown-menu dropdown-menu-right p-contentnavigation",
                        'id' => "p-contentnavigation",
                        'x-placement' => "bottom-end",
                        'style' => "position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(98px, 56px, 0px);"
                    ],
                    join(' ', $actions)
                )
            ])
        );

        return $tabs;
    }

    /**
     * @return mixed
     */
    public function getPageToolsStructure()
    {
        return $this->getSkinTemplate()->get('content_navigation', null);
    }

    /**
     * @param $category String
     * @param $actionList Array
     *
     * @return array
     */
    protected function getTabsAndActions($category, $actionList)
    {
        // We want to return some actions as separate tabs, and some in a dropdown.
        $separateTabs = [];
        $dropdownActions = [];

        foreach ($actionList as $key => $action) {
            switch (strtolower($action['text'])) {
                case 'main page':
                case 'page':
                    $separateTabs[] = $this->getSeparateTab('View', $action, $key);
                    break;
                case 'discussion':
                    $separateTabs[] = $this->getSeparateTab('Discuss', $action, $key);
                    break;
                case 'edit' :
                    // We always want the edit tab to edit the main page, not the Discussion.
                    $tabaction = $action;
                    $tabaction['href'] = str_replace('title=Talk:', 'title=', $action['href']);
                    $separateTabs[] = $this->getSeparateTab('Edit', $tabaction, $key);

                    if (strpos($_SERVER['REQUEST_URI'], '/Talk:') !== FALSE) {
                        // We're on the discussion page - add an edit of the discussion page as an action.
                        $dropdownActions[] = $this->getDropdownAction('Edit Discussion', $action, $key);
                    }
                    break;
                case 'view':
                case '+':
                    // Duplicates we ignore.
                    break;
                default:
                {
                    // Others go into the actions dropdown.
                    $dropdownActions[] = $this->getDropdownAction($action['text'], $action, $key);
                    break;
                }
            }
        }

        return [$separateTabs, $dropdownActions];
    }

    /**
     * @param string $tabName
     * @param mixed[] $tabDescription
     * @param string $key
     *
     * @return string
     */
    protected function getSeparateTab($tabName, $tabDescription, $key)
    {
        // Set the tab active if we're on that url.
        $activeClass = $_SERVER['REQUEST_URI'] == $tabDescription['href'] ? 'active' : '';

        // Get an li for this action.
        return IdRegistry::getRegistry()->element(
            'li',
            ['class' => 'nav-item'],

            IdRegistry::getRegistry()->element(
                'a',
                ['class' => "nav-link bg-white $activeClass", 'href' => $tabDescription['href']],
                $tabName
            )
        );
    }


    /**
     * @param string $actionName
     * @param mixed[] $actionDescription
     * @param string $key
     *
     * @return string
     */
    protected function getDropdownAction($actionName, $actionDescription, $key)
    {
        // Get an a for this action.
        return IdRegistry::getRegistry()->element(
            'a',
            ['class' => "dropdown-item", 'href' => $actionDescription['href']],
            $actionName
        );
    }
}
