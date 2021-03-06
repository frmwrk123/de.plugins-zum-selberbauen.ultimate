<?php
/**
 * Contains the ContentCommentResponseUserNotificiationEvent class.
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
 * along with the Ultimate CMS. If not, see {@link http://www.gnu.org/licenses/}}.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.user.notification.event
 * @category	Ultimate CMS
 */
namespace ultimate\system\user\notification\event;
use ultimate\system\cache\builder\ContentCacheBuilder;
use wcf\system\request\LinkHandler;
use wcf\system\user\notification\event\AbstractUserNotificationEvent;
use wcf\system\WCF;

/**
 * User notification event for content comment responses.
 *
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.user.notification.event
 * @category	Ultimate CMS
 */
class ContentCommentResponseUserNotificationEvent extends AbstractUserNotificationEvent {
	/**
	 * The determined content for this event.
	 * 
	 * @var \ultimate\data\content\Content
	 */
	private $content = null;
	
	/**
	 * Returns a short title used for the notification overlay.
	 * 
	 * @return	string
	 */
	public function getTitle() {
		return $this->getLanguage()->get('wcf.user.notification.content.commentResponse.title');
	}
	
	/**
	 * Returns the notification event message.
	 * 
	 * @return	string
	 */
	public function getMessage() {
		$content = $this->getContent();
		$user = $content->__get('author');
		
		return $this->getLanguage()->getDynamicVariable('wcf.user.notification.content.commentResponse.message', array(
			'author' => $this->author,
			'owner' => $user
		));
	}
	
	/**
	 * Returns the message for this notification event.
	 * 
	 * @param	string	$notificationType	(optional) 'instant' by default
	 * @return	string
	 */
	public function getEmailMessage($notificationType = 'instant') {
		$content = $this->getContent();
		$user = $content->__get('author');
		return $this->getLanguage()->getDynamicVariable('wcf.user.notification.content.commentResponse.mail', array(
			'response' => $this->userNotificationObject,
			'author' => $this->author,
			'owner' => $user,
			'notificationType' => $notificationType,
			'link' => $this->getLink()
		));
	}
	
	/**
	 * Returns object link.
	 * 
	 * @return	string
	 */
	public function getLink() {
		$content = $this->getContent();
		/* @var $date \DateTime */
		$date = $content->__get('publishDateObject');
		return LinkHandler::getInstance()->getLink(null, array(
			'date' => ''. $date->format('Y-m-d'),
			'contentslug' => $content->__get('contentSlug')
		));
	}
	
	/**
	 * Determines the content and returns it.
	 * 
	 * @return \ultimate\data\content\Content
	 */
	private function getContent() {
		if ($this->content === null) {
			$commentID = $this->userNotificationObject->__get('commentID');
			
			$sql = "SELECT objectID
			        FROM   wcf".WCF_N."_comment
			        WHERE  commentID = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array($commentID));
			$row = $statement->fetchArray();
			
			$contentID = $row['objectID'];
			
			$contents = ContentCacheBuilder::getInstance()->getData(array(), 'contents');
			/* @var $content \ultimate\data\content\Content */
			$this->content = $contents[$contentID];
		}
		return $this->content;
	}
}
