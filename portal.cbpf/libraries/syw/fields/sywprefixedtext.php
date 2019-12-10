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
class JFormFieldSYWPrefixedText extends JFormField 
{
	protected $type = 'SYWPrefixedText';
	
	protected $prefix;
	protected $postfix;
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
		
		$hint = $this->translateHint ? JText::_($this->hint) : $this->hint;
		$hint = $hint ? ' placeholder="'.$hint.'"' : '';
				
		$overall_class = empty($this->prefix) ? '' : 'input-prepend';
		$overall_class .= empty($this->postfix) ? '' : ' input-append';
		$overall_class = trim($overall_class);
		$overall_class = empty($overall_class) ? '' : ' class="'.$overall_class.'"';
		
		$html .= '<div'.$overall_class.'>';
		
		if ($this->prefix) {
			$html .= '<div class="add-on">';
			
			if ($this->icon) {
				JHtml::_('stylesheet', 'syw/fonts-min.css', false, true);
				$html .= '<i class="'.$this->icon.'"></i>&nbsp;';
			}
			
			$html .= '<span>'.$this->prefix.'</span>';
			
			$html .= '</div>';
		}
		
		$html .= '<input type="text" name="'.$this->name.'" id="'.$this->id.'"'.' value="'.htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8').'"'.$style.$size.$this->maxLength.$hint.' />';
		
		if ($this->postfix) {
			$html .= '<div class="add-on">'.$this->postfix.'</div>';
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
			$this->prefix = isset($this->element['prefix']) ? $this->element['prefix'] : '';
			$this->postfix = isset($this->element['postfix']) ? $this->element['postfix'] : '';
			$this->help = isset($this->element['help']) ? $this->element['help'] : '';
			$this->icon = isset($this->element['icon']) ? $this->element['icon'] : '';
			$this->maxLength = isset($this->element['maxlength']) ? ' maxlength="'.$this->maxLength.'"' : '';
		}

		return $return;
	}

}
?>