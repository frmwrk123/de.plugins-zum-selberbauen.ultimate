<?php
/**
 * Contains the CacheResetListener class.
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
 * @subpackage	system.event.listener
 * @category	Ultimate CMS
 */
namespace ultimate\system\event\listener;
use ultimate\system\cache\builder\AuthorCacheBuilder;
use wcf\system\event\IEventListener;

/**
 * Resets caches.
 * 
 * @author		Jim Martens
 * @copyright	2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.event.listener
 * @category	Ultimate CMS
 */
class CacheResetListener implements IEventListener {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.system.event.IEventListener.html#execute
	 */
	public function execute($eventObj, $className, $eventName) {
		switch ($className) {
			case 'wcf\acp\action\AJAXProxyAction':
				if ($eventObj->className != 'wcf\data\user\UserAction' || $eventName != 'delete') break;
			case 'wcf\acp\form\UserAddForm':
			case 'wcf\acp\form\UserEditForm':
				$this->resetAuthorCache();
				break;
		}
	}
	
	/**
	 * Resets the author cache.
	 */
	protected function resetAuthorCache() {
		AuthorCacheBuilder::getInstance()->reset();
	}
}