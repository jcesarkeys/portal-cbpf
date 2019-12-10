<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die ;

jimport('joomla.form.formfield');
jimport('joomla.plugin.helper');

class JFormFieldPhpExtensionTest extends JFormField 
{		
	public $type = 'PhpExtensionTest';
	
	protected $extension;

	protected function getLabel() 
	{	
		return '';
	}

	protected function getInput() 
	{		
		$lang = JFactory::getLanguage();
		$lang->load('lib_syw.sys', JPATH_SITE);
				
		$html = '';
		
		$extensions = get_loaded_extensions();
		
		if (!in_array($this->extension, $extensions)) {
			$html .= '<div style="margin-bottom:0" class="alert alert-error">';			
				$html .= '<span>'.JText::sprintf('LIB_SYW_PHPEXTENSION_NOTINSTALLED', $this->extension).'</span>';
			$html .= '</div>';
		} else {
			$html .= '<div style="margin-bottom:0" class="alert alert-success">';
				$html .= '<span>'.JText::sprintf('LIB_SYW_PHPEXTENSION_INSTALLED', $this->extension).'</span>';
			$html .= '</div>';
		}
		
		return $html;
	}
	
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);
		
		if ($return) {
			$this->extension = isset($this->element['extension']) ? trim($this->element['extension']) : '';
		}
		
		return $return;
	}

}
?>
