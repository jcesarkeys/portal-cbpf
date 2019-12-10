<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die ;

jimport('joomla.form.formfield');
jimport('joomla.plugin.helper');

/**
 *
 * deprecated. Use phpextensiontest instead
 *
 */
class JFormFieldIntltest extends JFormField 
{		
	public $type = 'Intltest';

	protected function getLabel() 
	{		
		return '<div style="clear: both;"></div>';
	}

	protected function getInput() 
	{		
		$lang = JFactory::getLanguage();
		$lang->load('lib_syw.sys', JPATH_SITE);
		
		$extensions = get_loaded_extensions();
		
		$html = '';
		
		if( !in_array( 'intl', $extensions ) ) {
			$html .= '<div class="alert alert-error">';			
			$html .= '<span>';
			$html .= JText::_('LIB_SYW_INTLTEST_NOTLOADED');
			$html .= '</span>';
			$html .= '</div>';
				
			return $html;
		} else {
			$html .= '<div class="alert alert-success">';			
			$html .= '<span>';
			$html .= JText::_('LIB_SYW_INTLTEST_LOADED');
			$html .= '</span>';
			$html .= '</div>';
		}
		
		return $html;
	}

}
?>
