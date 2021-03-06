<?php
/**
 * The UltimateLinkEdit form.
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
use ultimate\data\link\CategorizedLink;
use ultimate\data\link\Link;
use ultimate\data\link\LinkAction;
use wcf\form\AbstractForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\language\I18nHandler;
use wcf\system\WCF;

/**
 * Shows the UltimateLinkEdit form.
 * 
 * @author		Jim Martens
 * @copyright	2011-2015 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
class UltimateLinkEditForm extends UltimateLinkAddForm {
	/**
	 * The active menu item.
	 * @var	string
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.link';
	
	/**
	 * The link id.
	 * @var	integer
	 */
	public $linkID = 0;
	
	/**
	 * The link object.
	 * @var \ultimate\data\link\CategorizedLink
	 */
	public $link = null;
	
	/**
	 * Reads parameters.
	 * @see	UltimateLinkAddForm::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['id'])) $this->linkID = intval($_REQUEST['id']);
		$link = new CategorizedLink(new Link($this->linkID));
		if (!$link->__get('linkID')) {
			throw new IllegalLinkException();
		}
		
		$this->link = $link;
	}
	
	/**
	 * Reads data.
	 * @see http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#readData
	 */
	public function readData() {
		$this->linkName = $this->link->__get('linkName');
		$this->linkDescription = $this->link->__get('linkDescription');
		$this->linkURL = $this->link->__get('linkURL');
		
		// fixes problem with wrong validation
		$categories = $this->link->__get('categories');
		require(ULTIMATE_DIR.'acp/config.inc.php');
		unset($categories[$categoryID]);
		$this->categoryIDs = array_keys($categories);
		
		I18nHandler::getInstance()->setOptions('linkName', PACKAGE_ID, $this->linkName, 'ultimate.link.\d+.linkName');
		I18nHandler::getInstance()->setOptions('linkDescription', PACKAGE_ID, $this->linkDescription, 'ultimate.link.\d+.linkDescription');
		parent::readData();
	}
	
	/**
	 * Saves the form input.
	 * @see http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.form.IForm.html#save
	 */
	public function save() {
		AbstractForm::save();
		
		$this->linkName = 'ultimate.link.'.$this->linkID.'.linkName';
		if (I18nHandler::getInstance()->isPlainValue('linkName')) {
			I18nHandler::getInstance()->remove($this->linkName, PACKAGE_ID);
			$this->linkName = I18nHandler::getInstance()->getValue('linkName');
		} else {
			I18nHandler::getInstance()->save('linkName', $this->linkName, 'ultimate.link', PACKAGE_ID);
		}
		
		$this->linkDescription = 'ultimate.link.'.$this->linkID.'.linkDescription';
		if (I18nHandler::getInstance()->isPlainValue('linkDescription')) {
			I18nHandler::getInstance()->remove($this->linkDescription, PACKAGE_ID);
			$this->linkDescription = I18nHandler::getInstance()->getValue('linkDescription');
		} else {
			I18nHandler::getInstance()->save('linkDescription', $this->linkDescription, 'ultimate.link', PACKAGE_ID);
		}
		
		$parameters = array(
			'data' => array(
				'linkName' => $this->linkName,
				'linkDescription' => $this->linkDescription,
				'linkURL' => $this->linkURL
			),
			'categories' => $this->categoryIDs
		);
		
		$this->objectAction = new LinkAction(array($this->linkID), 'update', $parameters);
		$this->objectAction->executeAction();
		
		$this->saved();
		
		WCF::getTPL()->assign(
			'success', true
		);
	}
	
	/**
	 * Assigns the template variables.
	 * @see http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.page.IPage.html#assignVariables
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		$useRequestData = (!empty($_POST)) ? true : false;
		I18nHandler::getInstance()->assignVariables($useRequestData);
		
		WCF::getTPL()->assign(array(
			'linkID' => $this->linkID,
			'action' => 'edit'
		));
	}
}
