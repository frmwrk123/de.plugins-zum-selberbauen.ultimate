<?php
/**
 * The UltimateLinkAdd form.
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
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
namespace ultimate\acp\form;
use ultimate\data\link\LinkAction;
use ultimate\data\link\LinkEditor;
use ultimate\util\LinkUtil;
use wcf\form\AbstractForm;
use wcf\system\category\CategoryHandler;
use wcf\system\exception\UserInputException;
use wcf\system\language\I18nHandler;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use wcf\util\StringUtil;

/**
 * Shows the UltimateLinkAdd form.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
class UltimateLinkAddForm extends AbstractForm {
	/**
	 * The active menu item.
	 * @var	string
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.link.add';
	
	/**
	 * The template name.
	 * @var string
	 */
	public $templateName = 'ultimateLinkAdd';
	
	/**
	 * Array of needed permissions.
	 * @var	string[]
	 */
	public $neededPermissions = array(
		'admin.content.ultimate.canManageLinks'
	);
	
	/**
	 * The link name.
	 * @var string
	 */
	public $linkName = '';
	
	/**
	 * The link URL.
	 * @var string
	 */
	public $linkURL = '';
	
	/**
	 * The link description.
	 * @var string
	 */
	public $linkDescription = '';
	
	/**
	 * The chosen categories.
	 * @var	integer[]
	 */
	public $categoryIDs = array();
	
	/**
	 * All categories.
	 * @var	\wcf\data\category\Category[]|string[]
	 */
	public $categories = array();
	
	/**
	 * Reads parameters.
	 */
	public function readParameters() {
		parent::readParameters();
		I18nHandler::getInstance()->register('linkName');
		I18nHandler::getInstance()->register('linkDescription');
	}
	
	/**
	 * Reads data.
	 */
	public function readData() {
		$this->categories = CategoryHandler::getInstance()->getCategories('de.plugins-zum-selberbauen.ultimate.linkCategory');
		// get category id
		require(ULTIMATE_DIR.'acp/config.inc.php');		
		unset($this->categories[$categoryID]);
		
		// workaround for html checkboxes
		$categories = array();
		foreach ($this->categories as $categoryID => $category) {
			/* @var $category \wcf\data\category\Category */
			$categories[$categoryID] = $category->getTitle();
		}
		$this->categories = $categories;
		parent::readData();
	}
	
	/**
	 * Reads form input.
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		I18nHandler::getInstance()->readValues();
		if (I18nHandler::getInstance()->isPlainValue('linkName')) $this->linkName = StringUtil::trim($_POST['linkName']);
		if (I18nHandler::getInstance()->isPlainValue('linkDescription')) $this->linkDescription = StringUtil::trim($_POST['linkDescription']);
		if (isset($_POST['linkURL'])) $this->linkURL = StringUtil::trim($_POST['linkURL']);
		if (isset($_POST['categoryIDs']) && is_array($_POST['categoryIDs'])) $this->categoryIDs = ArrayUtil::toIntegerArray(($_POST['categoryIDs']));
	}
	
	/**
	 * Validates the form input.
	 */
	public function validate() {
		parent::validate();
		$this->validateLinkName();
		$this->validateLinkURL();
		$this->validateLinkDescription();
		$this->validateCategories();
	}
	
	/**
	 * Saves the form input.
	 */
	public function save() {
		parent::save();
		
		$parameters = array(
			'data' => array(
				'linkName' => $this->linkName,
				'linkURL' => $this->linkURL,
				'linkDescription' => $this->linkDescription
			),
			'categories' => $this->categoryIDs
		);
		
		$this->objectAction = new LinkAction(array(), 'create', $parameters);
		$this->objectAction->executeAction();
		
		// get new created link
		$returnValues = $this->objectAction->getReturnValues();
		$linkID = $returnValues['returnValues']->__get('linkID');
		$updateEntries = array();
		if (!I18nHandler::getInstance()->isPlainValue('linkName')) {
			I18nHandler::getInstance()->save('linkName', 'ultimate.link.'.$linkID.'.linkName', 'ultimate.link', PACKAGE_ID);
			$updateEntries['linkName'] = 'ultimate.link.'.$linkID.'.linkName';
		}
		if (!I18nHandler::getInstance()->isPlainValue('linkDescription')) {
			I18nHandler::getInstance()->save('linkDescription', 'ultimate.link.'.$linkID.'.linkDescription', 'ultimate.link', PACKAGE_ID);
			$updateEntries['linkDescription'] = 'ultimate.link.'.$linkID.'.linkDescription';
		}
		if (!empty($updateEntries)) {
			$linkEditor = new LinkEditor($returnValues['returnValues']);
			$linkEditor->update($updateEntries);
		}
		$this->saved();
		
		WCF::getTPL()->assign(
			'success', true
		);
		
		// show empty form
		$this->linkName = $this->linkURL = $this->linkDescription = '';
		I18nHandler::getInstance()->reset();
	}
	
	/**
	 * Assigns template variables.
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		I18nHandler::getInstance()->assignVariables();
		WCF::getTPL()->assign(array(
			'linkURL' => $this->linkURL,
			'categories' => $this->categories,
			'categoryIDs' => $this->categoryIDs,
			'action' => 'add'
		));
	}
	
	/**
	 * Validates link name.
	 * 
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateLinkName() {
		if (!I18nHandler::getInstance()->isPlainValue('linkName')) {
			if (!I18nHandler::getInstance()->validateValue('linkName')) {
				throw new UserInputException('linkName');
			}
		}
		else {
			if (empty($this->linkName)) {
				throw new UserInputException('linkName');
			}
		}
	}
	
	/**
	 * Validates link url.
	 * 
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateLinkURL() {
		if (empty($this->linkURL)) {
			throw new UserInputException('linkURL');
		}
		// add http scheme if no scheme exists
		$parsedURL = parse_url($this->linkURL);
		if (!isset($parsedURL['scheme'])) $this->linkURL = 'http://'.$this->linkURL;
		if (!LinkUtil::isValidURL($this->linkURL)) {
			throw new UserInputException('linkURL', 'notValid');
		}
		
		if (!LinkUtil::isAvailableURL($this->linkURL, (isset($this->linkID) ? $this->linkID : 0))) {
			throw new UserInputException('linkURL', 'notUnique');
		}
	}
	
	/**
	 * Validates link description.
	 *
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateLinkDescription() {
		if (!I18nHandler::getInstance()->isPlainValue('linkDescription')) {
			if (!I18nHandler::getInstance()->validateValue('linkDescription')) {
				throw new UserInputException('linkDescription');
			}
		}
		else {
			if (empty($this->linkDescription)) {
				throw new UserInputException('linkDescription');
			}
		}
	}
	
	/**
	 * Validates the link categories.
	 * 
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateCategories() {
		foreach ($this->categoryIDs as $categoryID) {
			if (!in_array($categoryID, array_keys($this->categories))) {
				throw new UserInputException('category', 'notValid');
			}
		}
		if (empty($this->categoryIDs)) {
			// if no categories chosen, put link into uncategorized category
			require(ULTIMATE_DIR.'acp/config.inc.php');
			$this->categoryIDs[] = $categoryID;
		}
	}
}
