<?php
/**
 * Contains the IBlockType interface.
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
 * @subpackage	system.blocktype
 * @category	Ultimate CMS
 */
namespace ultimate\system\blocktype;

/**
 * Interface for all BlockType classes.
 * 
 * This interface provides the basic methods for BlockType classes. Instead
 * of implementing this interface directly, you should inherit from AbstractBlockType.
 * 
 * @author		Jim Martens
 * @copyright	2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.blocktype
 * @category	Ultimate CMS
 */
interface IBlockType {
	/**
	 * Initializes the blockType.
	 * 
	 * @api
	 * @since	1.0.0
	 * 
	 * @param	string								$requestType
	 * @param	\ultimate\data\layout\Layout		$layout
	 * @param	\ultimate\data\IUltimateData|null	$requestObject	null is only allowed in connection with the index layout
	 * @param	integer								$blockID
	 * @return	void
	 */
	public function init($requestType, \ultimate\data\layout\Layout $layout, $requestObject, $blockID);
	
	/**
	 * Reads the necessary data.
	 * 
	 * Use this method to load data from cache or, if not possible otherwise, from database.
	 * 
	 * @since	1.0.0
	 * @api
	 * 
	 * @return	void
	 */
	public function readData();
	
	/**
	 * Assigns template variables.
	 * 
	 * @since	1.0.0
	 * @api
	 * 
	 * @return	void
	 */
	public function assignVariables();
	
	/**
	 * Returns the HTML for this blockType.
	 * 
	 * @since	1.0.0
	 * @api
	 * 
	 * @return	string
	 */
	public function getHTML();
	
	/**
	 * Returns the options HTML for this blockType.
	 * 
	 * @since	1.0.0
	 * @api
	 * 
	 * @return	(string[]|string)[]	The given array contains another array at pos 0 with all the available ids for the options and the actual options HTML at pos 1.
	 */
	public function getOptionsHTML();
}
