<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die ;

jimport('joomla.form.formfield');

class JFormFieldRadioWrapper extends JFormFieldRadio 
{	
	public $type = 'RadioWrapper';
	
	protected function hide_it() 
	{		
		$version = new JVersion();
		$jversion = explode('.', $version->getShortVersion());
		
		$min_version_param = $this->element['versionmin'] ? $this->element['versionmin'] : 2; // versions to show the field in
		$max_version_param = $this->element['versionmax'] ? $this->element['versionmax'] : 3;
				
		if (intval($jversion[0]) >= $min_version_param && intval($jversion[0]) <= $max_version_param) {
			return false;
		}
		
		return true;
	}
	
	public function getLabel() 
	{			
		if (!self::hide_it()) {
			return parent::getLabel();
		}
			
		return '';
	}
	
	public function getInput() 
	{
		if (!self::hide_it()) {
			return parent::getInput();
		}
		
		return '';
	}
	
	public function getControlGroup() 
	{
		if (!self::hide_it()) {
			return parent::getControlGroup();
		}
		
		return '';
	}
	
	public function renderField($options = array())
	{
		if (!self::hide_it()) {
			return parent::renderField();
		}
		
		return '';
	}
	
}
?>