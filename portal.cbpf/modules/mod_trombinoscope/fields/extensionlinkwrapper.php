<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.folder');
JFormHelper::loadFieldClass('extensionlink');

class JFormFieldExtensionLinkWrapper extends JFormFieldExtensionLink 
{	
	public $type = 'ExtensionLinkWrapper';
	
	static $is_Standalone;
	
	static function isStandalone() {
		
		if (!isset(self::$is_Standalone)) {
			
			self::$is_Standalone = false;
			
			$folder = JPATH_ROOT.'/components/com_trombinoscopeextended/views/trombinoscope'; // when adding themes, even if the component is not installed, it adds the folder
			if (!JFolder::exists($folder)) {
				self::$is_Standalone = true;
			}
		}

		return self::$is_Standalone;
	}
	
	public function getLabel() {		
		$condition = ($this->element['condition'] == "true") ? true : false;
		if ((self::isStandalone() && $condition) || (!self::isStandalone() && !$condition)) {
			return parent::getLabel();
		}
			
		return '';
	}
	
	public function getInput() {
		$condition = ($this->element['condition'] == "true") ? true : false;
		if ((self::isStandalone() && $condition) || (!self::isStandalone() && !$condition)) {
			return parent::getInput();
		}
		
		return '';
	}
	
	/**
	 * @since      3.2
	 * @deprecated 3.2.3 Use renderField() instead
	 */
	public function getControlGroup()
	{
		$condition = ($this->element['condition'] == "true") ? true : false;
		if ((self::isStandalone() && $condition) || (!self::isStandalone() && !$condition)) {
			return parent::getControlGroup();
		}
		
		return '';
	}
	
	public function renderField($options = array())
	{
		$condition = ($this->element['condition'] == "true") ? true : false;
		if ((self::isStandalone() && $condition) || (!self::isStandalone() && !$condition)) {
			return parent::renderField();
		}
		
		return '';
	}
	
}
?>