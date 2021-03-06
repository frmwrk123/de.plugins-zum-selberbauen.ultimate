<?php
/**
 * Contains the PublishContentPageCronjob class.
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
 * @subpackage	system.cronjob
 * @category	Ultimate CMS
 */
namespace ultimate\system\cronjob;
use ultimate\data\content\ContentAction;
use ultimate\data\page\PageAction;
use ultimate\system\cache\builder\ContentCacheBuilder;
use ultimate\system\cache\builder\ContentVersionCacheBuilder;
use ultimate\system\cache\builder\PageCacheBuilder;
use wcf\data\cronjob\Cronjob;
use wcf\system\cronjob\AbstractCronjob;

/**
 * Publishes contents and pages.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cronjob
 * @category	Ultimate CMS
 */
class PublishContentPageCronjob extends AbstractCronjob {
	
	/**
	 * Prepares a list of events for output.
	 * 
	 * @param	Cronjob	$cronjob
	 */
	public function execute(Cronjob $cronjob) {
		// reading cache
		$contents = ContentCacheBuilder::getInstance()->getData(array(), 'contents');
		$contentVersionsToContentID = ContentVersionCacheBuilder::getInstance()->getData(array(), 'versionsToObjectID');
		
		$pages = PageCacheBuilder::getInstance()->getData(array(), 'pages');
		
		// checking publish dates
		foreach ($contents as $contentID => $content) {
			foreach ($contentVersionsToContentID[$contentID] as $versionID => $version) {
				if (!(0 < $version->publishDate && $version->publishDate < TIME_NOW && $version->status == 2)) continue;
				/* @var $content \ultimate\data\content\TaggableContent */
				$parameters = array(
					'data' => array(
						'status' => 3
					),
					'versionID' => $versionID
				);
				$action = new ContentAction(array($contentID), 'updateVersion', $parameters);
				$action->executeAction();
			}
		}
		
		// pages
		$updateObjects = array();
		foreach ($pages as $pageID => $page) {
			if (!(0 < $page->publishDate && $page->publishDate <= TIME_NOW && $page->status == 2)) continue;
			$updateObjects[] = $page;
		}
		$parameters = array(
			'data' => array(
				'status' => 3
			)
		);
		$action = new PageAction($updateObjects, 'update', $parameters);
		$action->executeAction();
	}
}
