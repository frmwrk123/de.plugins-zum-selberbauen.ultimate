<?php
/**
 * The ContentAssignToCategory form.
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
 * @subpackage	form
 * @category	Ultimate CMS
 */
namespace ultimate\form;
use ultimate\data\content\CategorizedContent;
use ultimate\data\content\ContentAction;
use ultimate\page\IEditSuitePage;
use ultimate\system\cache\builder\CategoryCacheBuilder;
use wcf\form\AbstractForm;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\SystemException;
use wcf\system\exception\UserInputException;
use wcf\system\request\UltimateLinkHandler;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use wcf\util\HeaderUtil;

/**
 * Shows the ContentAssignToCategory form.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	form
 * @category	Ultimate CMS
 */
class ContentAssignToCategoryForm extends AbstractForm implements IEditSuitePage {
	/**
	 * name of the template for the called page
	 * @var	string
	 */
	public $templateName = 'editSuite';

	/**
	 * indicates if you need to be logged in to access this page
	 * @var	boolean
	 */
	public $loginRequired = true;

	/**
	 * enables template usage
	 * @var	string
	 */
	public $useTemplate = true;

	/**
	 * Array of needed permissions.
	 * @var string[]
	 */
	public $neededPermissions = array(
		'user.ultimate.editing.canAccessEditSuite',
		'user.ultimate.editing.canEditContent'
	);
	
	/**
	 * Contains content ids.
	 * @var integer[]
	 */
	public $contentIDs = array();
	
	/**
	 * Contains Content objects.
	 * @var \ultimate\data\content\CategorizedContent[]
	 */
	public $contents = array();
	
	/**
	 * Contains category ids.
	 * @var integer[]
	 */
	public $categoryIDs = array();
	
	/**
	 * Contains Category objects.
	 * @var \ultimate\data\category\Category[]
	 */
	public $categories = array();
	
	/**
	 * The clipboard item type id.
	 * @var integer|null
	 */
	protected $typeID = null;

	/**
	 * A list of active EditSuite menu items.
	 * @var string[]
	 */
	protected $activeMenuItems = array(
		'ContentAssignToCategoryForm',
		'ultimate.edit.contents'
	);

	/**
	 * @see \ultimate\page\IEditSuitePage::getActiveMenuItems()
	 */
	public function getActiveMenuItems() {
		return $this->activeMenuItems;
	}

	/**
	 * @see \ultimate\page\IEditSuitePage::getJavascript()
	 */
	public function getJavascript() {
		return WCF::getTPL()->fetch('__editSuiteJS.ContentAssignToCategoryForm', 'ultimate');
	}
	
	/**
	 * Reads parameters.
	 */
	public function readParameters() {
		parent::readParameters();
		// get type id
		$this->typeID = ClipboardHandler::getInstance()->getObjectTypeID('de.plugins-zum-selberbauen.ultimate.content');
		if ($this->typeID === null) {
			throw new SystemException("Clipboard item type 'de.plugins-zum-selberbauen.content' is unknown.");
		}
		
		// get content ids
		$contents = ClipboardHandler::getInstance()->getMarkedItems($this->typeID);
		if (empty($contents)) {
			throw new IllegalLinkException();
		}
		
		// load contents
		$this->contentIDs = array_keys($contents);
		$this->contents = $contents;
		foreach ($this->contents as $contentID => $content) {
			$this->contents[$contentID] = new CategorizedContent($content);
		}
	}
	
	/**
	 * Reads data.
	 */
	public function readData() {
		$this->loadCache();
		
		// determine already used categories
		$invalidCategoryIDs = array();
		foreach ($this->contents as $contentID => $content) {
			$categories = $content->__get('categories');
			foreach ($categories as $categoryID => $category) {
				$invalidCategoryIDs[] = $categoryID;
			}
		}
		// default category may never be in list of assignable categories
		$invalidCategoryIDs[] = 1;
		// delete those from the available categories
		$invalidCategoryIDs = array_unique($invalidCategoryIDs);
		foreach ($invalidCategoryIDs as $categoryID) {
			if (isset($this->categories[$categoryID])) {
				unset($this->categories[$categoryID]);
			}
		}
		parent::readData();
	}
	
	/**
	 * Reads form input.
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		if (isset($_POST['categoryIDs']) && is_array($_POST['categoryIDs'])) $this->categoryIDs = ArrayUtil::toIntegerArray($_POST['categoryIDs']);
	}
	
	/**
	 * Validates the form input.
	 */
	public function validate() {
		parent::validate();
		$this->validateContentIDs();
		$this->validateCategoryIDs();
	}
	
	/**
	 * Saves the form input.
	 */
	public function save() {
		parent::save();
		
		$parameters = array(
			'categories' => $this->categoryIDs,
			'removeCategories' => (!empty($this->categoryIDs) ? array(1) : array())
		);
		$this->objectAction = new ContentAction($this->contentIDs, 'update', $parameters);
		$this->objectAction->executeAction();
		
		ClipboardHandler::getInstance()->unmark($this->contentIDs, $this->typeID);
		
		$this->saved();

		$url = UltimateLinkHandler::getInstance()->getLink('ContentList',
			array(
				'application' => 'ultimate',
				'parent' => 'EditSuite'
			),
			'success=true'
		);
		HeaderUtil::redirect($url);
		// after initiating the redirect, no other code should be executed as the request for the original resource has ended
		exit;
	}
	
	/**
	 * Assigns template variables.
	 */
	public function assignVariables() {
		parent::assignVariables();
		WCF::getTPL()->assign(array(
			'categories' => $this->categories,
			'categoryIDs' => $this->categoryIDs,
			'contents' => $this->contents,
			'contentIDs' => $this->contentIDs
		));
	}
	
	/**
	 * Loads the cache.
	 */
	protected function loadCache() {
		$this->categories = CategoryCacheBuilder::getInstance()->getData(array(), 'categories');
	}
	
	/**
	 * Validates the content ids.
	 * 
	 * @throws	\wcf\system\exception\IllegalLinkException	if content ids are empty
	 */
	protected function validateContentIDs() {
		if (empty($this->contentIDs)) {
			throw new IllegalLinkException();
		}
	}
	
	/**
	 * Validates the category ids.
	 * 
	 * @throws UserInputException	if selected categories are invalid
	 */
	protected function validateCategoryIDs() {
		if (empty($this->categoryIDs)) {
			throw new UserInputException('categoryIDs');
		}
		foreach ($this->categoryIDs as $categoryID) {
			if (!isset($this->categories[$categoryID])) {
				throw new UserInputException('categoryIDs', 'notValid');
			}
		}
	}
}
