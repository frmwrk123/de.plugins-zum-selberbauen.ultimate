<?php
/**
 * Contains the LikeableContentProvider class.
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
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
namespace ultimate\data\content;
use wcf\data\like\object\ILikeObject;
use wcf\data\like\ILikeObjectTypeProvider;
use wcf\data\object\type\AbstractObjectTypeProvider;

/**
 * Object type provider for likeable contents.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.content
 * @category	Ultimate CMS
 */
class LikeableContentProvider extends AbstractObjectTypeProvider implements ILikeObjectTypeProvider {
	/**
	 * The class name.
	 * @var	string
	 */
	public $className = 'ultimate\data\content\Content';
	
	/**
	 * The class name of the decorator.
	 * @var string
	 */
	public $decoratorClassName = 'ultimate\data\content\LikeableContent';
	
	/**
	 * The class name of the list.
	 * @var string
	 */
	public $listClassName = 'ultimate\data\content\ContentList';
	
	/**
	 * Checks the permissions.
	 * 
	 * @param	ILikeObject	$content
	 * 
	 * @return	boolean	true if the user can access the given content
	 */
	public function checkPermissions(ILikeObject $content) {
		return $content->isVisible();
	}
}
