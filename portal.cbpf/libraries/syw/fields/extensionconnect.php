<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die ;

jimport('joomla.form.formfield');

class JFormFieldExtensionConnect extends JFormField 
{		
	public $type = 'ExtensionConnect';
	
	protected function getLabel() 
	{		
		$lang = JFactory::getLanguage();
		$lang->load('lib_syw.sys', JPATH_SITE);
		
		$html = '';
		
		//$html .= '<div style="clear: both;">'.JText::_('LIB_SYW_EXTENSIONCONNECT_CONNECT_LABEL').'</div>';
		
		return $html;
	}

	protected function getInput() 
	{
		JHtml::_('stylesheet', 'syw/fonts-min.css', false, true);
		JHtml::_('bootstrap.tooltip');
		
		$html = '<div style="padding-top: 5px; overflow: inherit">';
				
		$html .= '<a class="label hasTooltip" style="background-color: #02b0e8; padding: 4px 8px; margin: 0 3px 0 0;" title="@simplifyyourweb" href="https://twitter.com/simplifyyourweb" target="_blank"><i class="SYWicon-twitter">&nbsp;</i>Twitter</a>';
		$html .= '<a class="label hasTooltip" style="background-color: #db4437; padding: 4px 8px; margin: 0 3px;" title="+Simplifyyourweb" href="https://plus.google.com/+Simplifyyourweb" target="_blank"><i class="SYWicon-google">&nbsp;</i>Google+</a>';
		$html .= '<a class="label hasTooltip" style="background-color: #43609c; padding: 4px 8px; margin: 0 3px;" title="simplifyyourweb" href="https://www.facebook.com/simplifyyourweb" target="_blank"><i class="SYWicon-facebook">&nbsp;</i>Facebook</a>';
		$html .= '<a class="label" style="background-color: #ff8f00; padding: 4px 8px; margin: 0 3px;" href="https://simplifyyourweb.com/latest-news?format=feed&amp;type=rss" target="_blank"><i class="SYWicon-rss">&nbsp;</i>News feed</a>';
		
		$html .= '</div>';
		
		return $html;
	}

}
?>
