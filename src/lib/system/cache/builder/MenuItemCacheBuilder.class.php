<?php
/**
 * Contains the MenuItemCacheBuilder class.
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
 * along with the Ultimate CMS.  If not, see {@link http://www.gnu.org/licenses/}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
namespace ultimate\system\cache\builder;
use ultimate\data\menu\item\MenuItemList;
use wcf\system\cache\builder\AbstractCacheBuilder;

/**
 * Caches the menu items.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class MenuItemCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.cache.builder.AbstractCacheBuilder.html#rebuild
	 */
	protected function rebuild(array $parameters) {
		$data = array(
			'menuItems' => array(),
			'menuItemIDs' => array(),
			'menuItemsToParent' => array()
		);
		
		$menuItemList = new MenuItemList();
		$menuItemList->readObjects();
		$menuItems = $menuItemList->getObjects();
		
		foreach ($menuItems as $menuItemID => $menuItem) {
			/* @var $menuItem \ultimate\data\menu\item\MenuItem */
			$data['menuItems'][$menuItemID] = $menuItem;
			$data['menuItemIDs'][] = $menuItemID;
			$data['menuItemsToParent'][$menuItem->__get('menuItemName')] = $menuItem->__get('childItems');
		}
		
		$data['menuItemsToParent'][''] = array();
		foreach ($data['menuItems'] as $menuItemID => $menuItem) {
			if ($menuItem->__get('menuItemParent') != '') continue;
			$data['menuItemsToParent'][''][$menuItemID] = $menuItem;
		}
		
		return $data;
	}
}
