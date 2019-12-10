<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die ;

jimport('joomla.form.formfield');
jimport('joomla.filesystem.folder');

/**
 * 
 * deprecated. Use k2message instead
 *
 */
class JFormFieldK2test extends JFormField 
{		
	public $type = 'K2test';

	protected function getLabel() 
	{		
		return '<div style="clear: both;"></div>';		
	}

	protected function getInput() 
	{		
		$lang = JFactory::getLanguage();
		$lang->load('lib_syw.sys', JPATH_SITE);
		
		$alertmessage = (trim($this->element['alertmessage']) === "false") ? FALSE : TRUE;
		
		$html = '';
					
		$folder = JPATH_ROOT.'/components/com_k2';
		if (!JFolder::exists($folder)) {
			/*
			$html .= '<div class="alert alert-error">';			
			$html .= '<span>';
			$html .= JText::_('LIB_SYW_K2TEST_MISSING');
			$html .= '</span>';
			$html .= '</div>';*/
		} else if ($alertmessage) {
			$html .= '<div class="alert alert-message">';			
			$html .= '<span>';
			$html .= JText::_('LIB_SYW_K2TEST_SELECTLAYOUT');
			$html .= '</span>';
			$html .= '</div>';
		}
		
		return $html;
	}

}
?>