<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die ;

jimport('joomla.form.formfield');

class JFormFieldOr extends JFormField 
{		
	public $type = 'Or';

	protected function getLabel() 
	{			
		return '';
	}

	protected function getInput() 
	{
		$html = '';
		
		$lang = JFactory::getLanguage();
		$lang->load('lib_syw.sys', JPATH_SITE);
		
		$html .= JText::_('LIB_SYW_OR_OR');
			
		return $html;
	}

}
?>