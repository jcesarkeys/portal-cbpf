<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

// no direct access
defined( '_JEXEC' ) or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

JFormHelper::loadFieldClass('dynamicsingleselect');

class JFormFieldStyleSelect extends JFormFieldDynamicSingleSelect
{
	public $type = 'StyleSelect';
	
	protected $img_suffix;

	protected function getOptions() 
	{
		$options = array();
		
		$lang = JFactory::getLanguage();
		
		$path = '/modules/mod_trombinoscope/themes';		
		
		$optionsArray = JFolder::folders(JPATH_SITE.$path);
		
		foreach($optionsArray as $option) {
			
			$upper_option = strtoupper($option);
			
			$lang->load('com_trombinoscopeextended_theme_'.$option);
			
			$translated_option = JText::_('MOD_TROMBINOSCOPE_THEME_'.$upper_option.'_LABEL');

			$description = '';
			if (empty($translated_option) || substr_count($translated_option, 'TROMBINOSCOPE') > 0) {
				$translated_option = ucfirst($option);
			} else {
				$description = JText::_('MOD_TROMBINOSCOPE_THEME_'.$upper_option.'_DESC');
				if (substr_count($description, 'TROMBINOSCOPE') > 0) {
					$description = '';
				}
			}
			
			$image_hover = '';
			
			$options[] = array($option, $translated_option, $description, JURI::root(true).$path.'/'.$option.'/images/'.$option.$this->img_suffix.'.png', $image_hover);
		}
		
		return $options;
	}

	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);

		if ($return) {
			$this->width = 150;
			$this->height = 150;
			$this->img_suffix = isset($this->element['imgsuffix']) ? $this->element['imgsuffix'] : '';
		}

		return $return;
	}
}
?>