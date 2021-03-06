<?php
/**
 * The UltimateWidgetAreaListPage class.
 * 
 * LICENSE:
 * This file is part of the Ultimate CMS.
 *
 * The Ultimate CMS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * The Ultimate CMS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with the Ultimate CMS.  If not, see {@link http://www.gnu.org/licenses/}}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.page
 * @category	Ultimate CMS
 */
namespace ultimate\acp\page;
use wcf\page\AbstractCachedListPage;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\menu\acp\ACPMenu;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Shows the UltimateWidgetAreaList page.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.page
 * @category	Ultimate CMS
 */
class UltimateWidgetAreaListPage extends AbstractCachedListPage {
	/**
	 * The template name.
	 * @var	string
	 */
	public $templateName = 'ultimateWidgetAreaList';
	
	/**
	 * The object list class name.
	 * @var	string
	 */
	public $objectListClassName = '\ultimate\data\widget\area\WidgetAreaList';
	
	/**
	 * Array of valid sort fields.
	 * @var	string[]
	 */
	public $validSortFields = array(
		'widgetAreaID',
		'widgetAreaName'
	);
	
	/**
	 * The default sort order.
	 * @var	string
	 */
	public $defaultSortOrder = ULTIMATE_SORT_WIDGETAREA_SORTORDER;
	
	/**
	 * The default sort field.
	 * @var	string
	 */
	public $defaultSortField = ULTIMATE_SORT_WIDGETAREA_SORTFIELD;
	
	/**
	 * The cache builder class name.
	 * @see	\wcf\page\AbstractCachedListPage::$cacheBuilderClassName
	 */
	public $cacheBuilderClassName = '\ultimate\system\cache\builder\WidgetAreaCacheBuilder';
	
	/**
	 * The cache index.
	 * @see	\wcf\page\AbstractCachedListPage::$cacheIndex
	 */
	public $cacheIndex = 'widgetAreas';
	
	/**
	 * The active menu item.
	 * @var string
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.appearance.widgetArea.list';
	
	/**
	 * The url.
	 * @var	string
	 */
	protected $url = '';
	
	/**
	 * Reads data.
	 */
	public function readData() {
		parent::readData();
		$this->url = LinkHandler::getInstance()->getLink('UltimateWidgetAreaList', array(), 'action='.rawurlencode($this->action).'&pageNo='.$this->pageNo.'&sortField='.$this->sortField.'&sortOrder='.$this->sortOrder);
	}
	
	/**
	 * Loads the cache.
	 * 
	 * @param	string	$path
	 * 
	 * @see	\wcf\page\AbstractCachedListPage::loadCache()
	 */
	public function loadCache($path = ULTIMATE_DIR) {
		parent::loadCache($path);
	}
	
	/**
	 * Assigns template variables.
	 */
	public function assignVariables() {
		parent::assignVariables();
	
		WCF::getTPL()->assign(array(
			'hasMarkedItems' => ClipboardHandler::getInstance()->hasMarkedItems(),
			'url' => $this->url
		));
	}
	
	/**
	 * Shows the page.
	 */
	public function show() {
		// set active menu item
		ACPMenu::getInstance()->setActiveMenuItem($this->activeMenuItem);
	
		parent::show();
	}
}
