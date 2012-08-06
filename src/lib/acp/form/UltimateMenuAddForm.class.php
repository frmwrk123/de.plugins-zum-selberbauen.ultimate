<?php
namespace ultimate\acp\form;
use wcf\util\HeaderUtil;

use wcf\system\request\LinkHandler;

use ultimate\data\menu\item\MenuItemNodeList;
use ultimate\data\menu\MenuAction;
use ultimate\util\MenuUtil;
use wcf\acp\form\ACPForm;
use wcf\system\cache\CacheHandler;
use wcf\system\exception\UserInputException;
use wcf\system\language\I18nHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the UltimateMenuAdd form.
 * 
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage acp.form
 * @category Ultimate CMS
 */
class UltimateMenuAddForm extends ACPForm {
    /**
     * @var string
     * @see \wcf\acp\form\ACPForm::$activeMenuItem
     */
    public $activeMenuItem = 'wcf.acp.menu.link.ultimate.appearance.menu.add';
    
    /**
     * @var string
     * @see \wcf\page\AbstractPage::$templateName
     */
    public $templateName = 'ultimateMenuAdd';
    
    /**
     * @var string[]
     * @see \wcf\page\AbstractPage::$neededPermissions
     */
    public $neededPermissions = array(
        'admin.content.ultimate.canAddMenu'
    );
    
    /**
     * Contains the title of the category.
     * @var string
    */
    public $menuName = '';
    
    /**
     * Contains the MenuItemNodeList.
     * @var \ultimate\data\menu\item\MenuItemNodeList
     */
    public $menuItemNodeList = null;
    
    /**
     * Contains all categories.
     * @var (\ultimate\data\category\Category|array)[]
     */
    public $categories = array();
    
    
    /**
     * Contains all pages.
     * @var (\ultimate\data\page\Page|array)[]
     */
    public $pages = array();
    
    /**
     * @see \wcf\page\IPage::readParameters()
     */
    public function readParameters() {
        parent::readParameters();
        I18nHandler::getInstance()->register('title');
    }
    
    /**
     * @see \wcf\page\IPage::readData()
     */
    public function readData() {
        $this->menuItemNodeList = new MenuItemNodeList(0, 0, true);
        // read category cache
        $cacheName = 'category';
        $cacheBuilderClassName = '\ultimate\system\cache\builder\CategoryCacheBuilder';
        $file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
        CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
        $this->categories = CacheHandler::getInstance()->get($cacheName, 'categoriesNested');
        
        // read page cache
        $cacheName = 'page';
        $cacheBuilderClassName = '\ultimate\system\cache\builder\PageCacheBuilder';
        $file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
        CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
        $this->pages = CacheHandler::getInstance()->get($cacheName, 'pagesNested');
        
        parent::readData();
    }
    
    /**
     * @see \wcf\form\IForm::readFormParameters()
     */
    public function readFormParameters() {
        parent::readFormParameters();
    
        if (isset($_POST['menuName'])) $this->menuName = StringUtil::trim($_POST['menuName']);
    }
    
    /**
     * @see \wcf\form\IForm::validate()
     */
    public function validate() {
        parent::validate();
        $this->validateName();
    }
    
    /**
     * @see \wcf\form\IForm::save()
     */
    public function save() {
        parent::save();
    
        $parameters = array(
            'data' => array(
                'menuName' => $this->menuName
            )
        );
    
        $this->objectAction = new MenuAction(array(), 'create', $parameters);
        $this->objectAction->executeAction();
    
        $returnValues = $this->objectAction->getReturnValues();
        $menuID = $returnValues['returnValues']->menuID;
        $updateValues = array();
        
        $this->saved();
    
        WCF::getTPL()->assign(
            'success', true
        );
        
        $url = LinkHandler::getInstance()->getLink('UltimateMenuEdit', 
            array(
                'id' => $menuID
            )
        );
        HeaderUtil::redirect($url);
        exit;
    }
    
    /**
     * @see \wcf\page\IPage::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
    
        I18nHandler::getInstance()->assignVariables();
        WCF::getTPL()->assign(array(
            'menuName' => $this->menuName,
            'menuItemNodeList' => $this->menuItemNodeList,
            'categories' => $this->categories,
            'pages' => $this->pages,
            'action' => 'add'
        ));
    }
    
    /**
     * Validates the menu name.
     *
     * @throws \wcf\system\exception\UserInputException
     */
    protected function validateName() {
        if (empty($this->menuName)) {
                throw new UserInputException('menuName');
        }
        if (!MenuUtil::isAvailableName($this->menuName)) {
            throw new UserInputException('menuName', 'notUnique');
        }        
    }
    
}
