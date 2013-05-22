<?php
/**
 * Contains the template data model action class.
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
 * @subpackage	data.template
 * @category	Ultimate CMS
 */
namespace ultimate\data\template;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\WCF;

/**
 * Executes template-related actions.
 * 
 * @author		Jim Martens
 * @copyright	2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	data.ultimate.template
 * @category	Ultimate CMS
 */
class TemplateAction extends AbstractDatabaseObjectAction {
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.AbstractDatabaseObjectAction.html#$className
	 */
	public $className = '\ultimate\data\template\TemplateEditor';
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.AbstractDatabaseObjectAction.html#$permissionsCreate
	 */
	protected $permissionsCreate = array('admin.content.ultimate.canManageTemplates');
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.AbstractDatabaseObjectAction.html#$permissionsDelete
	 */
	protected $permissionsDelete = array('admin.content.ultimate.canManageTemplates');
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.AbstractDatabaseObjectAction.html#$permissionsUpdate
	 */
	protected $permissionsUpdate = array('admin.content.ultimate.canManageTemplates');
	
	/**
	 * Creates a new template and returns a JS-friendly array.
	 * 
	 * @since	1.0.0
	 * @internal	Calls create.
	 * 
	 * @return (int|string)[]
	 */
	public function createAJAX() {
		/* @var $template \ultimate\data\template\Template */
		$template = $this->create();
		$returnArray = array(
			'templateID' => $template->__get('templateID'),
			'templateName' => $template->getTitle()
		);
		return $returnArray;
	}
	
	/**
	 * Validates the 'createAJAX' action.
	 * 
	 * @since	1.0.0
	 * @internal	Calls validateCreate.
	 */
	public function validateCreateAJAX() {
		$this->validateCreate();
	}
	
	/**
	 * @link	http://doc.codingcorner.info/WoltLab-WCFSetup/classes/wcf.data.AbstractDatabaseObjectAction.html#update
	 */
	public function update() {
		parent::update();
		
		// delete existing entries
		$sql = 'DELETE FROM ultimate'.WCF_N.'_menu_to_template
		        WHERE       templateID = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		WCF::getDB()->beginTransaction();
		foreach ($this->objectIDs as $objectID) {
			$statement->executeUnbuffered(array($objectID));
		}
		WCF::getDB()->commitTransaction();
		
		$sql = 'DELETE FROM ultimate'.WCF_N.'_widget_area_to_template
		        WHERE       templateID = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		WCF::getDB()->beginTransaction();
		foreach ($this->objectIDs as $objectID) {
			$statement->executeUnbuffered(array($objectID));
		}
		WCF::getDB()->commitTransaction();
		
		// insert new entries
		$sql = 'INSERT INTO ultimate'.WCF_N.'_menu_to_template
		               (menuID, templateID)
		        VALUES (?, ?)';
		$statement = WCF::getDB()->prepareStatement($sql);
		WCF::getDB()->beginTransaction();
		foreach ($this->objectIDs as $objectID) {
			if (!$this->parameters['menuID']) continue;
			$statement->executeUnbuffered(array(
				$this->parameters['menuID'], 
				$objectID
			));
		}
		WCF::getDB()->commitTransaction();
		
		$sql = 'INSERT INTO ultimate'.WCF_N.'_widget_area_to_template
		               (templateID, widgetAreaID)
		        VALUES (?, ?)';
		$statement = WCF::getDB()->prepareStatement($sql);
		WCF::getDB()->beginTransaction();
		foreach ($this->objectIDs as $objectID) {
			if (!$this->parameters['widgetAreaID']) continue;
			$statement->executeUnbuffered(array(
				$objectID,
				$this->parameters['widgetAreaID']
			));
		}
		WCF::getDB()->commitTransaction();
	}
}
