<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class SYWStylesheets {
	
	static $twodtransitionsLoaded = false;
	static $bgtransitionsLoaded = false;
		
	/**
	 * Load the 2d transitions stylesheet if needed
	 */
	static function load2DTransitions()
	{
		if (self::$twodtransitionsLoaded) {
			return;
		}
		
		$doc = JFactory::getDocument();	
			
		$doc->addStyleSheet(JURI::root(true).'/media/syw/css/2d-transitions-min.css');
			
		self::$twodtransitionsLoaded = true;
	}
	
	/**
	 * Load the background transitions stylesheet if needed
	 */
	static function loadBGTransitions()
	{
		if (self::$bgtransitionsLoaded) {
			return;
		}
	
		$doc = JFactory::getDocument();
			
		$doc->addStyleSheet(JURI::root(true).'/media/syw/css/bg-transitions-min.css');
			
		self::$bgtransitionsLoaded = true;
	}
	
}
?>
