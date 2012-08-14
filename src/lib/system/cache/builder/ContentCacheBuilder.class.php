<?php
namespace ultimate\system\cache\builder;
use ultimate\data\content\ContentList;
use ultimate\data\content\TaggableContent;
use ultimate\data\content\TaggedContent;
use wcf\system\cache\builder\ICacheBuilder;

/**
 * Caches the contents.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	system.cache.builder
 * @category	Ultimate CMS
 */
class ContentCacheBuilder implements ICacheBuilder {
	/**
	 * @see	\wcf\system\cache\builder\ICacheBuilder::getData()
	 */
	public function getData(array $cacheResource) {
		$data = array(
			'contents' => array(),
			'contentIDs' => array()
		);
		
		$contentList = new ContentList();
		// order by default
		$sortField = ULTIMATE_SORT_CONTENT_SORTFIELD;
		$sortOrder = ULTIMATE_SORT_CONTENT_SORTORDER;
		$sqlOrderBy = $sortField." ".$sortOrder;
		$contentList->sqlOrderBy = $sqlOrderBy;
		
		$contentList->readObjects();
		$contents = $contentList->getObjects();
		if (empty($contents)) return $data;
		
		foreach ($contents as $contentID => $content) {
			/* @var $content \ultimate\data\content\Content */
			$data['contents'][$contentID] = new TaggableContent($content);
			$data['contentIDs'][] = $contentID;
			
			$taggedContent = new TaggedContent($content);
			if (!empty($taggedContent->tags)) {
				$data['contents'][$contentID] = $taggedContent;
			}
		}
		
		return $data;
	}
}
