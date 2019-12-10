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
 * path: the image path (relative or full)
 * width: the preview max width - defaults to 200
 * height: the preview max width
 * showname: show the file name - defaults to false 
 *
 */
class JFormFieldSYWImagePreview extends JFormField 
{		
	public $type = 'SYWImagePreview';
	
	protected $path;
	protected $relative_path;
	protected $width;
	protected $height;
	protected $show_name;

	protected function getInput() 
	{
		$html = '';
		
		$lang = JFactory::getLanguage();
		$lang->load('lib_syw.sys', JPATH_SITE);
		
		$style = '';
		
		$style .= 'max-width: '.$this->width.'px;';
		
		if ($this->height) {
			$style .= 'max-height: '.$this->height.'px;';
		}
		
		$html .= '<div class="image_preview" style="'.$style.' overflow: auto; border: 1px solid #ccc; border-radius: 3px; padding: 10px; text-align: center">';
		
		if ($this->path) {
			
			if (!$this->relative_path) {
				$this->path = JURI::root().$this->path;
			}
			
			$html .= '<img src="'.$this->path.'" style="max-width: 100%">';
			if ($this->show_name) {
				$parts = explode('/', $this->path);
				$html .= '<br /><br /><span class="label">'.end($parts).'</span>';
			}
		} else {
			// no preview available
			$html .= '<span>'.JText::_('LIB_SYW_IMAGEPREVIEW_NOPREVIEW').'</span>';
		}
		
		$html .= '</div>';
			
		return $html;
	}
	
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);
		
		if ($return) {
			$this->path = isset($this->element['path']) ? trim($this->element['path']) : '';
			$this->relative_path = isset($this->element['relativepath']) ? filter_var($this->element['relativepath'], FILTER_VALIDATE_BOOLEAN) : true;
			$this->width = isset($this->element['width']) ? trim($this->element['width']) : '200';
			$this->height = isset($this->element['height']) ? trim($this->element['height']) : '';
			$this->show_name = isset($this->element['showname']) ? filter_var($this->element['showname'], FILTER_VALIDATE_BOOLEAN) : false;
		}
		
		return $return;
	}

}
?>