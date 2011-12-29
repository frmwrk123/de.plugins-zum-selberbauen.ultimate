<?php
namespace ultimate\system;
use wcf\system\exception\IllegalLinkException;
use wcf\util\FileUtil;
use wcf\util\StringUtil;
use wcf\system\cache\CacheHandler;
use wcf\system\SingletonFactory;

/**
 * Handles the incoming links.
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage system
 * @category Ultimate CMS
 */
class Dispatcher extends SingletonFactory {
    
    /**
     * Contains the request data.
     * @var string
     */
    protected $requestURI = '';
    
    /**
     * Handles a http request.
     */
    public function handle($debug = false) {
        if (isset($_GET['request'])) $this->requestURI = FileUtil::removeTrailingSlash(FileUtil::removeLeadingSlash(StringUtil::trim($_GET['request'])));
        
        //loading links from cache
        $cache = 'ultimate-links-'.PACKAGE_ID;
        $file = ULTIMATE_DIR.'cache/cache.'.$cache.'.php';
        $className = 'ultimate\system\cache\builder\UltimateLinksCacheBuilder';
        CacheHandler::getInstance()->addResource($cache, $file, $className);
        $linkList = array();
        $linkList = CacheHandler::getInstance()->get($cache, 'links');
        
        if (!in_array($this->requestURI, $linkList)) {
            throw new IllegalLinkException();
        }
        //loading configurations from cache
        $viewConfigurations = array();
        $viewConfigurations = CacheHandler::getInstance()->get($cache, 'configs');
        
        //$config['templateName'] = template name of the overall generated template
        //$config['content'] = array ('content id' => '*ComponentPage')
        $config = $viewConfigurations[$this->requestURI];
        $callData = array(
        	'templateName' => $config['templateName'],
            'content' => array()
        );
        foreach ($config['content'] as $id => $component) {
            $result = new $component($id); //returns output
            $callData['content'][$id] = $result;
        }
        
        if ($debug) {
            echo $this->getRequestURI();
            exit;
        }
        $controllerObj = 'ultimate\page\GenericCMSPage';
        new $controllerObj();
    }
    
    /**
     * Returns the request uri.
     */
    public function getRequestURI() {
        return $this->requestURI;
    }
    
}
