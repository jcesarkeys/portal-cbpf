<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die ;

jimport('joomla.form.formfield');

/**
 *
 * deprecated. Use sywcolorpicker instead
 *
 */
class JFormFieldColorPicker extends JFormField 
{
		
	public $type = 'ColorPicker';
	
	static $mrLoaded = false;
	
	protected function getInput() 
	{		
		$doc = JFactory::getDocument();	
		
		$lang = JFactory::getLanguage();
		$lang->load('lib_syw.sys', JPATH_SITE);
		
		JHtml::_('bootstrap.tooltip');
		
		$allow_transparency = (trim($this->element['transparency']) === "true") ? true : false;
					
		$html = '';
			
		$color = strtolower($this->value);
			
		if (!$color || in_array($color, array('none', 'transparent'))) {
			$color = '';
		} elseif ($color['0'] != '#') {
			$color = '#' . $color;
		}
			
		JHtml::_('behavior.colorpicker');
			
		if ($allow_transparency) {

			$html .= '<div class="input-append">';

			$html .= '<input style="height:18px" type="text" name="' . $this->name . '" id="' . $this->id . '"' . ' value="'. htmlspecialchars($color, ENT_COMPAT, 'UTF-8') . '"' . ' class="' . 'minicolors' . '"' . '/>';

			$html .= '<a id="a_'.$this->id.'" class="btn hasTooltip" title="'.JText::_('JLIB_FORM_BUTTON_CLEAR').'" href="#" onclick="return false;">';
			$html .= '<i class="icon-remove"></i>';
			$html .= '</a>';

			$html .= '</div>';

			$doc->addScriptDeclaration("
				jQuery(document).ready(function (){
					jQuery('#a_".$this->id."').click(function() {
						jQuery('#".$this->id."').parent().find('span').first().children().css('background-color','transparent');
						jQuery('#".$this->id."').val('');
					});
				});
			");
		} else {
			$html .= '<input type="text" name="' . $this->name . '" id="' . $this->id . '"' . ' value="'. htmlspecialchars($color, ENT_COMPAT, 'UTF-8') . '"' . ' class="' . 'minicolors' . '"' . '/>';
		}
		
		return $html;
	}

}
?>
