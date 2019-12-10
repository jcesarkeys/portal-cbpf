<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class SYWFonts 
{	
	static $iconfontLoaded = false;
	static $googlefontLoaded = array();
		
	/**
	 * Load the icon font if needed
	 */
	static function loadIconFont($include_icomoon = false, $debug = false)
	{	
		if (self::$iconfontLoaded) {
			return;
		}
		
		if ($debug) {
			JFactory::getDocument()->addStyleSheet(JURI::base(true).'/media/syw/css/fonts.css');
		} else {
			JFactory::getDocument()->addStyleSheet(JURI::base(true).'/media/syw/css/fonts-min.css');
		}	
		
		if ($include_icomoon) {
			JFactory::getDocument()->addStyleSheet(JURI::base(true).'/media/jui/css/icomoon.css');
		}
						
		self::$iconfontLoaded = true;
	}
	
	/**
	 * Load the Google font if needed
	 */
	static function loadGoogleFont($safefont)
	{
		if (isset(self::$googlefontLoaded[$safefont]) && self::$googlefontLoaded[$safefont]) {
			return;
		}
		
		JFactory::getDocument()->addStyleSheet('https://fonts.googleapis.com/css?family='.$safefont);
		
		self::$googlefontLoaded[$safefont] = true;
	}
	
}
?>
