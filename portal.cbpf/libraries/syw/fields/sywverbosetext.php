<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.form.formfield');

/**
 * 
 * @author Olivier Buisard
 * 
 * for Joomla 3+ ONLY
 *
 */
class JFormFieldSYWVerboseText extends JFormField 
{
	protected $type = 'SYWVerboseText';
	
	protected $max;
	protected $min;
	protected $unit;
	protected $icon;
	protected $help;
	protected $maxLength;
	
	protected function getInput() 
	{	
		$html = '';
	
		$lang = JFactory::getLanguage();
		$lang->load('lib_syw.sys', JPATH_SITE);
		
		$size = !empty($this->size) ? ' size="' . $this->size . '"' : '';		
		$style = empty($size) ? '' : ' style="width:auto"';
		
		$min = isset($this->min) ? JText::_('LIB_SYW_SYWVERBOSETEXT_MIN').': '.$this->min : '';
		$max = isset($this->max) ? JText::_('LIB_SYW_SYWVERBOSETEXT_MAX').': '.$this->max : '';

		$range = (!empty($min) && !empty($max)) ? $min.' - '.$max : '';
		if (empty($range)) {
			$range = !empty($min) ? $min : '';
		}		
		if (empty($range)) {
			$range = !empty($max) ? $max : '';
		}

		$hint = $this->translateHint ? JText::_($this->hint) : $this->hint;
		$hint = $hint ? ' placeholder="'.$hint.'"' : (!empty($range) ? ' placeholder="'.$range.'"' : '');
				
		$overall_class = empty($this->icon) ? '' : 'input-prepend';
		$overall_class .= empty($this->unit) ? '' : ' input-append';
		$overall_class = trim($overall_class);
		$overall_class = empty($overall_class) ? '' : ' class="'.$overall_class.'"';
		
		$html .= '<div'.$overall_class.'>';
		
		if ($this->icon) {
			JHtml::_('stylesheet', 'syw/fonts-min.css', false, true);
			$html .= '<div class="add-on"><i class="'.$this->icon.'"></i></div>';
		}
		
		$html .= '<input type="text" name="'.$this->name.'" id="'.$this->id.'"'.' value="'.htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8').'"'.$style.$size.$this->maxLength.$hint.' />';
		
		if ($this->unit) {
			$html .= '<div class="add-on">'.$this->unit.'</div>';
		}
		
		$html .= '</div>';
		
		if ($this->help) {
			$html .= '<span class="help-block">'.JText::_($this->help).'</span>';
		}
		
		return $html;
	}
	
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);

		if ($return) {
			$this->max = isset($this->element['max']) ? $this->element['max'] : null;
			$this->min = isset($this->element['min']) ? $this->element['min'] : null;
			$this->unit = isset($this->element['unit']) ? $this->element['unit'] : '';
			$this->help = isset($this->element['help']) ? $this->element['help'] : '';
			$this->icon = isset($this->element['icon']) ? $this->element['icon'] : '';
			$this->maxLength = isset($this->element['maxlength']) ? ' maxlength="' . $this->maxLength . '"' : '';
		}

		return $return;
	}

}
?>