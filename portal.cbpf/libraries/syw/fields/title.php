<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

// no direct access
defined('_JEXEC') or die ;

jimport('joomla.form.formfield');

class JFormFieldTitle extends JFormField
{
	public $type = 'Title';
	
	protected $title;
	protected $image_src;
	protected $icon;
	protected $color;
	
	protected function getLabel()
	{
		return '';
	}

	protected function getInput()
	{
		$html = '';
		
		JHtml::_('script', 'syw/fields.js', false, true); 
		JHtml::_('stylesheet', 'syw/fields.css', false, true);

		$inline_style = array();
		
		$inline_style[] = 'background: '.$this->color.'; background: linear-gradient(to right, '.$this->color.' 0%, #fff 100%); ';
		$inline_style[] = 'color: #fff; ';
		//$inline_style[] = 'text-transform: uppercase; ';
		//$inline_style[] = 'letter-spacing: 3px; ';
		//$inline_style[] = 'font-family: "Courier New", Courier, monospace; ';
		//$inline_style[] = 'font-weight: bold; ';
		//$inline_style[] = 'margin: 15px 0; ';
		$inline_style[] = 'padding: 15px; ';
		//$inline_style[] = '-webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px; ';

		$html .= '<div class="syw_header syw_title" style=\''.implode($inline_style).'\'>';

		if ($this->image_src) {
			$html .= '<img style="margin-right: 6px; float: left; padding: 0; width: 16px; height: 16px" src="'.$this->image_src.'">';
		} else if ($this->icon) {
			JHtml::_('stylesheet', 'syw/fonts-min.css', false, true);
			$html .= '<i style="margin-right: 6px; font-size: inherit; vertical-align: baseline" class="SYWicon-'.$this->icon.'"></i>';
		}

		if ($this->title) {
			$html .= JText::_($this->title);
		}

		$html .= '</div>';

		return $html;
	}
	
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);
		
		if ($return) {
			$this->title = isset($this->element['title']) ? trim($this->element['title']) : '';
			$this->image_src = isset($this->element['imagesrc']) ? $this->element['imagesrc'] : ''; // ex: ../modules/mod_latestnews/images/icon.png (16x16)
			$this->icon = isset($this->element['icon']) ? $this->element['icon'] : ''; // ex: thumb-up
			$this->color = '#6f6f6f'; // isset($this->element['color']) ? $this->element['color'] : '#6f6f6f';
		}
		
		return $return;
	}

}
?>