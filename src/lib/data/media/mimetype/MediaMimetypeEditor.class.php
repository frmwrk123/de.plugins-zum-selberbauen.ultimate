<?php
/**
 * Contains the media mimetype data model editor class.
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
 * @subpackage	data.media.mimetype
 * @category	Ultimate CMS
 */
namespace ultimate\data\media\mimetype;
use ultimate\system\cache\builder\MediaMimetypeCacheBuilder;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;

/**
 * Provides functions to edit MIME types.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.media.mimetype
 * @category	Ultimate CMS
 */
class MediaMimetypeEditor extends DatabaseObjectEditor implements IEditableCachedObject {
	/**
	 * The base class.
	 * @var	string
	 */
	protected static $baseClass = '\ultimate\data\media\mimetype\Mimetype';
	
	/**
	 * Resets the cache.
	 */
	public static function resetCache() {
		MediaMimetypeCacheBuilder::getInstance()->reset();
	}
}
