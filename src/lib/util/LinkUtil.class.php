<?php
namespace ultimate\util;
use ultimate\util\thirdParty\Net\NET_IDNA2; // changed class to work with namespaces
use wcf\system\cache\CacheHandler;
use wcf\system\Regex;
use wcf\util\StringUtil;

/**
 * Provides useful functions for links.
 * 
 * @author		Jim Martens
 * @copyright	2011-2012 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.ultimate
 * @subpackage	util
 * @category	Ultimate CMS
 */
class LinkUtil {
	/**
	 * Checks whether the given URL is available or not.
	 * 
	 * @since	1.0.0
	 * @api
	 * 
	 * @param	string	$linkURL
	 * @param	integer	$linkID		optional, default is 0
	 * @return	boolean	$isAvailable
	 */
	public static function isAvailableURL($linkURL, $linkID = 0) {
		$linkURL = StringUtil::trim($linkURL);
		$linkID = intval($linkID);
		$isAvailable = true;
	
		$links = self::loadCache(
			'link',
			'\ultimate\system\cache\builder\LinkCacheBuilder',
			'links'
		);
		
		foreach ($links as $link) {
			/* @var $link \ultimate\data\link\CategorizedLink */
			if ($link->__get('linkID') == $linkID || $link->__get('linkURL') != $linkURL) continue;
			$isAvailable = false;
			break;
		}
	
		return $isAvailable;
	}
	
	/**
	 * Checks whether the given url is valid or not.
	 * 
	 * @since	1.0.0
	 * @api
	 * 
	 * @param	string	$url
	 * @return	boolean
	 */
	public static function isValidURL($url) {
		$url = self::parseURL(StringUtil::trim($url));
		$isValid = false;
		
		// prevents unnecessary check if URL is already proven wrong
		if ($url === false) return $isValid;
		/*
		 *  Source: http://hx3.de/software-webentwicklung-23/php-url-uri-validation-idn-internationalized-domain-name-support-17404/
		 *  Source: http://www.martin-helmich.de/?p=36
		 *  Source: http://phpcentral.com/208-url-validation-in-php.html
		 *  used parts of all of them
		 */
		
		// The scheme
		$pattern =	'^';
		$patterm .= '(?:https?|ftp)\:\/\/';
		// The domain
		$pattern .= '(?:';
		
		$pattern .= '(?:';
		// Domain name or IPv4
		$pattern .=	'(?:(?:[a-zA-Z][a-zA-Z0-9\-]+\.)+[a-zA-Z\-]+)|'.
					'(?:'.
					'(?:2(?:5[0-5]|[0-4][0-9])|[01][0-9]{2}|[0-9]{1,2})\.'.
					'(?:2(?:5[0-5]|[0-4][0-9])|[01][0-9]{2}|[0-9]{1,2})\.'.
					'(?:2(?:5[0-5]|[0-4][0-9])|[01][0-9]{2}|[0-9]{1,2})\.'.
					'(?:2(?:5[0-5]|[0-4][0-9])|[01][0-9]{2}|[0-9]{1,2})'.
					')';
		// or IPv6
		$pattern .= '|(?:\[(?:[0-9a-f]{0,4}:)*(?:[0-9a-f]{0,4})\])';
		$pattern .= ')';
		// Server port number (optional)";
		$pattern .= '(?:\:[0-9]{1,5})?';
		// The path (optional)
        $pattern .= '(?:\/(?:[\w0-9+,;\$_-]\.?)+)*\/?';
		// GET Query (optional)
		$pattern .= '(\?[a-z+&\$_.-][a-z0-9;:@/&%=+\$_.-]*)?';
		$pattern .= '$';
		
		// checks if URL is valid
		$regexURL = new Regex($pattern, Regex::CASE_INSENSITIVE);
		$isValid = (boolean) $regexURL->match($url);
		// prevents HTTP requests if URL is invalid anyway
		if (!$isValid) return $isValid;
		
		if (ini_get('allow_url_fopen') == '0') {
			// prevents exception
			return $isValid;
		}
		// checks if URL is accessible
		// Source: http://www.php.net/manual/en/function.file-exists.php#84918
		$hdrs = get_headers($url);
		$headerRegex = new Regex('^HTTP\\/\\d+\\.\\d+\\s+2\\d\\d\\s+.*$');
		$isValid =  is_array($hdrs) ? (boolean) $headerRegex->match($hdrs[0]) : false;
		return $isValid;
	}
	
	/**
	 * Decode Punycode-Domain to IDN-Domain.
	 * 
	 * @since	1.0.0
	 * @api
	 * 
	 * @param	string	$punycodeDomain
	 * @author	Herbert Walde
	 * @see		http://hx3.de/software-webentwicklung-23/php-e-mail-validation-idn-internationalized-domain-name-support-17398/
	 * @return	string	domain in unicode
	 */
	public static function decodePunycodeDomain($punycodeDomain) {
		$punycodeDomain = StringUtil::trim($punycodeDomain);
		$idnaConverter = new NET_IDNA2();
		return $idnaConverter->decode($punycodeDomain);
	}
	
	/**
	 * Encode IDN-Domain to Punycode.
	 * 
	 * @since	1.0.0
	 * @api
	 * 
	 * @param	string	$unicodeDomain
	 * @author	Herbert Walde
	 * @see		http://hx3.de/software-webentwicklung-23/php-e-mail-validation-idn-internationalized-domain-name-support-17398/
	 * @return	string	domain in punycode
	 */
	public static function encodePunycodeDomain($unicodeDomain) {
		$unicodeDomain = StringUtil::trim($unicodeDomain);
		$idnaConverter = new NET_IDNA2();
		return $idnaConverter->encode($unicodeDomain);
	}
	
	/**
	 * Parses a given URL and encodes it to punycode if necessary.
	 * 
	 * @since	1.0.0
	 * @api
	 * 
	 * @param	string	$url
	 * @return	string|false	the parsed URL or false on failure
	 */
	public static function parseURL($url) {
		$hostname = parse_url($url);
		if (isset($hostname) && $hostname !== false && isset($hostname['host'])) {
			$hostname = StringUtil::trim($hostname['host']);
			if (!empty($hostname)) {
				$res = strpos($url, $hostname);
				if ($res !== false) {
					// There is data to be replaced
					$left_seg = substr($url, 0, strpos($url, $hostname));
					$right_seg = substr($url, (strpos($url, $hostname) + strlen($hostname)));
					$url = $left_seg . self::encodePunycodeDomain($hostname) . $right_seg;
				}
			}
		} elseif ($hostname === false) {
			return false;
		}
		return $url;
	}
	
	/**
	 * Loads the cache.
	 *
	 * @return	\ultimate\data\link\CategorizedLink[]
	 */
	protected static function loadCache($cache, $cacheBuilderClass, $cacheIndex) {
		$file = ULTIMATE_DIR.'cache/cache.'.$cache.'.php';
		CacheHandler::getInstance()->addResource($cache, $file, $cacheBuilderClass);
		return CacheHandler::getInstance()->get($cache, $cacheIndex);
	}
	
	/**
	 * Constructor not supported
	 */
	private function __construct() {}
}