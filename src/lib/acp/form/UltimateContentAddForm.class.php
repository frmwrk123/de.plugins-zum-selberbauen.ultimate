<?php
namespace ultimate\acp\form;
use ultimate\data\content\ContentAction;
use ultimate\data\content\ContentEditor;
use ultimate\util\ContentUtil;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\tag\Tag;
use wcf\form\MessageForm;
use wcf\form\RecaptchaForm;
use wcf\system\bbcode\URLParser;
use wcf\system\cache\CacheHandler;
use wcf\system\exception\UserInputException;
use wcf\system\language\I18nHandler;
use wcf\system\menu\acp\ACPMenu;
use wcf\system\tagging\TagEngine;
use wcf\system\Regex;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use wcf\util\DateUtil;
use wcf\util\DateTimeUtil;
use wcf\util\MessageUtil;
use wcf\util\StringUtil;

/**
 * Show the UltimateContentAdd form.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	acp.form
 * @category	Ultimate CMS
 */
class UltimateContentAddForm extends MessageForm {
	/**
	 * @see	\wcf\page\AbstractPage::$templateName
	 */
	public $templateName = 'ultimateContentAdd';
	
	/**
	 * @see	\wcf\acp\form\ACPForm::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.ultimate.content.add';
	
	/**
	 * @see	\wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array(
		'admin.content.ultimate.canAddContent'
	);
	
	/**
	 * @see	\wcf\form\MessageForm::$enableMultilangualism
	 */
	public $enableMultilangualism = true;
	
	/**
	 * Contains the description of the content.
	 * @var	string
	 */
	public $description = '';
	
	/**
	 * Contains the slug of the content.
	 * @var	string
	 */
	public $slug = '';
	
	/**
	 * Contains the chosen categories.
	 * @var	integer[]
	 */
	public $categoryIDs = array();
	
	/**
	 * Contains all categories.
	 * @var	\ultimate\data\category\Category[]
	 */
	public $categories = array();
	
	/**
	 * Contains all tags.
	 * @var \wcf\data\tag\Tag[]
	 */
	public $availableTags = array();
	
	/**
	 * Contains all chosen tags.
	 * @var string[]
	 */
	public $tags = array();
	   
	/**
	 * Contains the maximal length of the text.
	 * @var	integer	0 means there's no limitation
	 */
	public $maxTextLength = 0;
	
	/**
	 * Contains the visibility.
	 * @var	string
	 */
	public $visibility = 'public';
	
	/**
	 * Contains the chosen groupIDs.
	 * @var	integer[]
	 */
	public $groupIDs = array();
	
	/**
	 * Contains all available groups.
	 * @var	\wcf\data\user\group\UserGroup[]
	*/
	public $groups = array();
	
	/**
	 * Contains the publish date.
	 * @var	string
	*/
	public $publishDate = '';
	
	/**
	 * Contains the publish date as timestamp.
	 * @var	integer
	 */
	public $publishDateTimestamp = TIME_NOW;
	
	/**
	 * Contains all status options.
	 * @var	string[]
	 */
	public $statusOptions = array();
	
	/**
	 * Contains the status id.
	 * @var	integer
	*/
	public $statusID = 0;
	
	/**
	 * Contains the save type.
	 * @var	string
	 */
	public $saveType = '';
	
	/**
	 * jQuery datepicker date format.
	 * @var	string
	 */
	protected $dateFormat = 'yy-mm-dd';
	
	/**
	 * Contains the timestamp from the begin of the add process.
	 * @var	integer
	 */
	protected $startTime = 0;
	
	/**
	 * @see	\wcf\form\IForm::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		I18nHandler::getInstance()->register('subject');
		I18nHandler::getInstance()->register('description');
		I18nHandler::getInstance()->register('tags');
		I18nHandler::getInstance()->register('text');
	}
	
	/**
	 * @see	\wcf\form\IForm::readData()
	 */
	public function readData() {
		$cacheName = 'category';
		$cacheBuilderClassName = '\ultimate\system\cache\builder\CategoryCacheBuilder';
		$file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$this->categories = CacheHandler::getInstance()->get($cacheName, 'categories');
		unset ($this->categories[1]);
		
		$cacheName = 'usergroups';
		$cacheBuilderClassName = '\wcf\system\cache\builder\UserGroupCacheBuilder';
		$file = WCF_DIR.'cache/cache.'.$cacheName.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$this->groups = CacheHandler::getInstance()->get($cacheName, 'groups');
		
		// read tags
		$cacheName = 'content-tag';
		$cacheBuilderClassName = '\ultimate\system\cache\builder\ContentTagCloudCacheBuilder';
		$file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$tags = CacheHandler::getInstance()->get($cacheName);
		
		$languages = WCF::getLanguage()->getLanguages();
		
		/* @var $language \wcf\data\language\Language */
		/* @var $tag \wcf\data\tag\TagCloudTag */
		foreach ($languages as $languageID => $language) {
			$this->availableTags[$languageID] = array();
			
			foreach ($tags as $tagName => $tag) {
				if ($tag->__get('languageID') != $languageID) continue;
				$this->availableTags[$languageID] = $tag;
			}
		}
		
		// fill status options
		$this->statusOptions[0] = WCF::getLanguage()->get('wcf.acp.ultimate.status.draft');
		$this->statusOptions[1] = WCF::getLanguage()->get('wcf.acp.ultimate.status.pendingReview');
		
		// fill publishDate with default value (today)
		/* @var $dateTime \DateTime */
		$dateTime = null;
		if (isset($this->content)) {
			$dateTime = $this->content->__get('publishDateObject');
		}
		if (!$dateTime->getTimestamp()) $dateTime = DateUtil::getDateTimeByTimestamp(TIME_NOW);
		$dateTime->setTimezone(WCF::getUser()->getTimezone());
		$date = WCF::getLanguage()->getDynamicVariable(
			'ultimate.date.dateFormat',
			array(
				'britishEnglish' => ULTIMATE_GENERAL_ENGLISHLANGUAGE
			)
		);
		$time = WCF::getLanguage()->get('wcf.date.timeFormat');
		$format = str_replace(
			'%time%',
			$time,
			str_replace(
				'%date%',
				$date,
				WCF::getLanguage()->get('ultimate.date.dateTimeFormat')
			)
		);
		$this->publishDate = $dateTime->format($format);
		$this->publishDateTimestamp = $dateTime->getTimestamp();
		
		parent::readData();
	}
	
	/**
	 * @see	\wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		I18nHandler::getInstance()->readValues();
		if (I18nHandler::getInstance()->isPlainValue('subject')) $this->subject = StringUtil::trim(I18nHandler::getInstance()->getValue('subject'));
		if (I18nHandler::getInstance()->isPlainValue('description')) $this->description = StringUtil::trim(I18nHandler::getInstance()->getValue('description'));
		if (isset($_POST['slug'])) $this->slug = StringUtil::trim($_POST['slug']);
		if (isset($_POST['categoryIDs']) && is_array($_POST['categoryIDs'])) $this->categoryIDs = ArrayUtil::toIntegerArray(($_POST['categoryIDs']));
		if (I18nHandler::getInstance()->isPlainValue('text')) $this->text = MessageUtil::stripCrap(trim(I18nHandler::getInstance()->getValue('text')));
		if (isset($_POST['visibility'])) $this->visibility = StringUtil::trim($_POST['visibility']);
		if (isset($_POST['groupIDs'])) $this->groupIDs = ArrayUtil::toIntegerArray($_POST['groupIDs']);
		if (isset($_POST['publishDate'])) $this->publishDate = StringUtil::trim($_POST['publishDate']);
		if (isset($_POST['dateFormat'])) $this->dateFormat = StringUtil::trim($_POST['dateFormat']);
		if (isset($_POST['save'])) $this->saveType = 'save';
		if (isset($_POST['publish'])) $this->saveType = 'publish';
		if (isset($_POST['startTime'])) $this->startTime = intval($_POST['startTime']);
	}
	
	/**
	 * @see	\wcf\form\IForm::validate()
	 */
	public function validate() {
		$this->validateSubject();
		$this->validateDescription();
		$this->validateSlug();
		$this->validateCategories();
		$this->validateTags();
		$this->validateText();
		// multilingualism
		$this->validateContentLanguage();
		$this->validateStatus();
		$this->validateVisibility();
		$this->validatePublishDate();
		
		RecaptchaForm::validate();
	}
	
	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		if (!I18nHandler::getInstance()->isPlainValue('text')) RecaptchaForm::save();
		else parent::save();
		
		// change status to planned or publish
		if ($this->saveType == 'publish') {
			if ($this->publishDateTimestamp > TIME_NOW) {
				$this->statusID = 2; // planned
			} elseif ($this->publishDateTimestamp < TIME_NOW) {
				$this->statusID = 3; // published
			}
		}
		
		$parameters = array(
			'data' => array(
				'authorID' => WCF::getUser()->userID,
				'contentTitle' => $this->subject,
				'contentDescription' => $this->description,
				'contentSlug' => $this->slug,
				'contentText' => $this->text,
				'enableBBCodes' => $this->enableBBCodes,
				'enableHtml' => $this->enableHtml,
				'enableSmilies' => $this->enableSmilies,
				'publishDate' => $this->publishDateTimestamp,
				'lastModified' => TIME_NOW,
				'status' => $this->statusID,
				'visibility' => $this->visibility
			),
			'categories' => $this->categoryIDs
		);
		
		if ($this->visibility == 'protected') {
			$parameters['groupIDs'] = $this->groupIDs;
		}
		
		$this->objectAction = new ContentAction(array(), 'create', $parameters);
		$this->objectAction->executeAction();
		
		$returnValues = $this->objectAction->getReturnValues();
		$contentID = $returnValues['returnValues']->contentID;
		$updateEntries = array();
		if (!I18nHandler::getInstance()->isPlainValue('subject')) {
			I18nHandler::getInstance()->save('subject', 'ultimate.content.'.$contentID.'.contentTitle', 'ultimate.content', PACKAGE_ID);
			$updateEntries['contentTitle'] = 'ultimate.content.'.$contentID.'.contentTitle';
		}
		if (!I18nHandler::getInstance()->isPlainValue('description')) {
			I18nHandler::getInstance()->save('description', 'ultimate.content.'.$contentID.'.contentDescription', 'ultimate.content', PACKAGE_ID);
			$updateEntries['contentDescription'] = 'ultimate.content.'.$contentID.'.contentDescription';
		}
		if (!I18nHandler::getInstance()->isPlainValue('text')) {
			I18nHandler::getInstance()->save('text', 'ultimate.content.'.$contentID.'.contentText', 'ultimate.content', PACKAGE_ID);
			$updateEntries['contentText'] = 'ultimate.content.'.$contentID.'.contentText';
			
			// parse URLs
			if ($this->parseURL == 1) {
				$textValues = I18nHandler::getInstance()->getValues('text');
				foreach ($textValues as $languageID => $text) {
					$textValues[$languageID] = URLParser::getInstance()->parse($text);
				}
				
				// nasty workaround, because you can't change the values of I18nHandler before save
				$sql = 'UPDATE wcf'.WCF_N.'_language_item
						SET	languageItemValue = ?
						WHERE  languageID		= ?
						AND	languageItem	  = ?
						AND	packageID		 = ?';
				$statement = WCF::getDB()->prepareStatement($sql);
				WCF::getDB()->beginTransaction();
				foreach ($textValues as $languageID => $text) {
					$statement->executeUnbuffered(array(
						$text,
						$languageID,
						'ultimate.content.'.$contentID.'.contentText',
						PACKAGE_ID
					));
				}
				WCF::getDB()->commitTransaction();
			}
		}
		if (!empty($updateEntries)) {
			$contentEditor = new ContentEditor($returnValues['returnValues']);
			$contentEditor->update($updateEntries);
		}
		
		// save tags
		foreach ($this->tags as $languageID => $tags) {
			TagEngine::getInstance()->addObjectTags('de.plugins-zum-selberbauen.ultimate.contentTaggable', $contentID, $tags, $languageID);
		}
		
		$this->saved();
		
		WCF::getTPL()->assign('success', true);
		
		// showing empty form
		$this->subject = $this->description = $this->text = $this->publishDate = '';
		$this->publishDateTimestamp = $this->statusID = 0;
		$this->visibility = 'public';
		I18nHandler::getInstance()->disableAssignValueVariables();
		$this->categoryIDs = $this->groupIDs = array();
	}
	
	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		I18nHandler::getInstance()->assignVariables();
		WCF::getTPL()->assign(array(
			'description' => $this->description,
			'slug' => $this->slug,
			'action' => 'add',
			'categoryIDs' => $this->categoryIDs,
			'categories' => $this->categories,
			'languageID' => ($this->languageID ? $this->languageID : 0),
			'availableTags' => $this->availableTags,
			'tags' => $tags,
			'groups' => $this->groups,
			'groupIDs' => $this->groupIDs,
			'statusOptions' => $this->statusOptions,
			'statusID' => $this->statusID,
			'visibility' => $this->visibility,
			'startTime' => $this->startTime,
			'publishDate' => $this->publishDate
		));
	}
	
	/**
	 * @see	\wcf\page\IPage::show()
	 */
	public function show() {
		if (!empty($this->activeMenuItem)) {
			ACPMenu::getInstance()->setActiveMenuItem($this->activeMenuItem);
		}
		parent::show();
	}
	
	/**
	 * Validates content subject.
	 * 
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateSubject() {
		if (!I18nHandler::getInstance()->isPlainValue('subject')) {
			if (!I18nHandler::getInstance()->validateValue('subject')) {
				throw new UserInputException('subject');
			}
			$subjectValues = I18nHandler::getInstance()->getValues('subject');
			foreach ($subjectValues as $languageID => $subject) {
				if (strlen($subject) < 4) {
					throw new UserInputException('subject', 'tooShort');
				}
			}
		} else {
			// checks if subject is empty; we don't have to do it twice
			parent::validateSubject();
	
			if (strlen($this->subject) < 4) {
				throw new UserInputException('subject', 'tooShort');
			}
		}
	}
	
	/**
	 * Validates content description.
	 * 
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateDescription() {
		if (!I18nHandler::getInstance()->isPlainValue('description')) {
			if (!I18nHandler::getInstance()->validateValue('description')) {
				throw new UserInputException('description');
			}
			$descriptionValues = I18nHandler::getInstance()->getValues('description');
			foreach ($descriptionValues as $languageID => $description) {
				if (strlen($description) < 4) {
					throw new UserInputException('description', 'tooShort');
				}
			}
		}
		else {
			if (empty($this->description)) {
				throw new UserInputException('description');
			}
	
			if (strlen($this->description) < 4) {
				throw new UserInputException('description', 'tooShort');
			}
		}
	}
	
	/**
	 * Validates the slug.
	 * 
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateSlug() {
		if (empty($this->slug)) {
			throw new UserInputException('slug');
		}
		if (!ContentUtil::isAvailableSlug($this->slug, (isset($this->contentID)) ? $this->contentID : 0)) {
			throw new UserInputException('slug', 'notUnique');
		}
	}
	
	/**
	 * Validates content text.
	 * 
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateText() {
		if (!I18nHandler::getInstance()->isPlainValue('text')) {
			if (!I18nHandler::getInstance()->validateValue('text')) {
				throw new UserInputException('text');
			}
			$textValues = I18nHandler::getInstance()->getValues('description');
			foreach ($textValues as $languageID => $text) {
				if ($this->maxTextLength != 0 && strlen($text) > $this->maxTextLength) {
					throw new UserInputException('text', 'tooLong');
				}
			}
		}
		else {
			parent::validateText();
		}
	}
	
	/**
	 * Validates category.
	 * 
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateCategories() {
		// reading cache
		$cacheName = 'category';
		$cacheBuilderClassName = '\ultimate\system\cache\builder\CategoryCacheBuilder';
		$file = ULTIMATE_DIR.'cache/cache.'.$cacheName.'.php';
		CacheHandler::getInstance()->addResource($cacheName, $file, $cacheBuilderClassName);
		$categoryIDs = CacheHandler::getInstance()->get($cacheName, 'categoryIDs');
		foreach ($this->categoryIDs as $categoryID) {
			if (in_array($categoryID, $categoryIDs)) continue;
			throw new UserInputException('category', 'invalidIDs');
			break;
		}
		// add default category
		if (empty($this->categoryIDs)) {
			$this->categoryIDs[] = 1;
		}
	}
	
	/**
	 * Validates the tags.
	 * 
	 * @throws \wcf\system\exception\UserInputException
	 */
	protected function validateTags() {
		if (!I18nHandler::getInstance()->validateValue('tags')) {
			throw new UserInputException('tags');
		}
		$tagValues = I18nHandler::getInstance()->getValues('tags');
		foreach ($tagValues as $languageID => $tags) {
			$this->tags[$languageID] = Tag::splitString($tags);
		}
	}
	
	/**
	 * Validates status.
	 * 
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateStatus() {
		if (!array_key_exists($this->statusID, $this->statusOptions)) {
			throw new UserInputException('status', 'notValid');
		}
	}
	
	/**
	 * Validates visibility.
	 * 
	 * @throws	\wcf\system\exception\UserInputException
	 */
	protected function validateVisibility() {
		$allowedValues = array(
			'public',
			'protected',
			'private'
		);
		if (!in_array($this->visibility, $allowedValues)) {
			throw new UserInputException('visibility', 'notValid');
		}
		
		// validate groupIDs, only important for protected
		if ($this->visibility != 'protected') return;
		
		if (empty($this->groupIDs)) {
			throw new UserInputException('groupIDs', 'notSelected');
		}
		
		foreach ($this->groupIDs as $groupID) {
			if (array_key_exists($groupID, $this->groups)) continue;
			throw new UserInputException('groupIDs', 'notValid');
			break;
		}
	}
	
	/**
	 * Validates the publish date.
	 * 
	 * @throws	\wcf\system\exception\UserInputException
	 * @throws	\wcf\system\exception\SystemException
	 */
	protected function validatePublishDate() {
		if (empty($this->publishDate)) {
			throw new UserInputException('publishDate');
		}
		
		$pattern = '\d{4}-\d{2}-\d{2} \d{2}:\d{2}';
		$regex = new Regex($pattern);
		$dateTimeNow = new \DateTime('@'.TIME_NOW, WCF::getUser()->getTimezone());
		if ($regex->match($this->publishDate)) {
			// the browser has implemented the input type date
			// or (more likely) the user hasn't changed the jQuery code
			// that means we get the date in the right order for processing
			$dateTime = \DateTime::createFromFormat(
				'Y-m-d H:i',
				$this->publishDate,
				WCF::getUser()->getTimezone()
			);
			$this->publishDateTimestamp = $dateTime->getTimestamp();
			return;
		}
		// for the very unlikely reason that the date is not in the format
		// Y-m-d, we have to make it that way
		$phpDateFormat = DateTimeUtil::getPHPDateFormatFromDateTimePicker($this->dateFormat);
		$phpDateFormat .= ' H:i';
		$dateTime = \DateTime::createFromFormat(
			$phpDateFormat,
			$this->publishDate,
			WCF::getUser()->getTimezone()
		);
		$this->publishDateTimestamp = $dateTime->getTimestamp();
	}
	
}
