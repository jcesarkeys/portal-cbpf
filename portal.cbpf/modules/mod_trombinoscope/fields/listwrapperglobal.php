<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.folder');
JFormHelper::loadFieldClass('list');

class JFormFieldListWrapperGlobal extends JFormFieldList
{
	public $type = 'ListWrapperGlobal';
	
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
	
	protected function getOptions()
	{
		$options = array();
		
		$options = array_merge($options, parent::getOptions());
		
		if (!self::isStandalone()) {
			
			$params = JComponentHelper::getParams('com_trombinoscopeextended');
			$value  = $params->get($this->fieldname);
			$text = '';
			
			if (!is_null($value)) {
				$value = (string) $value;
				
				foreach ($options as $option) {
					if (isset($option->value) && $option->value === $value) {
						$text = ' ('.JText::_($option->text).')';
						break;
					}
				}
			}
			
			$global_option = JHTML::_('select.option', '', JText::_('JGLOBAL_USE_GLOBAL').$text, 'value', 'text', $disable = false);
			
			array_unshift($options, $global_option);
		}
		
		return $options;
	}
	
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);
		
		if ($return) {
			if (!self::isStandalone()) {
				$defaultvalue = '';
			} else {
				$defaultvalue = $this->element['standalonedefault'];
			}
			
			$this->value = isset($value) && !empty($value) ? $value : $defaultvalue;
		}
		
		return $return;
	}
	
}
?>