<?php
/**
 * Contains ContentLanguageEntry class.
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
 * @subpackage	data.content.language
 * @category	Ultimate CMS
 */
namespace ultimate\data\content\language;
use ultimate\data\AbstractUltimateLanguageEntry;

/**
 * Represents a content language entry.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content.language
 * @category	Ultimate CMS
 */
class ContentLanguageEntry extends AbstractUltimateLanguageEntry {
	/**
	 * The database table name.
	 * @var string
	 */
	protected static $databaseTableName = 'content_language';
	
	/**
	 * Name of the object id.
	 * @var string
	 */
	protected static $objectIDName = 'contentVersionID';
	
	/**
	 * Handles data.
	 * 
	 * @param	array	$data
	 */
	protected function handleData($data) {
		if (!isset($data['languageEntryID'])) {
			parent::handleData($data);
			return;
		}

		$data['languageEntryID'] = intval($data['languageEntryID']);
		$data['contentVersionID'] = intval($data['contentVersionID']);
		$data['languageID'] = intval($data['languageID']);
		parent::handleData($data);
	}
}
