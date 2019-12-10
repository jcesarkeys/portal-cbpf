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
 * @author Olivier Buisard
 * 
 * field parameters
 * 
 * accept: allowed extensions - defaults to .gif,.jpg,.png
 * size
 * maxlength
 * class
 * disabled
 * onchange
 * showpreview: show the file preview - defaults to false
 * width: the preview max width - defaults to 200
 * height: the preview max width
 * showname: show the file name - defaults to false
 *
 */
class JFormFieldSYWImageFile extends JFormField
{
	public $type = 'SYWImageFile';
	
	protected $width;
	protected $height;
	protected $show_name;
	protected $show_preview;

	protected function getInput()
	{
		$html = '';
		
		$lang = JFactory::getLanguage();
		$lang->load('lib_syw.sys', JPATH_SITE);
		
		// Initialize some field attributes.
		$accept = $this->element['accept'] ? ' accept="' . (string) $this->element['accept'] . '"' : ' accept=".gif,.jpg,.png"';
		$size = $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$maxLength = $this->element['maxlength'] ? ' maxlength="' . (int) $this->element['maxlength'] . '"' : '';
		$class = $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$disabled = ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';

		// Initialize JavaScript field attributes.
		$onchange = $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';

		$html .= '<input type="file" name="' . $this->name . '" id="' . $this->id . '"' . ' value="'. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') .'"' . $accept . $disabled . $class . $size . $maxLength . $onchange . ' />';
		
		if ($this->show_preview) {
			
			$style = '';
			
			$style .= 'max-width: '.$this->width.'px;';
			
			if (!empty($this->height)) {
				$style .= 'max-height: '.$this->height.'px;';
			}
			
			$path = htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8');
			
			$html .= '<div class="image_preview" style="'.$style.' overflow: auto; border: 1px solid #ccc; border-radius: 3px; padding: 10px; margin-top: 5px; text-align: center">';
			
			if (!empty($path)) {				
				$html .= '<img src="'.JURI::root().$path.'" style="max-width: 100%">';
				if ($this->show_name) {
					$parts = explode('/', $path);
					$html .= '<br /><br /><span class="label">'.end($parts).'</span>';
				}
			} else {
				// no preview available
				$html .= '<span>'.JText::_('LIB_SYW_IMAGEPREVIEW_NOPREVIEW').'</span>';
			}
			
			$html .= '</div>';
		} else {
			if ($this->show_name) {
				$parts = explode('/', htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8'));
				$html .= '<br /><br /><input class="image_file" type="text" disabled="disabled" value="'.end($parts).'" />';
			}
		}
			
		return $html;
	}
	
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);
		
		if ($return) {
			$this->width = isset($this->element['width']) ? trim($this->element['width']) : '200';
			$this->height = isset($this->element['height']) ? trim($this->element['height']) : '';
			$this->show_name = isset($this->element['showname']) ? filter_var($this->element['showname'], FILTER_VALIDATE_BOOLEAN) : false;
			$this->show_preview = isset($this->element['showpreview']) ? filter_var($this->element['showpreview'], FILTER_VALIDATE_BOOLEAN) : false;
		}
		
		return $return;
	}
	
}
