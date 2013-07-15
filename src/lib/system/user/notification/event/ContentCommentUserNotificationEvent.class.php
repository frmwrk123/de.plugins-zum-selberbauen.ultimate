<?php
/**
 * Contains the ContentCommentUserNotificationEvent class.
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
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.user.notification.event
 * @category	Ultimate CMS
 */
namespace ultimate\system\user\notification\event;
use ultimate\data\content\Content;

use ultimate\system\cache\builder\ContentCacheBuilder;
use ultimate\system\request\UltimateLinkHandler;
use wcf\system\request\LinkHandler;
use wcf\system\user\notification\event\AbstractUserNotificationEvent;
use wcf\system\user\notification\type\IUserNotificationType;
use wcf\system\WCF;

/**
 * User notification event for content comments.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.user.notification.event
 * @category	Ultimate CMS
 */
class ContentCommentUserNotificationEvent extends AbstractUserNotificationEvent {
	/**
	 * @see	\wcf\system\user\notification\event\IUserNotificationEvent::getMessage()
	 */
	public function getMessage() {
		return $this->getLanguage()->getDynamicVariable('wcf.user.notification.content.comment.message', array(
			'author' => $this->author
		));
	}
	
	/**
	 * @see	\wcf\system\user\notification\event\IUserNotificationEvent::getTitle()
	 */
	public function getTitle() {
		return $this->getLanguage()->get('wcf.user.notification.content.comment.title');
	}
	
	/**
	 * @see	wcf\system\user\notification\event\IUserNotificationEvent::getEmailMessage()
	 */
	public function getEmailMessage($notificationType = 'instant') {
		$content = $this->getContent();
		
		return $this->getLanguage()->getDynamicVariable('wcf.user.notification.content.comment.mail', array(
			'comment' => $this->userNotificationObject,
			'author' => $this->author,
			'owner' => $content->__get('author'),
			'notificationType' => $notificationType,
			'link' => $this->getLink()
		));
	}
	
	/**
	 * @see	\wcf\system\user\notification\event\IUserNotificationEvent::getLink()
	 */
	public function getLink() {
		$content = $this->getContent();
		
		/* @var $date \DateTime */
		$date = $content->__get('publishDateObject');
		return UltimateLinkHandler::getInstance()->getLink(null, array(
			'date' => ''. $date->format('Y-m-d'),
			'contentSlug' => $content->__get('contentSlug')
		));
	}
	
	/**
	 * Determines and returns the content for this event.
	 * 
	 * @return \ultimate\data\content\Content
	 */
	private function getContent() {
		$contentID = $this->userNotificationObject->__get('objectID');
		
		$contents = ContentCacheBuilder::getInstance()->getData(array(), 'contents');
		/* @var $content \ultimate\data\content\Content */
		$content = $contents[$contentID];
		
		return $content;
	}
}
