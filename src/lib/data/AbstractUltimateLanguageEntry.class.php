<?php
/**
 * Contains AbstractUltimateLanguageEntry class.
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
 * @subpackage	data
 * @category	Ultimate CMS
 */
namespace ultimate\data;
use wcf\data\AbstractLanguageEntry;

/**
 * ${CARET}
 * 
 * @author    Jim Martens <github@2martens.de>
 * @copyright 2013-2014 Jim Martens
 */
abstract class AbstractUltimateLanguageEntry extends AbstractLanguageEntry {
	/**
	 * Returns the database table name.
	 *
	 * @return	string
	 */
	public static function getDatabaseTableName() {
		return 'ultimate'.WCF_N.'_'.static::$databaseTableName;
	}
}
