<?php
/**
 * Contains the category data model class.
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
 * @subpackage	data.category
 * @category	Ultimate CMS
 */
namespace ultimate\data\category;
use ultimate\data\content\Content;
use ultimate\data\AbstractUltimateDatabaseObject;
use wcf\data\ITitledObject;
use wcf\system\WCF;

/**
 * Represents a category entry.
 * It offers the following properties (without '): 'categoryID', 'categoryParent', 'categoryTitle',
 * 'categoryDescription', 'categorySlug', 'childCategories', 'contents' and 'metaData'.
 * 
 * @author		Jim Martens
 * @copyright	2011-2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.category
 * @category	Ultimate CMS
 */
class Category extends AbstractUltimateDatabaseObject implements ITitledObject {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObject.html#$databaseTableName
	 */
	protected static $databaseTableName = 'category';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObject.html#$databaseTableIndexIsIdentity
	 */
	protected static $databaseTableIndexIsIdentity = true;
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObject.html#$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'categoryID';
	
	/**
	 * Contains the content to category database table name.
	 * @var	string
	 */
	protected $contentCategoryTable = 'content_to_category';
	
	/**
	 * Returns the title of this category (without language interpreting).
	 * 
	 * To use language interpreting, use magic toString method.
	 * 
	 * @return	string
	 */
	public function getTitle() {
		return $this->categoryTitle;
	}
	
	/**
	 * Returns the title of this category.
	 * 
	 * @return	string
	 */
	public function __toString() {
		return WCF::getLanguage()->get($this->categoryTitle);
	}
	
	/**
	 * Returns all child categories of this category.
	 *
	 * @return	\ultimate\data\category\Category[]
	 */
	protected function getChildCategories() {
		$sql = 'SELECT *
		        FROM   ultimate'.WCF_N.'_'.self::$databaseTableName.'
		        WHERE  categoryParent = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->categoryID));
		$categories = array();
		while ($category = $statement->fetchObject(get_class($this))) {
			$categories[$category->categoryID] = $category;
		}
		return $categories;
	}
	
	/**
	 * Returns all contents in this category.
	 *
	 * @return	\ultimate\data\content\Content[]
	 */
	protected function getContents() {
		$sql = 'SELECT    content.*
		        FROM      ultimate'.WCF_N.'_'.$this->contentCategoryTable.' contentToCategory
		        LEFT JOIN ultimate'.WCF_N.'_content content
		        ON        (content.contentID = contentToCategory.contentID)
		        WHERE     contentToCategory.categoryID = ?
		        ORDER BY '.ULTIMATE_SORT_CONTENT_SORTFIELD.' '.ULTIMATE_SORT_CONTENT_SORTORDER;
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->categoryID));
		$contents = array();
		while ($content = $statement->fetchObject('\ultimate\data\content\Content')) {
			$contents[$content->__get('contentID')] = $content;
		}
		return $contents;
	}

	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.DatabaseObject.html#handleData
	 */
	protected function handleData($data) {
		$data['categoryID'] = intval($data['categoryID']);
		$data['categoryParent'] = intval($data['categoryParent']);
		parent::handleData($data);
		$this->data['childCategories'] = $this->getChildCategories();
		$this->data['contents'] = $this->getContents();
		$this->data['metaData'] = $this->getMetaData($this->categoryID, 'category');
	}
}
